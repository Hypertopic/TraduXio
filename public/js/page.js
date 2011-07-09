if (typeof console == "undefined") console={log:function(){}};
 
var trId = '';
var workId;
var begin = 0;// segment from which to begin the text's display - it can represent the first or the last segment (page-turn backward or forward)
var end = 0;
var back = false;
var ajaxData;
var translations=[];
var nextHiddenId=null;    
var id_str = document.location.pathname.match(/\/id\/\d+/);
var minHeight = 400;
var maxH;
var docHeight;
var blocked=false;
var state = 'reset';//'reset' 'editable'
var trBlocks;
var user;
var temp;
var lastPage;
var sentenceToTag;

(function($) {
    
    $.fn.addText = function(x0,x1,pre,post,sentences) {
        for(var x=0; x<=x1; x++){               
            $(this).append(pre + x +"'>" + sentences[x].content + post);
        }
    }
    
    $.fn.replaceTag = function(oldTagId,newTagName){
		var w = $(this).width();
		var h = $(this).height();
		var cl = $(this).attr('class');
		var id = $(this).attr('id');
		var txt = (newTagName=='span')?nl2br($(this).val(),false):(trWork.blocks[id.match(/\d+/)].translation?trWork.blocks[id.match(/\d+/)].translation:'');
		$(this).replaceWith( "<"+ newTagName +" class=\"" + cl + "\" name=\""+ id+ "\" id=\""+ id +"\" >" + txt + "</"+ newTagName +">");
		$("#"+id).height(h);
		$("#"+id).width(w);
		$("#"+id).focus();
	}
    $.fn.resetToDefault = function(defaultValue){
		$(this).text(defaultValue);
	}
    
    $.getDocHeight = function(){
      //  alert('1 getdocheight');
    return Math.max(
        $(document).height(),
        $(window).height(),
        /* For opera: */
        document.documentElement.clientHeight
        );
    };
    
    
    tdxio.array = {
        trShift : function(trArray,id,selected){
            var index = 0;
            var L = trArray.length;
            
            var newArray = [];
            var firstItem = [];
            
            for(var i = 0; i<L; i++){
                if(trArray[i].work.id==id){
                    index = i;
                    break;
                }
            }
            newArray = trArray.splice(index,L);     
            if(selected==false){
                firstItem = trArray.splice(0,1);
            }             
            newArray = firstItem.concat(newArray,trArray);                
            return newArray;
        }
    }
	
	
	tdxio.textSearch = {
          getConcord : function() {
            if (typeof tdxio.textSearch.concord!='undefined') {
                tdxio.textSearch.concord.close();
            }
            tdxio.textSearch.concord = window.open("","concord") ;
        }
    }; 
    
    tdxio.page = {

		getBlocked : function(){return blocked;},
		setBlocked : function(val){blocked = val;},
		
        resize: function(){//alert('4');
            // 1. Make the height of the translation and of the work texts the same            
            var maxtemp = Math.max(maxH,$('#translation div.text').height(),$('#work div.text').height());
            $('#translation div.text').height(maxtemp);
            $('#work div.text').height(maxtemp);
            
            //2. Then (div#work and div#translation should have the same height)...
            if($('div#work').height()!=$('div#translation').height()){
               // alert('Change the code! #work and #translation have different heights');
            }            
            // ... change the borders' height
            $('div.Rborder, div.Lborder').height($('div#work').height());
        
        },
        
        turn: function(dir,index){//alert('3');
            begin = parseInt(index);
            end=parseInt(index);
         //   alert(begin +' '+(dir=='prev-page'));
            tdxio.page.displayWork(ajaxData,trId,(dir=='prev-page'),begin,end);
           // tdxio.page.setState($('#editbtn').attr('class')=='on'?'editable':'reset');
            tdxio.page.setState(window.state);
            tdxio.page.adjust();
            tdxio.page.resize();
        },
        writeWork: function(sentences,from,to, step){//alert('2');
            var pre = "<span class='segment' id='text"+data.work.id +"-segment";
            for(var x=from; x<=to; x+=step){
                $('#work div.text').append(pre + x +"'>" + nl2br(sentences[x].content,false) +"</span>");
            }
        },
        resetHeight : function(){
			//alert('1');
            $('div#test').height("");
            $('#translation div.text').height("");
            $('#work div.text').height("");
        },
        adjust : function(){
			//alert('adjust');
            if($("#editbtn").attr('class')=='on'){
                $('.block').toggleClass('show',true);
                                 
                $('#translation div.text .block').each(function(index,el){
                    var oid = 'o'+el.id;
                    if($(el).height()>$('#'+oid).height()){
                        $('#'+oid).height($(el).height());
                    }else{
                        $(el).height($('#'+oid).height());
                    }
                    $(el).offsetParent($('#'+oid).offsetParent());
                });
                
            }
        },
        
        redirect: function(address){
			var referer = document.location.href.replace(tdxio.baseUrl,"");
			//alert(referer);
			address = address+"?referer="+referer;
			window.location.replace(address);
		},
        
        replaceTag: function(oldTagId,newTagName){
			$(oldTagId).each(function(index,el){
				var w = $(this).width();
				var h = $(this).height();
				var cl = $(this).attr('class');
				var id = this.id;
				var txt = (newTagName=='span')?nl2br($(this).val(),false):trWork.blocks[id.match(/\d+/)].translation;
				$(this).replaceWith( "<"+ newTagName +" class=\"" + cl + "\" name=\""+ el.id+ "\" id=\""+ el.id +"\" >" + txt + "</"+ newTagName +">");
/*				if($("#edit-form")){
					alert('exists form');
					$("#edit-form").append("#"+el.id);
				}*/
				$("#"+el.id).height(h);
				$("#"+el.id).width(w);
			});
			//window.newBlocks = $.makeArray($("#translation div.text .block.show.editable"));
		},
        
        
        setState : function(mode){
            if(mode=='reset'){
				tdxio.page.replaceTag("textarea.block.show.editable","span");
                tdxio.page.setBlocked(false);
                $('.text').toggleClass('show',true);
                $('.block').toggleClass('show',false);
                $(".segment").toggleClass('highlighted',false);
                $(".segment").toggleClass('selected',false);
                $(".sentence-tag").remove();
                //$('.author').toggleClass('editable',false);
                //$('.title').toggleClass('editable',false); 
                $('.onglet.first .translator-name').toggleClass('editable',false);
                $('#tr-author').toggleClass('editable',false);
                $('#tr-title').toggleClass('editable',false); 
                //$("span.block.show.editable textarea").each(function(){$(this).replaceWith($(this).text());});
                //$("#translation span textarea").contents().unwrap();
               
				//$("#edit-form").replaceWith($("#edit-form").html());
				$("#editbtn").toggleClass('on',false);
                
                $('#translation .block').toggleClass('editable',false);
                $('#tr-icons div').toggleClass('on',false);
                $('.cut').remove();
                $('.merge').remove();
            }else if(mode=='editable'){
                tdxio.page.setBlocked(true);
                tdxio.page.replaceTag("textarea.block.show.editable","span");
                $('.text').toggleClass('show',false);
                $('.block').toggleClass('show',true);
                $('#tr-author').toggleClass('editable',true);
                $('#tr-title').toggleClass('editable',true);                
                $('.onglet.first .translator-name').toggleClass('editable',true);                
                $('#translation .block').toggleClass('editable',true);
                $('#editbtn').toggleClass('on',true);
                $('#work span.segment').after('<span class="cut" title="'+tdxio.i18n.cuthere+'"></span>');
                $('#work span.block').after('<span class="merge" title="'+tdxio.i18n.mergehere+'"></span>');
                $('#work span.block.show span.cut:last-child').remove();
                if(window.lastPage)	
					$('#work div.text span.merge:last-child').remove();			    
            }    
			window.state = mode;
        },
        
        updateLink: function(privileges,twId){
			if(twId!=null && twId!=''){
				$("#edit,#editbtn").toggleClass('idle',!privileges.edit);			
				$("#manage,#managebtn").toggleClass('idle',!privileges.manage);
				$("#delete,#tr-icons .delbtn").toggleClass('idle',!privileges.del);
				$("#history,#historybtn").toggleClass('idle',false);
				$("#print,#printbtn").toggleClass('idle',false)
				$("#history a,#historybtn a").attr("href",tdxio.baseUrl+"/work/history/id/"+twId);
				$("#manage a,#managebtn a").attr("href",tdxio.baseUrl+"/work/manage/id/"+twId);
				$("#print a,#printbtn a").attr("href",tdxio.baseUrl+"/translation/print/id/"+twId);
			}			
		},
		
        
        displayWork: function(data,trId,backward,beginSeg,endSeg){
            if(data.work.Sentences.length > 0){
				
                tdxio.page.resetHeight();
                $('#work div.text').empty();
                $('#test').empty();     
                $('#translation div.text').empty();     
                $('#test').width($('#work div.text').width());
                
                var sentences = data.work.Sentences;
                //var maxH = Math.max(minHeight,$(window).height()-$('#header').outerHeight()-$('.footer').outerHeight()-$('#under-header').outerHeight()-($('.tag-line').outerHeight()+15)-$('.work-title').outerHeight()-$('.Tborder').height()-$('.Bborder').height()-$('#work div.text').outerHeight() +$('#work div.text').height()-60);
                //var stdTextHeight = $.getDocHeight()-$('#header').outerHeight()-$('.footer').outerHeight()-$('#under-header').outerHeight()-($('.tag-line').outerHeight()+15)-$('.work-title').outerHeight()-$('.Tborder').height()-$('.Bborder').height()-60;
                var stdTextHeight = docHeight-$('#header').outerHeight(true)-$('.footer').outerHeight(true)-$('#under-header').outerHeight(true)-($('.tag-line').outerHeight(true)+15)-$('.Tborder').height()-$('.work-title').outerHeight(true)-($('#work div.text').outerHeight(true) -$('#work div.text').height())-$('.Bborder').height()-60-30;
                //alert(stdTextHeight);   
                maxH = Math.max(minHeight,stdTextHeight);

                var pre = "<span class='segment' id='text"+data.work.id +"-segment";  
                var len = sentences.length;
                //if(data.work.Interpretations.length == 0){// there are no translations
                if(trId == '' || trId==null){// there are no translations
                    //display only the work
                  
                    $('#translation .text').append("<span id='create'>"+tdxio.i18n.createtrsl+"</span>"); 
					$("#top-border").show(10);
					$("#plus").hide(10);
					$("#tr-author,#tr-title").empty();
					$("#comma").remove();
                    if(backward===false){ 
                        var i;            
                        $('#test').append(pre + sentences[beginSeg].number + "'>" + nl2br(sentences[beginSeg].content,false) + "</span>");                    
                        for(i=beginSeg; (i==beginSeg) || (i<len && $('#test').height()<= maxH) ; i++){
							$('#work div.text').append(pre + sentences[i].number + "'>" +nl2br(sentences[i].content,false) + "</span>");
							if(i+1<len){
                                $('#test').append(pre + sentences[i+1].number + "'>" +nl2br(sentences[i+1].content,false) + "</span>");
                            }
                            endSeg=i;
                        }                       
                    }else{
                        var i;
                        $('#test').append(pre + sentences[endSeg].number + "'>" + nl2br(sentences[endSeg].content,false) + "</span>");
                        for(i=endSeg; (i==endSeg)|| (i>=0 && $('#test').height()<= maxH) ; i--){
							$('#work div.text').prepend(pre + sentences[i].number + "'>" +sentences[i].content + "</span>");
                            if(i-1>=0){
                                $('#test').prepend(pre + sentences[i-1].number + "'>" +sentences[i-1].content + "</span>");
                            }                            
                        }
                        beginSeg=i+1;
                        while((endSeg+1<len)&&($('#test').height()<= maxH)){
                            $('#test').append(pre + sentences[endSeg+1].number + "'>" +nl2br(sentences[endSeg+1].content,false) + "</span>");
                            if($('#test').height()<= maxH){
                                endSeg++;
                                $('#work div.text').append(pre + sentences[endSeg].number + "'>" +nl2br(sentences[endSeg].content,false) + "</span>");                                
							}
                        }
                        back=true;
                    }      
                }else{
                    trWork = data.work.Interpretations[0];
                    
                    for(var j=0;j<data.work.Interpretations.length; j++){
                        if(data.work.Interpretations[j].work.id==trId)
                            trWork = data.work.Interpretations[j];
                    }
                    $("input[name=destlang]").val(trWork.work.language);
                    $('#translation .work-title span.author').html((trWork.work.author!=null)?trWork.work.author:tdxio.i18n.anonymous);
					if(!$("#comma").length) $('#translation .work-title span.title').before('<span id="comma">,&nbsp</span>');
                    $('#translation .work-title span.title').html((trWork.work.title!=null)?trWork.work.title:tdxio.i18n.notitle);
                    $('div#translation').attr('dir',(trWork.work.rtl==1)?'rtl':'');
                    $('#tr-tag').attr('dir',(trWork.work.rtl==1)?'rtl':'');
                    var trlen = trWork.blocks.length;
               //     alert('trlen: '+trlen);
                    var preblock = "<span class='block' id='block";
                    var outblock = "<span class='block' id='oblock";
                    
                    var beginBlock=0;
                    var endBlock=0;
                    for(var y=0;y<trlen;y++){
                        if((beginSeg>=trWork.blocks[y].from_segment) && (beginSeg<=trWork.blocks[y].to_segment)){
                            beginBlock=y;
                        }
                        if((endSeg>=trWork.blocks[y].from_segment) && (endSeg<=trWork.blocks[y].to_segment)){
                            endBlock=y;
                            break;
                        }
                    }
                    if(backward===false){  
                        var i;
                        var text;
                        beginSeg = trWork.blocks[beginBlock].from_segment;
                        
                        for(i=beginBlock;(i==beginBlock) || ( i<trlen && $('#test').height()<= maxH) ; i++){
							$('#translation div.text').append(preblock + i + "'>" +nl2br((trWork.blocks[i].translation)?trWork.blocks[i].translation:'',false) + "</span>");
                            
                            for(var x=trWork.blocks[i].from_segment,text=''; x<=trWork.blocks[i].to_segment; x++){
                                text += pre + x +"'>" + nl2br(sentences[x].content,false) +"</span>"; 
							}
                            $('#work div.text').append(outblock + i + "'>"+text+"</span>");
                            $('#test').html($('#work div.text').html());
                            if(i+1<trlen){
                                for(var x=trWork.blocks[i+1].from_segment; x<=trWork.blocks[i+1].to_segment; x++){
                                    $('#test').append(pre + x +"'>" + nl2br(sentences[x].content,false) +"</span>");
                                }
                            }
                            endSeg=trWork.blocks[i].to_segment;
                        }
                        back=false;
                    }else{
                        var i;
                        var text;
                        endSeg = trWork.blocks[endBlock].to_segment;
                        
                        for(i=endBlock; (i==endBlock) || (i>=0 && $('#test').height()<= maxH) ; i--){
                            $('#translation div.text').prepend(preblock + i + "'>" +nl2br((trWork.blocks[i].translation)?trWork.blocks[i].translation:'',false) + "</span>");
                             for(var x=trWork.blocks[i].from_segment,text=''; x<=trWork.blocks[i].to_segment; x++){
								text += pre + x +"'>" + sentences[x].content + "</span>";                          
                            }
                            $('#work div.text').prepend(outblock + i + "'>"+text+"</span>");
                            $('#test').html($('#work div.text').html());
                            if(i-1>=0){
                                for(var x=trWork.blocks[i-1].to_segment; x>=trWork.blocks[i-1].from_segment; x--){
                                    $('#test').prepend(pre + x +"'>" + nl2br(sentences[x].content,false) +"</span>");
                                }
                            }
                        }
                        $('#test').html($('#work div.text').html());
                        beginSeg=trWork.blocks[i+1].from_segment;
                        while((endBlock+1<trlen)&&($('#test').height()<= maxH)){
                            var from=trWork.blocks[endBlock+1].from_segment;
                            var to=trWork.blocks[endBlock+1].to_segment;
                            for(var x=from; x<=to; x++){
                                $('#test').append(pre + x +"'>" + nl2br(sentences[x].content,false) +"</span>");
                            }
                            if($('#test').height()<= maxH){
                                $('#translation div.text').append(preblock + (endBlock+1) + "'>" +nl2br((trWork.blocks[endBlock+1].translation)?trWork.blocks[endBlock+1].translation:'',false) + "</span>");
                                for(var x=from,text=''; x<=to; x++){
									text += pre + x +"'>" + nl2br(sentences[x].content,false) + "</span>";
								}
                                $('#work div.text').append(outblock + (endBlock+1) + "'>"+text+"</span>");
                                //window.trBlocks.(endBlock+1)=outblock + (endBlock+1) + "'>"+text+"</span>");
                                endSeg=to;
                            }
                            endBlock++;                            
                        }                        
                        back=true;
                    }
                    $('#prev-page a').attr('href','#tr'+trId);
                    $('#next-page a').attr('href','#tr'+trId);
                }
                //alert('begin: '+begin+', end: '+end );
                $('#next-page img').attr('id','goto-'+((endSeg+1<len)?endSeg+1:0));
                $('#next-page img').css('visibility',(endSeg+1<len)?'visible':'hidden');
                $('#prev-page img').attr('id','goto-'+((beginSeg>0)?beginSeg-1:0));                           
                $('#prev-page img').css('visibility',(beginSeg>0)?'visible':'hidden');  
                lastPage=!(endSeg+1<len);
            }
            $('#test').empty();
            begin = beginSeg;
            end = endSeg;
            
            tdxio.page.addNotes(beginSeg,endSeg);
        },
        
        addNote: function(segNumber,noteNumber,note){
			//noteNumber starts at 0, but notes' numeration starts at 1 so the visualised noteNumber is always incremented of 1
			var index = parseInt(noteNumber)+parseInt(1);
			var prec = parseInt(noteNumber)-parseInt(1);
			if(noteNumber>0){
				$("span#segment"+segNumber+"-note"+(prec)).after("<span class='note-symbol' id='segment"+segNumber+"-note"+noteNumber+"' title='"+note.comment+"'>"+index+"&nbsp;</span>");
			}else{
				$("span#text"+window.workId+"-segment"+segNumber).prepend("<span class='note-symbol' id='segment"+segNumber+"-note"+noteNumber+"' title='"+note.comment+"'>"+index+"&nbsp;</span>");
				ajaxData.work.SentencesTags[segNumber]=[];			
			}
			ajaxData.work.SentencesTags[segNumber][noteNumber]=note;
		},
		 
		removeNote: function(segNumber,noteNumber){
			var L = ajaxData.work.SentencesTags[segNumber];
			ajaxData.work.SentencesTags[segNumber].splice(noteNumber,1);
			//for(var i in ajaxData.work.SentencesTags[segNumber]
			//delete ajaxData.work.SentencesTags[segNumber][noteNumber]=note;
			$('#text'+window.workId+'-segment'+segNumber+' .note-symbol').remove();
			tdxio.page.addNotes(segNumber,segNumber);
		},
        
        addNotes: function(from,to){
			for(var segNumber in ajaxData.work.SentencesTags){
				if(segNumber >= from && segNumber<=to){
					var tags = ajaxData.work.SentencesTags[segNumber];
					var tagstext = '';
					for(var i in tags){
						var j = parseInt(i)+1;
						tagstext += "<span class='note-symbol' id='segment"+segNumber+"-note"+i+"' title='"+tags[i].comment+"'>"+ j +"&nbsp</span>";
					}
					$("span#text"+window.workId+"-segment"+segNumber).prepend(tagstext);
				}
			}
		},
        
        displayOnglets: function(trls){
            var N = trls.length;
            var lineWidth = $("#right-page").width()-30;
  //          $('span#more').css('visibility','hidden');
            $('.onglets li').css('z-index',-100);
            $('.onglets li').css('visibility','hidden'); 
            $("a.translator, .prec-onglet, .next-onglet").css('display','');
            var totWidth = 0;
            var overlap = 0;
            var ongClass='onglet first';
            var i,id;            
            $('li#onglet-'+trls[0].work.id).find('.translator').css('display','inline');            
            if(N>1)
				$('li#onglet-'+trls[0].work.id).find('.prec-onglet,.next-onglet').css('display','inline');
				
            for(i = 0;i==0 || (i<N && (totWidth<lineWidth));i++){
            //   alert('tot: '+totWidth +', lineW: '+lineWidth);
                id = trls[i].work.id;
                $('li#onglet-'+id).css('z-index',N-i).css('left',totWidth-overlap).attr('class',ongClass).css('visibility','visible');				                
                totWidth += $('li#onglet-'+id).outerWidth()-overlap;
                overlap = 15;
                ongClass='onglet';
            }
				
            //$(".onglet.first a.translator,.onglet.first .prec-onglet,.onglet.first .next-onglet").css('display','inline');
            if(totWidth>lineWidth){
                $('li#onglet-'+id).css('visibility','hidden');
                i--;
            }//alert(totWidth+' <-tot : line->'+lineWidth);
            if(i<N){
                nextHiddenId = trls[i].work.id;
           //     $('span#more').css('visibility','visible');
               // $('span#more').wrap('<a href="#tr'+trId+'" />');                
            }
        },
    
        translate: function(){
                
        },      
        
          
        getWork: function(){
			var url = tdxio.baseUrl+"/work/ajaxread"+id_str;
 			var hash = document.location.hash.substr(1);
 			var qtity=50;
 			var params;                
 			docHeight = $.getDocHeight();
 			if((hash==null)||(hash=='')||(hash==false)){
 				params={'qtity':qtity};
 			}else if(hash.match(/tr\d+/gi)!=null){
 				window.trId = hash.match(/tr\d+/gi)[0].substr(2);
 				beginHash = hash.match(/#beg\d+/gi);
 				if((beginHash!=null)&&(beginHash!='')){begin = beginHash[0].substr(4);}
 				params={'qtity':qtity,'trId':trId};
 			}
 						  
 			$.ajax({
 				type:"get",
 				url:encodeURI(url),
 				dataType: "json",
 				async: false,
 				data: params,
 				success: function(rdata){
					if (rdata.response==false) {
						if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}                        
						else alert(rdata.message.code);
					}else{
						window.trId = rdata.trId;
						document.location.hash = (rdata.trId!=null)?'tr'+rdata.trId : '';
						if(rdata.work.Interpretations.length>0){	
							translations = tdxio.array.trShift(rdata.work.Interpretations.slice(),rdata.trId,true);
							tdxio.page.displayOnglets(translations);
							if($("#tr-tag .add-tag").length==0)
								$("#tr-tag").append(rdata.tagbody);							
							$("input[name=srclang]").val(rdata.work.language);
						}						
						window.ajaxData = rdata;
						tdxio.page.displayWork(rdata,rdata.trId,false,begin,end);
						tdxio.page.resize();
						tdxio.page.updateLink(rdata.trPriv,rdata.trId);
					}
 				},
 				error: function() {
 					alert("error reading the work");
 				}
 			}); 
 		},
		
		gotoTransl: function(newId){
			if(newId!='' && newId!=null){
				document.location.hash="#tr"+newId;
				trId = newId;
				translations = tdxio.array.trShift(ajaxData.work.Interpretations.slice(),newId,true);
			   /*tdxio.page.displayOnglets(translations);    */
				tdxio.page.displayOnglets(translations);
				tdxio.page.displayWork(ajaxData,newId,back,begin,end);
				$.ajax({
					type:"get",
					url:encodeURI(tdxio.baseUrl+"/tag/gettags"),
					dataType: "json",
					data:{'id':newId},
					success: function(rdata,status){
						if (rdata.response==false) {
							if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}                        
							else alert(rdata.message.code);
						}else{
							$("div#tr-tag").empty().append(rdata.taglist);
						}
					},
					error: function() {
						alert("error getting the tags' list");
					}
				});  
			   // tdxio.page.setState($('#editbtn').attr('class')=='on'?'editable':'reset');
				tdxio.page.setState(window.state);
				tdxio.page.adjust();
				tdxio.page.resize();
			}else{
				document.location.hash="";		
				tdxio.page.displayWork(ajaxData,'',back,begin,end);
				tdxio.page.setState('reset');
				tdxio.page.adjust();
				tdxio.page.resize();
			}
		}
            
    }; 
      
        
    var tout = false;
    var otime = new Date(10, 10, 2010, 10,10,10);
    var dt = 100; 
    
    function resizeDT() {
        if (new Date() - otime < dt) {
            setTimeout(resizeDT, dt);
        } else {
            tout = false;
            tdxio.page.displayWork(ajaxData,trId,back,begin,end);//refresh arrows
            tdxio.page.resize();
            if(translations.length)tdxio.page.displayOnglets(translations);
        }                
    };
    
    $(document).ready(function() {
        var url;
        var action;
        var idstr = document.location.pathname.match(/\/id\/\d+/);
        workId = parseInt(idstr[0].match(/\d+/));
        
        tdxio.page.getWork();
       
  
     //   var blocks = $("#translation div.text span").map(function(){return this.id;}).get().join(',');
      //  alert('ids '+blocks);
        $(window).bind('resize',(function() {
            if(tdxio.page.getBlocked()!=true){
                otime = new Date();
                if (tout === false) {
                    tout = true;
                    setTimeout(resizeDT, dt);
                }
            }
        }));
        
        $(".turn-page").bind("click",function() {
            tdxio.page.turn($(this).parents('div').attr('id'),this.id.split('-')[1]);
        });
       
        $('#text').empty();
        
        $('ul.onglets li').live('click',function(e){
			if(!$(this).hasClass('first') && e.target.className!='prec-onglet' && e.target.className!='next-onglet'){
				tdxio.page.setBlocked(false);
				tdxio.page.gotoTransl(this.id.split("-")[1]);
			}
        });
        
        $('img.prec-onglet,img.next-onglet').live('click',function(event){
			/*if(nextHiddenId!=null)
                translations = tdxio.array.trShift(translations,nextHiddenId,false);
            tdxio.page.displayOnglets(translations);*/
            var index = (event.target.className.split('-')[0]=='prec')?translations.length-1:1;
            var trid = translations[index].work.id;
            tdxio.page.gotoTransl(trid);
        });
        
		$('span#more, li#translate,span#create').live('click',function(event){
			event.preventDefault();
			if( $("div#new-translation").css('visibility')=='hidden'){
				window.$.getForm('translate',window.workId);
			}
			return false;
		});
		
		
        url=encodeURI(tdxio.baseUrl+"/translation/save");
   
		
		$('.work-title input,input.translator-name').live('focusout change',function(e){
			var id = $(this).attr('id');
			var textId = ($(this).parent('div').parent('div.text-container').attr('id')=='work')?window.workId:window.trId;
			var value = $(this).val();
			var elName = $(this).attr('class');
			elName = (elName.match('author'))?'author':(elName.match('title')?'title':'translator');
			value = (value!='')?value:((elName.match('title'))?tdxio.i18n.notitle:tdxio.i18n.anonymous);
			if(e.originalEvent.type == "change"){
				window.$.saveMeta(textId,elName,value);
		/*		$.ajax({
					type: "post",
					url: encodeURI(tdxio.baseUrl+"/work/metaedit/id/"+textId),
					dataType: "json",
					data: params,
					success:function(rdata,status){
						if (rdata.response==false) {//error somewhere
							$("#"+e.target.id).resetToDefault(e.target.defaultValue);
							if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}                        
							else alert(rdata.message.code);
						}else {
							//alert('success');
							$.update('update-'+id.split('-')[0],{'el':elName.split(' ')[0],'val':value});
						}
					},
					error:function() {
						alert("Error in the saving process");
					},
				});
				* */
			}//save modified filed	
			
			if(!$(this).hasClass('translator-name')) $(this).replaceWith("<span class=\""+$(this).attr('class')+"\" id=\""+$(this).attr('id')+"\">"+value+"</span>");			
			else $(this).replaceWith("<a href='#"+window.trId+" class=\""+$(this).attr('class')+"\" >"+value+"</span>");			
		});
		
		
		$(".extbtn , li#extend").live('click',function(event){
		//$("ul li#extend a").live('click',function(event){
			event.preventDefault();
			action="extend";
			window.$.getForm(action);
			return false;
		});
		
		$("form#extend-form").live("submit",function(event) {
           event.preventDefault();
			window.$.submitForm($(this),"ajaxextend",{'id':workId});
           
            //alert('after ajax'); 
            //
            return false; 
        });
		$("#edit-meta").click(function(){$("#editmeta").click();});        
		$("#editmeta").click(function(){	
			var can = window.$.checkRights('edit',window.workId);
			if(can){
				if($(this).attr('class')=='on'){
					$('#orig-author,#orig-title').trigger('focusout');				
					$('#orig-author,#orig-title').toggleClass('editable',false);
				}else{
					$('#orig-author,#orig-title').toggleClass('editable',true);
				}
				$(this).toggleClass('on');
			}
		});
	    $("#editbtn, #edit").click(function(e){
			var can = false;
			if(window.trId!=null &&window.trId!='')
				can = window.$.checkRights('edit',window.trId);
			if(can){
				e.preventDefault();
			//    $('#tr-icons div').toggleClass('on');
			   var active = $(this).attr('class')=='on';
			 //   $('.text').toggleClass('show');
			 //   $('.block').toggleClass('show');
				switch(window.state){
					case 'reset': tdxio.page.setState('editable');break;
					case 'editable': tdxio.page.setState('reset');break;
				}
				//tdxio.page.setState(active?'editable':'reset');
				tdxio.page.setBlocked(!active);
				tdxio.page.resetHeight();
				tdxio.page.adjust();
				tdxio.page.resize();
				//if(active){$('form#modified-text').}else{}
			}
        });
           
        $(".merge , .cut").live('click',function(){
            var op = $(this).attr('class');
          //  alert(op);
            var segId = (op=='merge')?$(this).prev('span.block').children()[$(this).prev('span.block').children().length-1].id:$(this).prev('span.segment').attr('id'); 
            var after = segId.split('segment')[1];
            $.ajax({
                type:"get",
                url:encodeURI(tdxio.baseUrl+"/translation/ajax"+op),
                data: {'id':window.trId,'after':after},
                dataType: "json",
                success: function(rdata,status){
                    if (rdata.response==false) {
                        if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
                        else alert(rdata.message.code);
                    }else{
                        window.$.update(op,rdata);
                    }
                },
                error: function() {
                    alert("Error merging or cutting");
                }
            });
        });
                
        /*
		$('textarea.block.show.editable').live('change',function(){
			$('#savebtn').toggleClass('on',true);
		});
		
		$("#savebtn.on").live('click',function(){
			//$(this).append("#translation textarea");
			window.$.submitTranslation();});
		*/
		
		$('#translation div.text span.block.show.editable').live('click',function(){
			if(window.state=='editable')
				$(this).replaceTag("span.block.show.editable","textarea");			
		});
		$('#translation div.text textarea.block.show.editable').live('change focusout',function(e){
			if(window.state=='editable'){
				if(e.originalEvent.type == "change"){window.$.submitTranslation($(this).attr('id'));}
				$(this).replaceTag("textarea.block.show.editable","span");
				e.preventDefault();
			}
		});
		/*
		$('#translation div.text textarea.block.show.editable').live('focusout',function(e){
			if(window.state=='editable'){
				$(this).replaceTag("textarea.block.show.editable","span");
				e.preventDefault();
			}			
		});*/
		
		$('textarea').live('keypress',function(e){
			if(e.keyCode ==34){
				if($("#"+this.id).next().length)
					$("#"+this.id).next().click();
				else if($("#next-page .turn-page").length>0){
					$("#next-page .turn-page").click();					
					$("#translation .block.show.editable:first").click();
				}
				e.preventDefault();
			}else if(e.keyCode == 33){
				if($("#"+this.id).prev().length)
				{
					$("#"+this.id).prev().click();
				}else if($("#prev-page .turn-page").length>0){
					$("#prev-page .turn-page").click();			
					$("#translation .block.show.editable:last").click();
				}
				e.preventDefault();
			}
		});
	/*	$('.translator-name').live('click',function(e){
			var content = trWork.work.translator;
			$(this).empty().append("<input type=\"text\" value=\""+content+"\" />");			
		});*/
		$('.work-title span.editable,a.translator-name.editable').live('click',function(e){
			var target = e.target;
			var fontSize = $(this).css('font-size');
			var content = '';
			if($(this).hasClass('translator-name')) content = trWork.work.translator;
			else{
				var id = $(this).attr('id');
				switch(id){
					case 'tr-author':content = (trWork.work.author!=null)?trWork.work.author:tdxio.i18n.anonymous;break;
					case 'tr-title':content = (trWork.work.title!=null)?trWork.work.title:tdxio.i18n.notitle;break;
					case 'orig-author':content = ajaxData.work.author;break;
					case 'orig-title':content = ajaxData.work.title;break;
				}
			}
			$(this).replaceWith("<input type=\"text\" class=\""+$(this).attr('class')+"\" id=\""+$(this).attr('id')+"\" value=\""+content+"\" />");
			//$("#"+$(this).attr('id')).focus();
			if($(this).hasClass('translator-name')) $('.onglet.first .translator-name').focus();
			else $("#"+$(this).attr('id')).focus();
		});
		
		$('#showtags').live('click',function(){$('.show-tag').click();$(this).attr('id','hidetags').children('a').text(tdxio.i18n.hidetags);});
		$('#hidetags').live('click',function(){$('.hide-tag').click();$(this).attr('id','showtags').children('a').text(tdxio.i18n.showtags);});
        $('.show-tag').live('click',function(){$('div.show-tag-area').show(50);$(this).attr('class','hide-tag').attr('title',tdxio.i18n.hidetags);});
        $('.hide-tag').live('click',function(){$('div.show-tag-area').hide(50);$(this).attr('class','show-tag').attr('title',tdxio.i18n.showtags);});

		
		$("div#work div.text").selectedText({
			min: 2,
			max: 1000,
			selecting: function(text) {$('input#query-value').val(text);},
			stop: function(text) {$('input#query-value').val(text);}
		});
		
		$("#lens-search").click(function(){$("#concord-query").submit();});
		$("img#close-search").live('click',function(){$("input#query-value").val('');});
		
        $("#concord-query").submit(tdxio.textSearch.getConcord);
        
        $("form#translate-form").live("submit",function(event) {
            event.preventDefault();
            window.event = event;
			window.$.submitForm($(this),"createtr",{'id':workId});
            return false; 
        }); 
       /* $("form#translate-form").live("reset",function(event) {
            event.preventDefault();
            $("form#translate-form").remove();
            $("div#new-translation").css('visibility','hidden');
            return false; 
        });*/
        
     /*   $(".show-menu").live("click",function(){$(this).toggleClass('selected');});*/
		$(".closeimg").live('click',function(e){
			e.preventDefault();
			var form = $(this).parents('form').attr('id');
			switch(form){
				case 'stag-form':
					$("div#insert-sentence-tag").css('visibility','hidden');	break;
				case 'translate-form':
					$("div#new-translation").css('visibility','hidden');	break;
				case 'tagform':
					$(this).parents('.show-tag-area').children(".add-tag").css('display','block');
					break;
				default:
					$(this).parent().css('visibility','hidden');
			}
			$(this).parents('form').remove();
		});

		$("#tr-icons .delbtn, #delete").live('click',function(){
			var can = false;
			if(window.trId!=null &&window.trId!='')
				can = window.$.checkRights('delete',window.trId);
			if(can){
				var answer = confirm(tdxio.i18n.want2delete);
				if(answer) {
					window.$.deleteWork(window.trId);
		}}});
	/*	
		$('.printbtn').live('click',function(){
			$('.print').empty();
			if($(this).parents('').length>0){
				$('.print').append(ajaxData.work.title);
				$('.print').append(ajaxData.work.the_text);
			}else{
				$('.print').append(ajaxData.work.title);
				$('.print').append(ajaxData.work.the_text);
			}
			$(".print").jqprint();
		});
		*/
		$('textarea').live('focus',function(){$(this).css('font-size','1em').css('color','#585858');});
		//$('.work-title input').live('focus',function(){$(this).css('font-size','inherit');});
					
		/*$('#tr-icons a').click(function(e){
			e.preventDefault();
			if(window.trId!=null && window.trId!='')
				document.location.href = this.href.replace(/\/id\/\d+/,"/id/"+window.trId);
		});*/
		
		$("#menu").click(function(){
			$(this).toggleClass('hide');
			$("#show-icons").toggleClass('hidden');
		});
		
		$(".segment").live('mousemove',function(event){
			if($("#notebtn").hasClass('on') ){
				var id = $(this).attr('id');
				$(".sentence-tag").remove();
				$(".segment").toggleClass('highlighted',false);
				$("#"+id).toggleClass('highlighted',true);				
				$('body').prepend('<span class="sentence-tag" id="'+id+'-img"></span>');
				$("#"+id+"-img").css('top',event.pageY-10).css('left',event.pageX+10);
				//$("#"+id+"-img").css('top',$(this).offset().top);
				//$("#"+id+"-img").css('left',$(this).offset().left - 10); 
			}
		});
		$("div#work div.text").live('mouseleave',function(event){
			$(".sentence-tag").remove();
		});
		
		$(".segment").live('click',function(event){		
			if($("#notebtn").hasClass('on') && event.target.className!="note-symbol"){
				$(".segment").toggleClass('selected',false);				
				$(this).toggleClass('selected',true);
				$("div#insert-sentence-tag").css('visibility','hidden');
				$("div#insert-sentence-tag").empty();
				$("div#insert-sentence-tag").css('top',event.pageY-100).css('left',event.pageX);
				window.sentenceToTag = $(this).attr('id').split('segment')[1];
				window.$.getForm('sentencetag'/*,window.workId,id.split('-')[0].match(/\d+/)*/);				
			}
		});
		
		$("#stagTA").live('focus',function(event){$(this).empty(); $(this).unbind( event );});
		
		$("#note,#notebtn").click(function(e){
			var can = false;
			if(window.workId!=null &&window.workId!='')
				can = window.$.checkRights('tag',window.workId);
			if(can){
				e.preventDefault();
				$("#notebtn").toggleClass('on');
				if(!$("#notebtn").hasClass('on')){
					$(".segment").toggleClass('highlighted',false);
					$(".segment").toggleClass('selected',false);
					$("div#insert-sentence-tag").css('visibility','hidden');
					$("div#insert-sentence-tag").empty();
				}else{
					window.$.hint(tdxio.i18n.notehint);
				}
			}
		});
		$(".note-symbol").live('click',function(){			
			var segNum = $(this).attr('id').split('-')[0].match(/\d+/);
			var noteNum = $(this).attr('id').split('-')[1].match(/\d+/);			
			window.$.showNote(segNum,noteNum);
		});
		$(".idle a,a.idle").click(function(e){e.preventDefault();});
		
    });
    
    

})(jQuery);
