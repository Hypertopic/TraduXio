
  $.fn.toggleName = function(name1, name2) {
    this.text(
      (this.text()==name1)? name2 : name1
    );
  }

  function find(parentType, translationNumber) {
    return $("tr").find(parentType + ":nth-child(" + translationNumber + ")");
  }

  function findPleat(parentType, translationNumber) {
    var offset=translationNumber + translationsNumber();
    return $("tr").find(parentType + ":nth-child(" + offset + ")");
  }

  function addPleat(translationNumber) {
    var pleat=$("<td/>").addClass("pleat").addClass("close").attr("rowspan",$("tbody tr").length).html(find("th.pleat.open" ,translationNumber).html());
    $("table tbody tr:first-child").append(pleat);
    var pleatRound=$("<th/>").addClass("pleat").addClass("close");
    $("thead tr").append(pleatRound);
  }

  function findUnits(translationNumber) {
    return find("td", translationNumber).find(".unit");
  }

  function translationsNumber() {
    return $("#hexapla .header tr:first-child .pleat.open").length;
  }

  function positionPleats() {
    var closedPleats=$(".pleat.close:visible");
    //pleats positioning is done automatically with FF23 and Chromium 28
    //chromium has a bug, which requires to redraw the fixed elements
    closedPleats.children(":visible").redraw();
    return;
    //in case other browsers needs to be said the position of pleats, the following code does it.
    closedPleats.each(function() {
      //var offset=$(this).nextAll(":visible").length;
      var offset=(closedPleats.length-closedPleats.index(this)-1);
      $(this).children(":visible").css("right",offset * 3+"em");
    });
  }

  //https://coderwall.com/p/ahazha
  //does not work
  $.fn.redraw = function(){
    $(this).each(function(){
      var redraw = this.offsetHeight;
    });
  };

  //https://gist.github.com/hdragomir/740199
  $.fn.redraw = function(){
      return $(this).each(function(){
	  var n = document.createTextNode(' ');
	  $(this).append(n);
	  setTimeout(function(){n.parentNode.removeChild(n)}, 0);
      });
  };

  //http://forrst.com/posts/jQuery_redraw-BGv
  $.fn.redraw = function() {
    return this.hide(0, function(){$(this).show()});
  };

  function toggleShow(translationNumber) {
    find("td",translationNumber).toggle();
    findPleat("td",translationNumber).toggle();
    find("th",translationNumber).toggle();
    findPleat("th",translationNumber).toggle();
    positionPleats();
  }

  $.fn.isEdited = function() {
    return this.find("textarea").length>0;
  }

  function htmlToString(unit) {
    return unit.html()
      .replace(/<br\/?>/g, "\n").replace("&lt;","<").replace("&gt;",">");
  }

  function stringToHtml(formattedString) {
     return formattedString.replace("<","&lt").replace(">","&gt;").replace(/\n/g, "<br>");
  }

  $.fn.getTranslationNumber = function(ancestor) {
    return this.closest(ancestor).index() + 1;
  }

  $.fn.getReference = function() {
    return {
      version: this.data("version"),
      line: this.data("line")
    }
  }

  $(document).ready(function() {

    $("#hexapla").on("click", ".pleat.open .button", function() {
      toggleShow($(this).getTranslationNumber("th"));
    });

    $("#hexapla").on("click", ".pleat.close .button", function() {
      toggleShow($(this).getTranslationNumber("th")-translationsNumber());
    });

    var getEndLine=function (units,index) {
      var nextIndex=index+1;
      var lastLine=0;
      if (nextIndex<units.length) {
	var nextUnit=units.eq(nextIndex);
	lastLine=nextUnit.data("line") - 1 ;
      } else {
	lastLine=$("#hexapla").data("lines") - 1;
      }
      return lastLine;
      
    }

    var createJoin=function(unit1,unit2) {
        var p=($(unit2).offset().top-$(unit1).offset().top-$(unit1).outerHeight()+32)/(-2);
        var join=$("<span/>").addClass("join").attr("title","merge with previous").css("top",p+"px");
        unit2.prepend(join);
        join.on("mouseleave",function(){$(".unit").removeClass("tomerge");});
    }

    var highlightLines = function() {
      downlightLines(); 
      var transNum=$(this).getTranslationNumber("td");
	var units=findUnits($(this).getTranslationNumber("td"));
        var unit=$(this).closest(".unit");
        unit.addClass("active");
	var currLine=unit.data("line");
	var currIndex=units.index(unit);
        var lastLine=getEndLine(units,currIndex);
      if (currIndex>0) {
        var prevUnit=units.eq(currIndex-1);
        createJoin(prevUnit,unit);
      }
      if (currIndex<units.length-1) {
        var nextUnit=units.eq(currIndex+1);
        createJoin(unit,nextUnit);
      }
      unit.find(".split").remove();
      var maxLines=$("#hexapla").data("lines");
      if (currLine<lastLine && currLine<maxLines) {
        for (var i=currLine+1; i<=lastLine; ++i) {
	  var split=$("<span/>").addClass("split").attr("title","split").data("line",i);
          //var offset=0.5*(i-currLine);
          //split.css("right","-"+offset+"em").css("z-index",99);
	  unit.append(split);
        }
      }
      for (var i=1;i<=translationsNumber();i++) {
        if (i!=transNum) {
	  var origUnits=findUnits(i);
	  origUnits.each(function(index) {
	    var startLine=$(this).data("line");
            var endLine=getEndLine(origUnits,index);
            if (startLine==lastLine && endLine==currLine) {
	      $(this).addClass("highlight");
            } else if (startLine >=currLine && endLine <= lastLine) { //included
	      $(this).addClass("highlight").addClass("included");
            } else if (endLine>=currLine && startLine <= lastLine) { //overlaped
	      $(this).addClass("highlight").addClass("partial");
            }
	  });
        }
      }
    }
    
    var downlightLines = function () {
      $(".unit").removeClass("highlight partial included active").find(".join").remove().end().find(".split").remove();
    }

    $("#hexapla").on({ mouseenter:highlightLines,"mouseleave":function(){$(".unit.tomerge").removeClass("tomerge");}},".unit:not(.edit)");
    $("#hexapla").on("dblclick",".unit",function() {
        $(".unit").has("textarea").each(unedit);
        $(this).addClass("edit").find("span").remove();
        var textarea=$("<textarea/>");
        textarea.html(htmlToString($(this))).
          css({
            "height":($(this).outerHeight())+"px"
           ,"width":$(this).width()+"px"
          });
	$(this).empty().append(textarea);
        textarea.focus();
     });

    $(".edit").on("click", function() {
      $(this).toggleName("Lire", "Éditer");
      var units = findUnits($(this).getTranslationNumber("th"))
      units.each(function(index) {
        if ($(this).isEdited()) {
          $(this).html(stringToHtml($(this).find("textarea").val()));
        } else {
          $(this).html("<textarea>" + htmlToString($(this)) + "</textarea>");
          if (index!=0) {
            $(this).prepend('<button class="join">X</button>');
          }
          var current_line = $(this).data("line")+1;
          var lines = $("#hexapla").data("lines");
          if (current_line<lines) {
            var next_unit = units.eq(index+1);
            var max_lines_to_split = (next_unit.length==0)? lines : next_unit.data("line");
            for (var i = current_line; i<max_lines_to_split; i++) {
              $(this).append(
                '<button class="split" data-line="' + i + '">+</button>'
              );
            }
          }
        }
      });
    });

    var editOnServer = function(content, reference) {
      var id=$("#hexapla").data("id");
      return $.ajax({
        type: "PUT",
        url: "version/"+id+"?"+ $.param(reference),
        contentType: "text/plain",
        data: content
      });
    }
   
    var mergeRows=function(tr1,tr2) {
      var tds1=$(tr1).find("td");
      var tds2=$(tr2).find("td");
      for (var i=0;i<translationsNumber();i++) {
        tds1.eq(i).append(tds2.eq(i).html());
      }
      tr2.remove();
    }

    var getPreviousUnit=function(unit) {
      var translationNumber=unit.getTranslationNumber("td");
      var units=findUnits(translationNumber);
      return $(units.eq(units.index(unit)-1));
    }

    $("#hexapla").on({
      "mouseleave":function(e) {
        e.stopPropagation();
        downlightLines();
        var unit=$(this).closest(".unit");
        unit.removeClass("tomerge");
	getPreviousUnit(unit).removeClass("tomerge");
        $("#hexapla").find(".join").remove();
      },
      mouseenter:function(e) {
        e.stopPropagation();
        var unit=$(this).closest(".unit");
        unit.addClass("tomerge");
	getPreviousUnit(unit).addClass("tomerge");
      }
    },".join");
    
    $("#hexapla").on({mouseenter:function(e) {
        e.preventDefault();
        e.stopPropagation();
        var unit=$(this).closest(".unit");
        $(".unit").not(unit).find(".split").remove();
        var line=$(this).data("line");
        for (var i=1;i<translationsNumber();i++) {
          
        }
        $(".unit[data-line="+line+"]").addClass("tosplit");
      },
      "mouseleave":function() {
        var line=$(this).data("line");
        $(".unit[data-line="+line+"]").removeClass("tosplit");
      }
    },".split");


    $("tr").on("click", ".join", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var translationNumber=unit.getTranslationNumber("td");
      editOnServer("null", $(this).closest(".unit").getReference())
        .done(function() {
          var units=findUnits(translationNumber);
          var previousUnit=units.eq(units.index(unit)-1);
          if (previousUnit) {
            var thisRow=unit.closest("tr");
            var previousRow=previousUnit.closest("tr");
            previousUnit.append(" "+unit.html()).removeClass("tomerge");
            $(".join").remove();
	    unit.remove();
            if (!previousRow.is(thisRow)) {
              mergeRows(previousRow,thisRow);
            }
          }
        });
    });

    $("tr").on("click", ".split", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var line=$(this).data("line");
      var version=unit.data("version");
      editOnServer("", {
        version:version,
        line: line
      }).done(function() {
        var newUnit=$("<div/>");
        unit.after(newUnit);
        newUnit.addClass("unit").data("line",line).data("version",version);
        unit.find(".split").remove();
        $(".tosplit").removeClass("tosplit");
      });
    });

    var unedit=function() {
      var unit=this;
      var content=$(unit).find("textarea").val();
      editOnServer(content, $(unit).getReference()).done(function() {
         $(unit).html(stringToHtml(content)).removeClass("edit");
      });
    }

    $("tr").on("focusout", ".unit", unedit);

    const N = $("thead.header th.pleat.open").length;
    for (var i = 1; i<=N; i++) {
      //addPleat(i);
    }
    for (var i = 3; i<=N; i++) {
      toggleShow(i);
    }

  });


