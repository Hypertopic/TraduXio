if (typeof console == "undefined") console={log:function(){}};

(function($) {
    
var id_str = document.URL.substr(document.URL.search("/id/"));
        
    tdxio.tag = {
		
        print_tags: function(tag,genre,index,tagID){
            
            //var base = tdxio.baseUrl.split(document.domain)[1];                       
            //var remove_url= base +"/tag/deletetag"+id_str+"/tag/"+tag+"/genre/"+index;
            //var remove = "<a class=\"delete\" id=\""+index+"-"+tag+"\" href=\""+remove_url+"\"> X </a>";
            
            var newid = index+"-"+tagID;
            var remove = "<span><a class=\"delete\" id=\""+newid+"\" onmouseover=\"this.style.cursor= 'pointer'\"> X </a></span>";
            if($("#group-"+index).length==0){
                $("#show-tag-ajax").append("<div class=\"tag-group\" id=\"group-"+index+"\"></div>");
                $("#group-"+index).append("<span class='genre' id='"+index+"'>"+genre+"</span> ");
            }
            if($("#"+newid).length==0){
                $("#group-"+index).append("<span class=\"tag-item\" title=\""+genre+"\"><span class=\"tag-text\">"+tag+"</span>"+remove+"</span> ");
            }
        },
        
        insert: function(){
			var url = tdxio.baseUrl+"/tag/ajaxtag"+id_str;
			if($('#tag_input').val()!="" || $('#tag_input').val()!=""==null){                
                $("#tagform").ajaxSubmit({
                    type: "post",
                    url: url,
                    dataType: "json",
                    success:function(rdata){
                        if(rdata.response==true){
                            var genre=$("#genresel")[0].options[$("#genresel")[0].selectedIndex].text.toLowerCase();
                            var genreId=$("#genresel").val();
                            var tag = $('#tag_input').val(); 
                            tdxio.tag.print_tags(tag,genre,genreId,rdata.newID);
                            $('#tagform').resetForm();
                        }
                        else{alert(rdata.message);}
                    },
                    error:function() {alert("error storing the tag");},
                    complete:function() {alert('complete');}
                });
            }else{
                alert("No tag inserted!");
            }
        },
        
        remove: function(id){  
			
			var temp = id.split('-');
            var genre = temp[0];
            var tagID = temp[1];
            var elID = "#"+id;
            var url = tdxio.baseUrl+"/tag/deletetag"+id_str+"/tagid/"+id.split('-')[1]+"/genre/"+id.split('-')[0];
            
            $.ajax({
                type:"post",
                url:encodeURI(url),
                dataType: "json",
                success: function(rdata){
                    if(rdata.last){
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
	   
        $("#tagform").bind('submit',function(){
            tdxio.tag.insert();            
            return false;
		});            
       
		$("a.delete").live("click",function(){
            tdxio.tag.remove(this.id);          
            return false;
        });        
    });
    

})(jQuery);
