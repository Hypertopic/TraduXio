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
        tdxio.page.setState('reset');
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
        tdxio.page.resetHeight();
        tdxio.page.resize();
        
        $.scrollTo($('#insert-text'));
        //  alert('transform '+action+' resized');
        $.setBlocked(true);
    };
    
    $.updateTranslation = function(translation){
        window.translations[0] = data.translation;
        window.ajaxData.work.Interpretations.each(function(index,el){if(el.work.id==window.trId)window.ajaxData.work.Interpretations[index]=data.translation;});
        tdxio.page.displayWork(window.ajaxData,window.trId,window.back,window.begin,window.end);
        tdxio.page.setState('edit');
        tdxio.page.adjust();
        tdxio.page.resize();
    };
    
    $.update = function(action,data){
        alert('update '+action);
        switch(action)
        {
        case 'extend':
            $('form#extend-form').remove();
            var lastId = $("#work div.text span:last")[0].id;
          //  alert(lastId);
            var newId = lastId;
            newId = newId.replace(/\d+$/,parseInt(lastId.match(/\d+$/))+1);
            $('#'+lastId).after("<span id='"+newId+"'>"+data.addedtext+"</span>" );

            //append(data.addedtext);
            break;
        case 'cut':
            
            $.updateTranslation(data.translation);
            var segId = data;
            $('#'+segId).after('test');
        break;
        case 'merge':
            var segId = data;
            $('#'+segId).after('test');
          break;
        case 'translate':
          break;
        default:
          
        }
        $("#work div.text").height("");
        tdxio.page.resize();
        $.setBlocked(false);
    }
    
    $.showBlocks = function(show){
        if(show){
            $('.text').css('border','none');
            
        }else{
            $('.text').css('border','');
            
        }
      /*  window.ajaxData
        window.trId
        window.begin
        window.end*/
        
    }
    
    $(document).ready(function() {
        var url;
        var action;
        var idstr = document.location.pathname.match(/\/id\/\d+/);
        var workId = parseInt(idstr[0].match(/\d+/));
        
        url=encodeURI(tdxio.baseUrl+"/translation/save");
        
        $('#translation div.text .block.show.editable').live('hover',function(){$(this).editable(url,{ 
            submitdata: {'id': window.trId},
            type      : 'textarea',
            cancel    : 'Cancel',
            submit    : 'Save',
            indicator : $(this).html(),
            tooltip   : 'Click to edit...',
            intercept : function (jsondata) {
                obj = jQuery.parseJSON(jsondata);
                if(obj.response!=null && obj.response===false)
                    {alert(obj.message);return;}
                // do something with obj.status and obj.other
                return(obj.result);
            }
        });});
       
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
                    //alert('complete');    
                }
            });
            //alert('after ajax'); 
            //event.preventDefault();
            return false; 
        });
        
        $("#editbtn").click(function(){
            $('#icons div').toggleClass('on');
            var active = $(this).attr('class')=='on';
            $('.text').toggleClass('show');
            $('.block').toggleClass('show');
            tdxio.page.setState(active?'edit':'reset');
            $.setBlocked(active);
            tdxio.page.resetHeight();
            tdxio.page.adjust();
            tdxio.page.resize();
        });
        
        
        $(".merge , .cut").live('click',function(){
            var op = $(this).attr('class');
            alert(op);
            var segId = (op=='merge')?$(this).prev('span.block').children()[$(this).prev('span.block').children().length-1].id:$(this).prev('span.segment').attr('id'); 
            var after = segId.split('segment')[1];
            $.ajax({
                type:"get",
                url:encodeURI(tdxio.baseUrl+"/translation/ajax"+op),
                data: {'id':window.trId,'after':after},
                dataType: "json",
                success: function(rdata,status){
                    if (rdata.response==false) {
                        alert(rdata.message);
                    }else{
                        $.update(op,segId);
                    }
                },
                error: function() {
                    alert("Error merging");
                }
            });
        });
            
    });
    
    

})(jQuery);
