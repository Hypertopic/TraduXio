if (typeof console == "undefined") console={log:function(){}};

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
        //go to the last page, if you're not in it
        tdxio.page.displayWork(window.ajaxData,window.trId,true,window.ajaxData.work.Sentences.length-1,window.ajaxData.work.Sentences.length-1);
        tdxio.page.resize();
        //append the form
            $('#work div.text').append(data.form);
          /*  $('#myForm').ajaxForm(function() { 
                alert("The text has been successfully extended!"); 
            });*/
            break;
        case 'edit':

          break;
        case 'translate':
          break;
        default:
          
        }
        $("#work div.text").height("");
        tdxio.page.resize();
        
        $.scrollTo($('#insert-text'));
      //  alert('transform '+action+' resized');
    };
    
    $.update = function(action,data){
        alert('update '+action);
        switch(action)
        {
        case 'extend':
            $('form#extend-form').remove();
            var lastId = $("#work div.text span:last")[0].id;
            alert(lastId);
            var newId = lastId;
            newId = newId.replace(/\d+$/,parseInt(lastId.match(/\d+$/))+1);
            $('#'+lastId).after("<span id='"+newId+"'>"+data.addedtext+"</span>" );

            //append(data.addedtext);
            break;
        case 'edit':

          break;
        case 'translate':
          break;
        default:
          
        }
        $("#work div.text").height("");
        tdxio.page.resize();
    }

    
    $(document).ready(function() {
        var url;
        var action;
        var idstr = document.location.pathname.match(/\/id\/\d+/);
        var workId = parseInt(idstr[0].match(/\d+/));
       
       $("div#myslidemenu ul li ul li a").live('click',function(event){
            event.preventDefault();
           /* if($(this).attr('class')=='idle'){
                window.location.replace(tdxio.baseUrl+"/login/index");
            }*/
            url = $(this).attr("href");
            action=url.split("/work/")[1].split("/")[0];
           //alert(url);
            $.ajax({
                type:"get",
                url:encodeURI(tdxio.baseUrl+"/work/getform"),
                dataType: "json",
                data:{'type':action},
                success: function(rdata,status){
                    if (rdata.response==false) {
                        alert(rdata.message);
                    }else{
                        $.transform(action,rdata);
                    }
                },
                error: function() {
                    alert("error getting the form");
                }
            });   
            return false;
        });
        
       $("form").live("submit",function() {
           
           url = tdxio.baseUrl+"/work/ajax"+(this.id.split('-')[0]);
     //      alert('submit '+url);
           
        // submit the form 
            $(this).ajaxSubmit({
                type: "post",
                url: encodeURI(url),
                dataType: "json",
                data: {'id':workId},
                clearForm: true,
                success:function(rdata,status){
                    if (rdata.response==false) {//error somewhere
                        alert(rdata.message);
                    }else {
                        $.update(action,rdata);
                    }
                },
                error:function() {
                    alert("error posting the form");
                },
                complete:function() {
                    alert('complete');    
                }
            });
            //alert('after ajax'); 
            //event.preventDefault();
            return false; 
        });
    });
    
    

})(jQuery);
