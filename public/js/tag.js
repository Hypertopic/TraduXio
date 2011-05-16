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
                    complete:function() {}
                });
            }else{
                alert("No tag inserted!");
            }
        },
        
        remove: function(id,textId){  
			
			var temp = id.split('-');
            var genre = temp[0];
            var tagID = temp[1];
            var elID = "#"+id;
            var url = tdxio.baseUrl+"/tag/deletetag/id/"+textId+"/tagid/"+id.split('-')[1]+"/genre/"+id.split('-')[0];
            
            $.ajax({
                type:"post",
                url:encodeURI(url),
                dataType: "json",
                success: function(rdata){
                    if(rdata.response==false){
						if(rdata.message.code == 2){tdxio.page.redirect(rdata.message.text);}
					}else if(rdata.last){
                        $("#"+id).parent('span').parent('span').parent('div').remove();
                    }
                    else{
                        $("#"+id).parent('span').parent('span').remove();
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
					$("div#"+parentId+" .add-tag").replaceWith(rdata.tagform);
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
		
		
       
		$("a.delete").live("click",function(){
			//alert($(this).parent('span').parent('span').parent('div').parent('div').attr('id'));
            var textId = $(this).parent('span').parent('span').parent('div').parent('div').attr('id')=='orig-tag'?window.workId:window.trId;
            tdxio.tag.remove(this.id,textId);          
            return false;
        });        
    });
    

})(jQuery);
