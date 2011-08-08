if (typeof console == "undefined") console={log:function(){}};

(function($) {
    
//var id_str = document.URL.substr(document.URL.search("/id/"));
        
    tdxio.tag = {
	
        print_tags: function(tag,genre,index,tagID,parentId){
            //var base = tdxio.baseUrl.split(document.domain)[1];                       
            //var remove_url= base +"/tag/deletetag"+id_str+"/tag/"+tag+"/genre/"+index;
            //var remove = "<a class=\"delete\" id=\""+index+"-"+tag+"\" href=\""+remove_url+"\"> X </a>";
            
            var newid = index+"-"+tagID;
            var remove = "<span><a class=\"delete\" id=\""+newid+"\" onmouseover=\"this.style.cursor= 'pointer'\"> X </a></span>";
            if($("div#"+parentId+" #group-"+index).length==0){
				$(".show-tag-area#"+parentId).append("<div class=\"tag-group\" id=\"group-"+index+"\"></div>");
                $("div#"+parentId+" #group-"+index).append("<span class='genre' id='"+index+"'>"+genre+"</span> ");
            }
            if($("div#"+parentId+" #"+newid).length==0){
                $("div#"+parentId+" #group-"+index).append("<span class=\"tag-item\" title=\""+genre+"\"><span class=\"tag-text\">"+tag+"</span>"+remove+"</span> ");
            }
        },
        insertStag: function(textId,number){
			var url = tdxio.baseUrl+"/tag/ajaxtag/id/"+textId;
			if($("#stagTA").val()!="" || $("#stagTA").val()!=""==null){  
				$("#stag-form").ajaxSubmit({
                    type: "post",
                    url: url,
                    dataType: "json",
                    data: {'number':number},
                    success:function(rdata){
                        if(rdata.response==true){
							var note,noteNumber;
							for(var i in rdata.tags){
								if(rdata.tags[i].id==rdata.newID){
									note = rdata.tags[i];
									noteNumber = i;
								}
							}				
							$(".segment").toggleClass('highlighted',false);
							$(".segment").toggleClass('selected',false);                    
							$("#insert-sentence-tag").css('visibility','hidden');
							$("#insert-sentence-tag").empty();
							tdxio.page.addNote(number,noteNumber,note); 
                        }
                        else{
							if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
							else alert(rdata.message.text);
						}
                    },
                    error:function() {alert("error storing the tag");},
                }); 
			}else{
                alert("No tag inserted!");
            }
		},
        
        insert: function(textId,parentId){
			var url = tdxio.baseUrl+"/tag/ajaxtag/id/"+textId;
			if($("div#"+parentId+" #tag_input").val()!="" || $("div#"+parentId+" #tag_input").val()!=""==null){                
                $("div#"+parentId+" #tagform").ajaxSubmit({
                    type: "post",
                    url: url,
                    dataType: "json",
                    success:function(rdata){
                        if(rdata.response==true){
                            var genre=$("div#"+parentId+" #genresel")[0].options[$("div#"+parentId+" #genresel")[0].selectedIndex].text.toLowerCase();
                            var genreId=$("div#"+parentId+" #genresel").val();
                            var tag = $("div#"+parentId+" #tag_input").val(); 
                            tdxio.tag.print_tags(tag,genre,genreId,rdata.newID,parentId);
                            $("div#"+parentId+" #tagform").resetForm();
                        }
                        else{
							if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
							else alert(rdata.message.code);
						}
                    },
                    error:function() {alert("error storing the tag");},
                });
            }else{
                alert("No tag inserted!");
            }
        },
        
        remove: function(workId,taggableId,tagId,genre){  
			
            var url = tdxio.baseUrl+"/tag/deletetag";
            
            $.ajax({
                type:"post",
                url:encodeURI(url),
                dataType: "json",
                data: {'id':workId,'taggableid':taggableId,'tagid':tagId,'genre':genre},
                success: function(rdata){
                    if(rdata.response==false){
						if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					}else if(workId==taggableId){ 
						if(rdata.last)$("#"+genre+'-'+tagId).parent('span').parent('span').parent('div').remove();
						else $("#"+genre+'-'+tagId).parent('span').parent('span').remove();
                    }
                },
                error: function() {alert("error erasing the tag");}
            });
		}
        
    };
    
   $(document).ready(function() {
	   
	   $(".add-tag").live('click',function(){
		var parentId = $(this).parent('div').attr('id');
		var textId = parentId=='orig-tag'?window.workId:window.trId;
		$.ajax({
			type:"get",
			url:encodeURI(tdxio.baseUrl+"/tag/getform"),
			dataType: "json",
			data:{'id':textId},
			success: function(rdata,status){
				if (rdata.response==false) {
					if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					else alert(rdata.message.code);
				}else{
					//$("div#"+parentId+" .add-tag").replaceWith(rdata.tagform);
					$("div#"+parentId).prepend(rdata.tagform);
					$("div#"+parentId+" .add-tag").css('display','none');
				}
			},
			error: function() {
				alert("error getting the form");
			}
		});   
	   });
	   
        $("#tagform").live('submit',function(){
			var parentId = $(this).parent('div').attr('id');
			var textId = parentId=='orig-tag'?window.workId:window.trId;
            tdxio.tag.insert(textId,parentId);      
            return false;
		});            
		
		 $("#stag-form").live('submit',function(){
			var textId = window.workId;
            tdxio.tag.insertStag(textId,window.sentenceToTag);      
            return false;
		});  
       
		$("a.delete").live("click",function(e){
			e.preventDefault();
			var taggableId,tagId,genre,workId;
			if($(this).parents(".show-note").length!=0){				
				var segNum = $(this).parents(".show-note").attr('id').split('-')[0].match(/\d+/)[0];
				tagId = $(this).parents(".show-note").attr('id').split('-')[1].match(/\d+/)[0];
				taggableId = ajaxData.work.Sentences[segNum].id;
				genre = ajaxData.work.SentencesTags[segNum][0].genre;
				workId = window.workId;
				tdxio.tag.remove(workId,taggableId,tagId,genre);    
				$('.show-note .closeimg').click();
				var noteNum;
				for(var i in ajaxData.work.SentencesTags[segNum]){
					if(ajaxData.work.SentencesTags[segNum][i].id==tagId)
						noteNum=i;
				}
				tdxio.page.removeNote(segNum,noteNum);
			}else{
				taggableId = $(this).parent('span').parent('span').parent('div').parent('div').attr('id')=='orig-tag'?window.workId:window.trId;				
				workId = taggableId;
				genre = this.id.split('-')[0];
				tagId = this.id.split('-')[1];
				tdxio.tag.remove(workId,taggableId,tagId,genre);            
			}                  
            return false;
        });        
        
    });
    

})(jQuery);
