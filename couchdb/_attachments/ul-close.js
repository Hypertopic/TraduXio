(function($) {
    $(document).ready(function() {
        $("li.foldable").click(function(e) {
            e.stopPropagation();
            $(this).toggleClass("closed");
        });
        $("li.foldable a").click(function(e) {e.stopPropagation();});
    });
}) (jQuery);
