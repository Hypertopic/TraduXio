if (typeof console == "undefined") console={log:function(){}};

(function($) {

    tdxio.textSearch = {
        filter_layout:function (data,type,div) {
            $.each(data.metadata[type],function (name,filter) {
                var ul=$('<ul class="filter"/>');
                ul.append('<li class="filter-title">'+data.metadatas[type][name]+'</li>').attr('name',name);
                $.each(filter,function (j,val) {
                    var li=$('<li class="filter-value"/>');
                    li.append(val).attr('name',j);
                    if (typeof data.filters[type]!='undefined' &&
                    data.filters[type][name] != 'undefined' &&
                    data.filters[type][name]===j) {
                        li.addClass("selected");
                    } else {
                        li.addClass("possible");
                    }
                    ul.append(li);
                });
                var t=$("li.filter-value",ul);
                if (t.length==1) t.addClass("alone").removeClass('possible');
                div.append(ul).attr('name',type).addClass('filters');
            });
            return div.addClass('filterscroll');
        },
        search : function () {
            if (!tdxio.textSearch.searching) {
                tdxio.textSearch.searching=true;
                $(document.body).css("cursor","wait");
                var url=tdxio.baseUrl+"/translation/search";
                var data={query:tdxio.textSearch.current.query,
                        returnStyle:'json'};
                if (typeof tdxio.textSearch.current.filters != 'undefined') {
                    var filters={};
                    var ok=false;
                    $.each(tdxio.textSearch.current.filters,function (name,filter) {
                        filters[name]=filter.join("/");
                        ok=true;
                    });
                    if (ok) {
                        $.extend(data,filters);
                    }
                }
                var filters=$("#filters");
                /*filters.empty();
                $("#results").empty().append('fetching results...');*/
                $.ajax({
                    type: "post",
                    url: url,
                    dataType: "json",
                    data: data,
                    success:function(data, status) {
                        if (data.results.length>0) {
                            var table=$("<table/>");
                            $.each(data.results,function (i,res) {
                                var tr=$("<tr/>").addClass('result');
                                if (i % 2==1) tr.addClass('altern');
                                var td_title=$('<td class="title">'+res['src_title']+
                                        "<br/>"+res['src_language']+
                                        "<br/>"+res['src_author']+
                                        "<br/>"+res['src_release']+
                                        "</td>");
                                var td_block=$('<td class="block">'+res['src_block']+"</td>");
                                if (res['src_language_rtl']=='1') {
                                    td_title.css('direction','rtl');
                                    td_block.css('direction','rtl');
                                }
                                tr.append(td_title);
                                tr.append(td_block);
                                var td_title=$('<td class="title">'+res['dest_title']+
                                        "<br/>"+res['dest_language']+
                                        "<br/>"+res['dest_author']+
                                        "<br/>"+res['dest_release']+
                                        "</td>");
                                var td_block=$('<td class="block">'+res['dest_block']+"</td>");
                                if (res['dest_language_rtl']=='1') {
                                    td_title.css('direction','rtl');
                                    td_block.css('direction','rtl');
                                }

                                tr.append(td_block);
                                tr.append(td_title);
                                if (res['reverse']==1) tr.addClass('reverse');
                                table.append(tr);
                                /*alert(res['src_title']+' '+res['src_language']+' '+res['src_language_rtl']
                                  +"\n"+res['dest_title']+' '+res['dest_language']+' '+res['dest_language_rtl']);
                                alert(tr.html());*/

                            });
                            filters.empty();
                            tdxio.textSearch.filter_layout(data,'orig',filters);
                            var filter_tr=$("<tr/>").append("<td/>").addClass('filters');
                            filters=$("<td/>");
                            tdxio.textSearch.filter_layout(data,'src',filters);
                            filter_tr.append(filters);
                            filters=$("<td/>");
                            tdxio.textSearch.filter_layout(data,'dest',filters);
                            filter_tr.append(filters).append("<td/>");
                            table.prepend(filter_tr);
                            $("#results").empty().append(table);
                            tdxio.ready();
                        } else {
                            $("#results").empty().append('no results for '+tdxio.textSearch.current.query);
                            $("#filters").empty();
                        }
                    },
                    error:function() {
                        alert("error getting result");
                    },
                    complete:function() {
                        tdxio.textSearch.searching=false;
                        $(document.body).css("cursor","");
                    }
                });
            }
        },

        filter : function (e) {
            var t = e.target;
            var filterfield='';
            var filtertype='';
            $.each($(t).parents(),function (i,par) {
                if ($(par).is("ul.filter")) {
                    filterfield=$(par).attr('name');
                }
                if ($(par).is(".filters")) {
                    filtertype=$(par).attr('name');
                }
                if (filterfield!='' && filtertype!='') return false;
            });
            var filtervalue=$(t).attr('name');
            if (filterfield!='' && filtertype!='' && filtervalue!='') {
                e.stopPropagation();
                filtertype=filtertype+"_filter";
                if (typeof tdxio.textSearch.current.filters[filtertype] == 'undefined') {
                    tdxio.textSearch.current.filters[filtertype]=[];
                }
                tdxio.textSearch.current.filters[filtertype][tdxio.textSearch.current.filters[filtertype].length]=filterfield+":"+filtervalue;
                console.log(filterfield+"="+filtervalue);
                tdxio.textSearch.search();
            }
        },

        resetFilter : function (e) {
            var t = e.target;
            var filterfield='';
            var filtertype='';
            $.each($(t).parents(),function (i,par) {
                if ($(par).is("ul.filter")) {
                    filterfield=$(par).attr('name');
                }
                if ($(par).is(".filters")) {
                    filtertype=$(par).attr('name');
                }
                if (filterfield!='' && filtertype!='') return false;
            });
            if (filterfield!='' && filtertype!='') {
                filtertype=filtertype+"_filter";
                e.stopPropagation();
                if (typeof tdxio.textSearch.current.filters[filtertype] != 'undefined') {
                    var newFilters=[];
                    $.each(tdxio.textSearch.current.filters[filtertype],function (i,v) {
                        var a=v.split(":");
                        if (a[0]!=filterfield) {
                            newFilters[newFilters.length]=v;
                        }
                    });
                    tdxio.textSearch.current.filters[filtertype]=newFilters;
                    tdxio.textSearch.search();
                }
            }
        },

        ready : function() {
            $("li.filter-value.possible").click(tdxio.textSearch.filter);
            $("li.filter-value.selected").click(tdxio.textSearch.resetFilter);
        },

        current:{query:'',filters:{}},
        searching : false,
        submit : function (e) {
            var query=$("#query-value").attr('value');
            if (!tdxio.textSearch.searching && query != '' & query!=tdxio.textSearch.currentQuery) {
                tdxio.textSearch.current.filters={};
                if ($("#form-query input[name=srclang]").attr('value')!='') {
                    tdxio.textSearch.current.filters.src_filter=["language:"+$("#form-query input[name=srclang]").attr('value')];
                }
                if ($("#form-query input[name=destlang]").attr('value')!='') {
                    tdxio.textSearch.current.filters.dest_filter=["language:"+$("#form-query input[name=destlang]").attr('value')];
                }
                tdxio.textSearch.current.query=query;
                tdxio.textSearch.search(query);
            }
            return false;
        }

    };
    tdxio.addReady(tdxio.textSearch.ready);
    $(document).ready(function() {
        $("#form-query").bind('submit',tdxio.textSearch.submit);
        tdxio.textSearch.submit();
    });

})(jQuery);