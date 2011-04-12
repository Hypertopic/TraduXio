if (typeof console == "undefined") console={log:function(){}};
 
var trId = '';
var begin = 0;// segment from which to begin the text's display - it can represent the first or the last segment (page-turn backward or forward)
var end = 0;
var back = false;
var ajaxData;
var translations;
var nextHiddenId=null;    
var id_str = document.location.pathname.match(/\/id\/\d+/);
var minHeight = 400;
var maxH;
var docHeight;
var blocked=false;

(function($) {
    
    $.fn.addText = function(x0,x1,pre,post,sentences) {
        for(var x=0; x<=x1; x++){               
            $(this).append(pre + x +"'>" + sentences[x].content + post);
        }
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
    
    $.getBlocked = function(){return blocked;}
    $.setBlocked = function(val){blocked = val;}
    
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
        
    tdxio.page = {
        resize: function(){
            // 1. Make the height of the translation and of the work texts the same            
            var maxtemp = Math.max(maxH,$('#translation div.text').height(),$('#work div.text').height());
            $('#translation div.text').height(maxtemp);
            $('#work div.text').height(maxtemp);
            
            //2. Then (div#work and div#translation should have the same height)...
            if($('div#work').height()!=$('div#translation').height()){
                alert('Change the code! #work and #translation have different heights');
            }            
            // ... change the borders' height
            $('div.Rborder, div.Lborder').height($('div#work').height());
        
        },
        
        turn: function(dir,index){
            begin = parseInt(index);
            end=parseInt(index);
         //   alert(begin +' '+(dir=='prev-page'));
            tdxio.page.displayWork(ajaxData,trId,(dir=='prev-page'),begin,end);
            tdxio.page.setState($('#editbtn').attr('class')=='on'?'editable':'reset');
            tdxio.page.adjust();
            tdxio.page.resize();
        },
        writeWork: function(sentences,from,to, step){
            var pre = "<span class='segment' id='text"+data.work.id +"-segment";
            for(var x=from; x<=to; x+=step){
                $('#work div.text').append(pre + x +"'>" + sentences[x].content +"</span>");
            }
        },
        resetHeight : function(){
            $('div#test').height("");
            $('#translation div.text').height("");
            $('#work div.text').height("");
        },
        adjust : function(){
            if($("#editbtn").attr('class')=='on'){
                $('.block').toggleClass('show',true);
                                 
                $('#translation div.text span.block').each(function(index,el){
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
        
        setState : function(mode){
            if(mode=='reset'){
                $.setBlocked(false);
                $('.text').toggleClass('show',true);
                $('.block').toggleClass('show',false);
                $('#translation .block').toggleClass('editable',false);
                $('#icons div').toggleClass('on',false);
                $('.cut').remove();
                $('.merge').remove();
            }else if(mode=='editable'){
                $.setBlocked(true);
                $('.text').toggleClass('show',false);
                $('.block').toggleClass('show',true);
                $('#translation .block').toggleClass('editable',true);
                $('#editbtn').toggleClass('on',true);
                $('#work span.segment').after('<span class="cut" title="Cut here"></span>');
                $('#work span.block').after('<span class="merge" title="Merge here"></span>');
                $('#work span.block.show span.cut:last-child').remove();
            }else if(mode=='editing'){
				$("#translation div.text").wrap('<form id="edit-form" />');
				$("span.block.show.editable").wrapInner("<textarea class='autogrow' />");
				$('#icons #savebtn').toggleClass('on',true);
			}else if(mode=='stop-edit'){
				$('#icons #savebtn').toggleClass('on',false);
				var textsA = $("textarea.autogrow").detach();
				var editF = $("#edit-form").detach();
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
                if(trId == ''){// there are no translations
                    //display only the work
                   
                    $('#translation .text').append("<span id='create'>Create a translation</span>"); 
                    if(backward===false){    
                        var i;
                        $('#test').append(pre + sentences[beginSeg].number + "'>" + sentences[beginSeg].content + "</span>");                    
                        for(i=beginSeg; (i==beginSeg) || (i<len && $('#test').height()<= maxH) ; i++){
                            $('#work div.text').append(pre + sentences[i].number + "'>" +sentences[i].content + "</span>");
                            if(i+1<len){
                                $('#test').append(pre + sentences[i+1].number + "'>" +sentences[i+1].content + "</span>");
                            }
                            endSeg=i;
                        }                       
                    }else{
                        var i;
                        $('#test').append(pre + sentences[endSeg].number + "'>" + sentences[endSegSeg].content + "</span>");
                        for(i=endSeg; (i==endSeg)|| (i>=0 && $('#test').height()<= maxH) ; i--){
                            $('#work div.text').prepend(pre + sentences[i].number + "'>" +sentences[i].content + "</span>");
                            if(i-1>=0){
                                $('#test').prepend(pre + sentences[i-1].number + "'>" +sentences[i-1].content + "</span>");
                            }                            
                        }
                        beginSeg=i+1;
                        while((endSeg+1<len)&&($('#test').height()<= maxH)){
                            $('#test').append(pre + sentences[endSeg+1].number + "'>" +sentences[endSeg+1].content + "</span>");
                            if($('#test').height()<= maxH){
                                endSeg++;
                                $('#work div.text').append(pre + sentences[endSeg].number + "'>" +sentences[endSeg].content + "</span>");                                
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
                    $('#translation .work-title span.author').html((trWork.work.author!=null)?trWork.work.author +', ':'');
                    $('#translation .work-title span.title').html(trWork.work.title);                    
                    $('div#translation').attr('dir',(trWork.work.rtl==1)?'rtl':'');
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
                            $('#translation div.text').append(preblock + i + "'>" +((trWork.blocks[i].translation)?trWork.blocks[i].translation:'') + "</span>");
                            
                            for(var x=trWork.blocks[i].from_segment,text=''; x<=trWork.blocks[i].to_segment; x++){
                                text += pre + x +"'>" + sentences[x].content +"</span>";
                            }
                            $('#work div.text').append(outblock + i + "'>"+text+"</span>");
                            $('#test').html($('#work div.text').html());
                            if(i+1<trlen){
                                for(var x=trWork.blocks[i+1].from_segment; x<=trWork.blocks[i+1].to_segment; x++){
                                    $('#test').append(pre + x +"'>" + sentences[x].content +"</span>");
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
                            $('#translation div.text').prepend(preblock + i + "'>" +((trWork.blocks[i].translation)?trWork.blocks[i].translation:'') + "</span>");
                             for(var x=trWork.blocks[i].from_segment,text=''; x<=trWork.blocks[i].to_segment; x++){
                                text += pre + x +"'>" + sentences[x].content +"</span>";
                            }
                            $('#work div.text').prepend(outblock + i + "'>"+text+"</span>");
                            $('#test').html($('#work div.text').html());
                            if(i-1>=0){
                                for(var x=trWork.blocks[i-1].to_segment; x>=trWork.blocks[i-1].from_segment; x--){
                                    $('#test').prepend(pre + x +"'>" + sentences[x].content +"</span>");
                                }
                            }
                        }
                        $('#test').html($('#work div.text').html());
                        beginSeg=trWork.blocks[i+1].from_segment;
                        while((endBlock+1<trlen)&&($('#test').height()<= maxH)){
                            var from=trWork.blocks[endBlock+1].from_segment;
                            var to=trWork.blocks[endBlock+1].to_segment;
                            for(var x=from; x<=to; x++){
                                $('#test').append(pre + x +"'>" + sentences[x].content +"</span>");
                            }
                            if($('#test').height()<= maxH){
                                $('#translation div.text').append(preblock + (endBlock+1) + "'>" +((trWork.blocks[endBlock+1].translation)?trWork.blocks[i].translation:'') + "</span>");
                                for(var x=from,text=''; x<=to; x++){
                                    text += pre + x +"'>" + sentences[x].content +"</span>";
                                }
                                $('#work div.text').append(outblock + (endBlock+1) + "'>"+text+"</span>");
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
            }
            $('#test').empty();
            begin = beginSeg;
            end = endSeg;
            
        },
        
        displayOnglets: function(trls){
            var N = trls.length;
            var lineWidth = $("#right-page").width()-30;
            $('span#more').css('visibility','hidden');
            $('.onglets li').css('z-index',-100);
            $('.onglets li').css('visibility','hidden'); 
            var totWidth = 0;
            var overlap = 0;
            var ongClass='onglet first';
            var i,id;
            
            for(i = 0;i==0 || (i<N && (totWidth<lineWidth));i++){
            //   alert('tot: '+totWidth +', lineW: '+lineWidth);
                id = trls[i].work.id;
                $('li#onglet-'+id).css('z-index',N-i);
                $('li#onglet-'+id).css('left',totWidth-overlap);
                $('li#onglet-'+id).attr('class',ongClass);
                $('li#onglet-'+id).css('visibility','visible');
                totWidth += $('li#onglet-'+id).outerWidth()-overlap;
                overlap = 15;
                ongClass='onglet';
            }
            
            if(totWidth>lineWidth){
                $('li#onglet-'+id).css('visibility','hidden');
                i--;
            }//alert(totWidth+' <-tot : line->'+lineWidth);
            if(i<N){
                nextHiddenId = trls[i].work.id;
                $('span#more').css('visibility','visible');
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
				trId = hash.match(/tr\d+/gi)[0].substr(2);
				beginHash = hash.match(/#beg\d+/gi);
				if((beginHash!=null)&&(beginHash!='')){begin = beginHash[0].substr(4);}
				params={'qtity':qtity,'trId':trId};
			}
						  
			$.ajax({
				type:"get",
				url:encodeURI(url),
				dataType: "json",
				data: params,
				success: function(rdata){
					if(rdata.work.Interpretations.length>0){
						var exists=false;
						for(var k=0;k<rdata.work.Interpretations.length;k++){
							if(trId == rdata.work.Interpretations[k].work.id){
								exists = true;
								break;
							}
						}
						trId = (exists)?trId:rdata.work.Interpretations[0].work.id;
						document.location.hash='tr'+trId;
						//trId = (trId=='')?rdata.work.Interpretations[0].work.id:trId;
						translations = tdxio.array.trShift(rdata.work.Interpretations.slice(),trId,true);
						tdxio.page.displayOnglets(translations);
					}
					ajaxData = rdata;
					tdxio.page.displayWork(rdata,trId,false,begin,end);
					tdxio.page.resize();
				},
				error: function() {
					alert("error reading the workk");
				}
			}); 
		},
		
		gotoTransl: function(newId){
            trId = newId;
            translations = tdxio.array.trShift(ajaxData.work.Interpretations.slice(),newId,true);
           /*tdxio.page.displayOnglets(translations);    */
            tdxio.page.displayOnglets(translations);
            tdxio.page.displayWork(ajaxData,newId,back,begin,end);
            tdxio.page.setState($('#editbtn').attr('class')=='on'?'editable':'reset');
            tdxio.page.adjust();
            tdxio.page.resize();
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
            tdxio.page.displayOnglets(translations);
        }                
    };
    
    $(document).ready(function() {
        
        tdxio.page.getWork();
        
        $(window).bind('resize',(function() {
            if(!blocked){
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
        
        $('span#create').live('click',function(){
            alert('Create translation');
        });
        
        $('ul.onglets li').live('click',function(){
			$.setBlocked(false);
			gotoTransl(this.id.split("-")[1]);
        });
        
        $('span#more').click(function(){
			if(nextHiddenId!=null)
                translations = tdxio.array.trShift(translations,nextHiddenId,false);
            tdxio.page.displayOnglets(translations);
        });
        
    });
    
    

})(jQuery);
