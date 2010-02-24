(function($) {
    $(document).ready(function() {
        $("li.closed").click(function(e) {
            $(e.target).toggleClass("closed").toggleClass("open");
        });
        $("li.closed ul").click(function(e) {return $(e.target).is('a');});
    });
}) (jQuery);