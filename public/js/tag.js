if (typeof console == "undefined") console={log:function(){}};

(function($) {
    
    tdxio.tag = {
        
        print_tags: function(tag,genre,index){
            var id_str = document.URL.substr(document.URL.search("/id/"));
            
            var base = tdxio.baseUrl.split(document.domain)[1];
                       
            //var remove_url= base +"/tag/deletetag"+id_str+"/tag/"+tag+"/genre/"+index;
            //var remove = "<a class=\"delete\" id=\""+index+"-"+tag+"\" href=\""+remove_url+"\"> X </a>";
            var newid = index+"-"+tag;
            var remove = "<a class=\"delete\" id=\""+newid+"\" onmouseover=\"this.style.cursor= 'pointer'\"> X </a>";
            if($("#group-"+index).length==0){
                    $("#show-tag-ajax").append("<div class=\"tag-group\" id=\"group-"+index+"\"></div>");
                    $("#group-"+index).append("<span class='genre' id='"+index+"'>"+genre+"</span> ");
            }
            if($("#"+newid).length==0){
                $("#group-"+index).append("<span class=\"tag-item\" title=\""+genre+"\">"+tag+" "+remove+"</span> ");
           }
        }
        
    };
    
   $(document).ready(function() {
       
        $("#tagform").bind('submit',function(){
            
            if($('#tag_input').val()!="" || $('#tag_input').val()!=""==null){
                var id_str = document.URL.substr(document.URL.search("/id/"));
                var url = tdxio.baseUrl+"/tag/ajaxtag"+id_str;
                $("#tagform").ajaxSubmit({
                    type: "post",
                    url: url,
                    dataType: "json",
                    success:function(data,status){
                        if(data.response.outcome==true){
                            var genre=$("#genresel")[0].options[$("#genresel")[0].selectedIndex].text.toLowerCase();
                            var index=$("#genresel").val();
                            var tag = $('#tag_input').val(); 
                            tdxio.tag.print_tags(tag,genre,index);
                        }
                        else{
                            alert(data.response.message);
                        }
                    },
                    error:function() {
                        alert("error storing the tag");
                    },
                    complete:function() {
                        
                    }
                });
            }else{
                alert("No tag inserted!");
            }
            return false;});
            
       
         $("a.delete").live("click",function(){
            
            var TagId = this.id.split('-');
            var genre = TagId[0];
            var tag = TagId[1];
            var elID = "#"+this.id;
            
            var id_str = document.URL.substr(document.URL.search("/id/"));
            var url = tdxio.baseUrl+"/tag/deletetag"+id_str+"/tag/"+tag+"/genre/"+genre;
            
            $.ajax({
                type:"post",
                url:url,
                dataType: "json",
                success: function(data){
                    if(data.last){
                        $(elID).parent('span').parent('div').remove();
                    }
                    else{
                        $(elID).parent('span').remove();
                    }
                },
                error: function() {
                    alert("error erasing the tag");
                }
            });
            
            return false;
        });
        
    });
    

})(jQuery);
