if (typeof console == "undefined") console={log:function(){}};

(function($) {
    tdxio = {
        readyFn : [],
        ready : function() {
            $.each(tdxio.readyFn,function(i,fn) {
                fn();
            });
        },
        addReady : function (fn) {
            if (typeof fn =='function') {
                tdxio.readyFn[tdxio.readyFn.length]=fn;
            }
        }
    };

    $(document).ready(tdxio.ready);
}) (jQuery);