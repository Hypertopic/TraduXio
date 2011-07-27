if (typeof console == "undefined") console={log:function(){}};

var workId;
var state;
(function($) {
    var url;
    
    $.functionName1 = function(){
    };
            
    outerLabel = {        
        fname1: function(){                
        }            
    };    

        
    $.transform = function(action,data){

        switch(action)
        {
        case 'extend':
        tdxio.page.setState('reset');
        //go to the last page, if you're not in it
        tdxio.page.displayWork(window.ajaxData,window.trId,true,window.ajaxData.work.Sentences.length-1,window.ajaxData.work.Sentences.length-1);
        tdxio.page.resetHeight();
        
        //append the form
        $('#work div.text').append(data.form);
       // $('#work #extend-form').append('<span class="closeimg" title="Close"></span>');
          /*  $('#myForm').ajaxForm(function() { 
                alert("The text has been successfully extended!"); 
		});*/
		tdxio.page.resize();
		$.scrollTo($('#insert-text'));
		tdxio.page.setBlocked(true);
            break;
        case 'edit':

          break;
        case 'translate':
        tdxio.page.setState('reset');
        $('div#new-translation').css('visibility','visible');
        $('div#new-translation').css('left',document.width/2-200);
        $('div#new-translation').append(data.form);
        break;
        case 'sentencetag':
        $('div#insert-sentence-tag').css('visibility','visible');
        //$('div#insert-sentence-tag').css('left',document.width/2-200);
        $('div#insert-sentence-tag').append(data.form);
        break;
        default:          
        }     
    };
    
    $.updateTranslation = function(el,newEl){
		var i=0;
		for(i = 0; i<window.ajaxData.work.Interpretations.length;i++){
			if(window.ajaxData.work.Interpretations[i].work.id==window.trId){break;}
		}  
        if(el=="blocks"){
			window.translations[0].blocks = newEl;
			window.ajaxData.work.Interpretations[i].blocks=newEl;
			tdxio.page.displayWork(window.ajaxData,window.trId,window.back,window.begin,window.end);
			tdxio.page.setState(window.state);
			tdxio.page.adjust();
			tdxio.page.resize();
		}else if(el=="block"){
			window.translations[0].blocks[newEl.blockId].translation = newEl.newText;
			window.trWork.blocks[newEl.blockId].translation = newEl.newText;
			window.ajaxData.work.Interpretations[i].blocks[newEl.blockId].translation = newEl.newText;			
			tdxio.page.displayWork(window.ajaxData,window.trId,window.back,window.begin,window.end);
			tdxio.page.setState(window.state);
			tdxio.page.adjust();
			tdxio.page.resize();

		}
		else if(el=="title" || el=="author"){
			window.translations[0].work[el] = newEl;			
			window.ajaxData.work.Interpretations[i].work[el]=newEl;
			window.trWork.work[el]=newEl;  
		}
	};
    
    $.update = function(action,data){
       // alert('update '+action);
        switch(action)
        {
        case 'ajaxextend':
            $('form#extend-form').remove();
            var lastId = $("#work div.text span:last")[0].id;
          //  alert(lastId);
            var newId = lastId;
            newId = newId.replace(/\d+$/,parseInt(lastId.match(/\d+$/))+1);
            $('#'+lastId).after("<span id='"+newId+"' class='segment'>"+data.addedtext+"</span>" );
            $("#work div.text").height("");
            tdxio.page.resize();
            tdxio.page.setBlocked(false);
            break;
        case 'cut':
        case 'merge':    
            $.updateTranslation('blocks',data.newblocks);                        
            $.scrollTo($('#text'+workId+'-segment'+data.segToRed));
        break;     
        case 'delete': 
			if(data.newId==window.trId){
				window.translations.splice(0,1);//remove item from translations
				var k;//remove item from ajaxData
				var L = ajaxData.work.Interpretations.length;
				for(k=0;k<L;k++){
					if(ajaxData.work.Interpretations[k].work.id==data.newId){
						ajaxData.work.Interpretations.splice(k,1);
						break;
					}
				}//update trId
				if(L>1)
					window.trId = (k-1)>=0?ajaxData.work.Interpretations[k-1].work.id:(k<L?ajaxData.work.Interpretations[k].work.id:'');
				else window.trId = '';
				//remove onglet
				$(".onglet#onglet-"+data.newId).remove();	
				//if(window.trId!='')
					tdxio.page.gotoTransl(window.trId);
			/*	else{
					$("#top-border").show(10);
					$("#plus").hide(10);
				}*/
			};
        break; 
        case 'createtr':  
			$("form#translate-form").remove();
            $("div#new-translation").css('visibility','hidden'); 
            //1 add an element to the list of translations (window.translations): empty translation template
			var lastTo = ajaxData.work.Sentences.pop().number;
			var trlTemplate = {'blocks':[{'work_id': data.newId , 'original_work_id' : workId , 'translation':'' , 'from_segment':0,'to_segment':lastTo}],'work': {'id': data.newId,'title':data.values.title,'author':data.values.author,'language':data.values.language}};
			trlTemplate=[trlTemplate].concat(window.translations);
			window.$.getCurrentUser();
			if(data.newId!='' && data.newId!=null)
				document.location.hash="tr"+data.newId;
			tdxio.page.getWork();			
            $("#top-border").hide(10);
            $("#plus").show(10);
            var translatorName = (data.values.translator!='' && data.values.translator!=null)?data.values.translator:tdxio.i18n.anonymous;
			$("ul.onglets").prepend("<li class='onglet' id='onglet-"+data.newId+"'><span title='"+data.values.language+"'><span class='container'><img class='prec-onglet' src='"+tdxio.baseUrl+"/images/prevong.png"+"'/> <a class='translator' href='#tr"+data.newId+"'>"+ tdxio.i18n.translator+":</a> <a class='translator-name' href='#tr"+data.newId+"'>"+translatorName+"</a> <img class='next-onglet' src='"+tdxio.baseUrl+"/images/nextong.png"+"'/></span></span></li>");
			tdxio.page.gotoTransl(data.newId,'edit');
            break;
        case 'translate':
        $.updateTranslation('block',data);
          break;
		case 'update-orig':
			window.ajaxData.work[data.el]=data.val;
		break;
		case 'update-tr':$.updateTranslation(data.el,data.val);
		break;
        default:
          
        }

    };     
    
    $.showBlocks = function(show){
        if(show){
            $('.text').css('border','none');
            
        }else{
            $('.text').css('border','');
            
        }        
    };
    $.setUser = function(username){window.user = username;};
    
    $.getCurrentUser = function(){
		var url = tdxio.baseUrl+"/work/getuser/";
		var curUser;
		$.ajax({
			type: "get",
			url: encodeURI(url),
			dataType: "json",
			data: {'id':window.workId},
			async:false,
			success:function(rdata){
				if (rdata.response==false) {//error somewhere
					if(rdata.message.code ==2){tdxio.page.redirect(rdata.message.text);}
					else alert(rdata.message.text);
				}else {
					$.setUser(rdata.user); 					
					curUser = rdata.user;
				}
			},
			error:function() {alert('Could not retrieve the current user');}		
		});
		return curUser;
	};
	
    $.getValue = function(name,id){
		var value;
		$.ajax({
			type:"get",
			url:encodeURI(tdxio.baseUrl+"/work/getvalue"),
			dataType: "json",
			data:{'value':name,'id':id},
			async:false,
			success: function(rdata,status){
				if (rdata.response==false) {
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}                        
					else alert(rdata.message.text);
				}else{
					value = rdata.value;
				}
			},
			error: function() {
				alert("error getting the form");
			}
		}); 
		return value;  
	};
	
    $.getForm = function(formType,id){
		$.ajax({
			type:"get",
			url:encodeURI(tdxio.baseUrl+"/work/getform"),
			dataType: "json",
			data:{'type':formType,'id':id},
			async:false,
			success: function(rdata,status){
				if (rdata.response==false) {
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}                        
					else alert(rdata.message.text);
				}else{
					window.$.transform(formType,rdata);
				}
			},
			error: function() {
				alert("error getting the form");
			}
		});   
	};
    
    $.submitTranslation = function(blockId){
		var url = tdxio.baseUrl+"/translation/ajaxedit/id/"+window.trId;
		
		$.ajax({
                type: "post",
                url: encodeURI(url),
                dataType: "json",
                data: $("#translation .block.show.editable#"+blockId),
                clearForm: false,
                success:function(rdata,status){
                    if (rdata.response==false) {//error somewhere
						if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
                        else alert(rdata.message.text);
                    }else {
						$("#translation .block.show.editable#"+blockId).text(this.value);
						$.update('translate',{'blockId':blockId.match(/\d/),'newText':rdata.newText});
                    }
                },
                error:function() {
                    alert("error saving the translation");
                },
                complete:function() {
                    //alert('complete');    
                }
            });		
	};
	
	$.submitForm = function(form,action,params){
		url = tdxio.baseUrl+"/work/"+action;
		form.ajaxSubmit({
			type: "post",
			url: encodeURI(url),
			dataType: "json",
			data: params,
			clearForm: true,
			success:function(rdata,status){
				if (rdata.response==false) {//error somewhere
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					else alert(rdata.message.text);
				}else {
					window.$.update(action,rdata);
				}
			},
			error:function() {
				alert("error posting the form");
			},
			complete:function() {
				//alert('complete');    
			}
		});
	};
	
    $.deleteWork = function(id){
		url = tdxio.baseUrl+"/work/ajaxdelete";
		$.ajax({
			type: "post",
			url: encodeURI(url),
			dataType: "json",
			data: {'id':id},
			success:function(rdata,status){
				if (rdata.response==false) {//error somewhere
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					else alert(rdata.message.text);
				}else{
					$.update('delete',rdata);
				}
			},
			error:function() {
				alert("error deleting the work");
			},
			complete:function() {
				//alert('complete');    
			}
		});
	};
	
	$.checkRights = function(privilege,id){
		url = tdxio.baseUrl+"/work/can";
		var can=false;
		$.ajax({	
			type: "get",
			url: encodeURI(url),
			dataType: "json",
			async:false,
			data: {'privilege':privilege,'id':id},
			success:function(rdata,status){
				if (rdata.response==false) {//error somewhere
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					else alert(rdata.message.text);
				}else{
					can=true;
				}
			},
			error:function() {
				alert("error retrieving the rights");
			}
		});	
		return can;
		
	};
	
	$.hint = function(text){
		$('#hint').fadeIn('fast', function(){
			$(this).append(text);
			setTimeout( function(){
				$("#hint").fadeOut("slow");
			}, 3000);
		});
		$('#hint').empty();
	};
	
	$.showNote = function(segNum,noteNum){
		var note = window.ajaxData.work.SentencesTags[segNum][noteNum];
		$(".show-note").css('visibility','visible').attr('id','showseg'+segNum+'-note'+note.id);
		$(".show-note .note").text(note.comment);
		if(note.user==$.getCurrentUser()){
			$(".show-note .delete").toggleClass('hidden',false);
		}else{
			$(".show-note .delete").toggleClass('hidden',true);
		}
	};
	
	$.saveMeta = function(textId,elName,value){
		var params= {'elName':elName,'value':value};
		$.ajax({
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
					var updObj = (textId!=window.workId)?'tr':'orig';
					$.update('update-'+updObj,{'el':elName,'val':value});
				}
			},
			error:function() {
				alert("Error in the saving process");
			},
		});
		
	};
	
	
	$.setIconsState = function(state){
		$.ajax({
			type: "post",
			url: encodeURI(tdxio.baseUrl+"/index/iconstate/state/"+state),
			dataType: "json",
			success:function(rdata,status){
				alert('success');
			},
			error:function() {
				alert("Error in the icons' state setting process");
			},
		});
	};
    
    $(document).ready(function() {

		
    });
    
    

})(jQuery);
