(function($) {
    tdxio.autoGrow = {
        update : function (force) {
            if (force || !tdxio.autoGrow.blocked && this.oldValue != this.value) {
                this.oldValue=this.value;
                var html=this.value.replace(/\n/g,"<br/>")+"<br/>";
                $("#autogrow_shadow").html(html);
                var h1=$("#autogrow_shadow").height();
                if ($(this).is(".mini")) {
                    var nbrow=$(this).attr('rows');
                    html=new Array(nbrow+1).join('<br/>');
                    $("#autogrow_shadow").html(html);
                    var h1b=$("#autogrow_shadow").height();
                    h1=Math.max(h1,h1b);
                }
                var block=$(".block",$(this).parents().filter('tr'));
                var h2=block.height();
                var h=Math.max(h1,h2);
                //if (h==h2) h-=3;
                $(this).css('height',h+'px');
                if (!force) tdxio.autoGrow.block();
            }
        },
        resize : function() {
            var w=$("textarea.autogrow").width()-10;
            if (w!=tdxio.autoGrow.current_width) {
                if (!tdxio.autoGrow.blocked) {
                    tdxio.autoGrow.resize_pending=true;
                    tdxio.autoGrow.block();
                    $("#autogrow_shadow").css('width',w);
                    tdxio.autoGrow.current_width=w;
                    $("textarea.autogrow").each(tdxio.autoGrow.update,[true]);
                    tdxio.autoGrow.resize_pending=false;
                } else {
                    if (!tdxio.autoGrow.resize_pending) {
                        window.setTimeout("tdxio.autoGrow.resize();",500);
                        tdxio.autoGrow.resize_pending=true;
                    }
                }
            }
        },
        block : function () {
            tdxio.autoGrow.blocked=true;
            window.setTimeout("tdxio.autoGrow.blocked=false;",500);
        },
        blocked : false,
        resize_pending:false,
        current_width:0,
        focus:function() {
            $(this).addClass("focus");
        },
        blur:function() {
            $(this).removeClass("focus");
        }
    };

    $(document).ready(function() {
        $(document.body).append('<div id="autogrow_shadow"></div>');
        tdxio.autoGrow.resize();
        $("textarea.autogrow").keyup(tdxio.autoGrow.update).click(tdxio.autoGrow.update).change(tdxio.autoGrow.update,[true]);
        $(window).resize(tdxio.autoGrow.resize);
        $("textarea,input").focus(tdxio.autoGrow.focus);
        $("textarea,input").blur(tdxio.autoGrow.blur);
        if((document.location.href).indexOf("#")!=-1) {
            //var position=document.location.href.substring(document.location.href.indexOf("#")+1,document.location.href.length);
            document.location.href = document.location.href;
            
            //document.location.href = document.location.href.substring(0,(document.location.href).indexOf("#"));
        }
        
    });

})(jQuery);
