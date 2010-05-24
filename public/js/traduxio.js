if (typeof console == "undefined") console={log:function(){}};

(function($) {

    tdxio.textSearch = {
        getSelText : function () {
            var txt = '';
            if (window.getSelection)
            {
                txt = window.getSelection();
            }
            else if (document.getSelection)
            {
                txt = document.getSelection();
            }
            else if (document.selection)
            {
                txt = document.selection.createRange().text;
            }
            if (typeof txt != "string") {
                if (typeof txt == 'undefined') {
                    txt="";
                } else {
                    txt=txt.toString();
                }
            }
            return txt;
        },

        getSegnum : function (id) {
            var arr=id.split('-');
            return arr[2];
        },
        manageSelection : function (e) {
            var txt=tdxio.textSearch.getSelText();
            if (txt != "" && txt!=tdxio.textSearch.currentSelection) {
                tdxio.textSearch.currentSelection=txt;
                $("#query-value").attr('value',txt);
                $("#concord-query").submit();
            }
        },
        getConcord : function() {
            if (typeof tdxio.textSearch.concord!='undefined') {
                tdxio.textSearch.concord.close();
            }
            tdxio.textSearch.concord = window.open("","concord") ;
        },
        translationModified : function (e) {
            tdxio.textSearch.modified=true;
        },

        allowSplit : function (e) {
            return !tdxio.textSearch.modified || confirm("Text modified but not save, are you sure you want to continue?");
        },

        currentSelection : '',
        //searching : false,
        modified : false
    };
    $(document).ready(function() {
        $("td.newblock").mouseup(tdxio.textSearch.manageSelection).keyup(tdxio.textSearch.manageSelection);
        $("input,textarea").change(tdxio.textSearch.translationModified);
        $("a").click(tdxio.textSearch.allowSplit);
        $("#concord-query").submit(tdxio.textSearch.getConcord);
    });

})(jQuery);
