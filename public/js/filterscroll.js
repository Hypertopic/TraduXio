if (typeof console == "undefined") console={log:function(){}};

(function($) {
    tdxio.filterScroll = {
        init : function (i,e) {
            var $e=$(e);
            $e.children().hide();
            $e.children(":first").show();
            $e.prepend('<div class="scrollbutts"><div class="button up"></div><div class="button down"></div></div>');
            tdxio.filterScroll.updateButtons($e);
        },
        registerButtons : function(filterScroll) {
            $(".scrollbutts .button",filterScroll).unbind('click');
            $(".scrollbutts .up:not(.disabled)",filterScroll).click(tdxio.filterScroll.up);
            $(".scrollbutts .down:not(.disabled)",filterScroll).click(tdxio.filterScroll.down);
        },
        up : function (e) {
            var filterScroll=$(e.target).parent().parent();
            var current=tdxio.filterScroll.getCurrent(filterScroll);
            $(filterScroll).children(":not(.scrollbutts)").hide();
            if (current.prev(":not(.scrollbutts)").length>0) {
                current.prev(":not(.scrollbutts)").show();
            } else {
                $(filterScroll).children(":not(.scrollbutts):last").show();
            }
            tdxio.filterScroll.updateButtons(filterScroll);
        },
        down : function (e) {
            var filterScroll=$(e.target).parent().parent();
            var current=tdxio.filterScroll.getCurrent(filterScroll);
            $(filterScroll).children(":not(.scrollbutts)").hide();
            var new_cur;
            if (current.next(":not(.scrollbutts)").length>0) {
                new_cur=current.next(":not(.scrollbutts)").show();
            } else {
                new_cur=$(filterScroll).children(":not(.scrollbutts):first").show();
            }
            tdxio.filterScroll.updateButtons(filterScroll);
        },
        updateButtons : function(filterScroll) {
            var current=tdxio.filterScroll.getCurrent(filterScroll);
            if (current.next(":not(.scrollbutts)").length==0) {
                $(".button.down",filterScroll).addClass("disabled");
            } else {
                $(".button.down",filterScroll).removeClass("disabled");
            }
            if (current.prev(":not(.scrollbutts)").length==0) {
                $(".button.up",filterScroll).addClass("disabled");
            } else {
                $(".button.up",filterScroll).removeClass("disabled");
            }
            tdxio.filterScroll.registerButtons(filterScroll);
        },
        getCurrent : function (filterScroll) {
            return $(filterScroll).children(":not(.scrollbutts):visible");
        },
        ready : function (e) {
            $(".filterscroll").each(tdxio.filterScroll.init);
        }
    };

    tdxio.addReady(tdxio.filterScroll.ready);

    $(document).ready(function() {
    });
}) (jQuery);
