
  $.fn.toggleName = function(name1, name2) {
    this.val(
      (this.val()==name1)? name2 : name1
    );
  }

  function find(version) {
    return $(".pleat.open[data-version='"+version+"']");
  }

  function findPleat(version) {
    return $(".pleat.close[data-version='"+version+"']");
  }

  $.fn.getHeight = function() {
    var fake=$("<div>").css({"position":"fixed","left":"-1000px"}).append(this.clone());
    $(document.body).append(fake);
    var height=fake.outerHeight();
    fake.remove();
    return height;
  }

  $.fn.rotate = function () {
    return $("<div>").addClass("rotated-text__wrapper").append(
      $("<div>").addClass("rotated-text").append(this)
    );
  }

  function addPleat(version) {
    var header=find(version).filter("th").first();
    var pleat=$("<td/>").addClass("pleat").addClass("close").attr("rowspan",$("tbody tr").length).attr("data-version",version);
    var language=header.find(".language").clone(true,true);
    language.attr("title",language.html()).html(language.data("id")).removeClass("expand");
    pleat.append(language.rotate());
    pleat.append(header.find(".creator").clone(true,true).rotate());
    find(version).filter("td").first().after(pleat);
    var pleatHead=$("<th/>").addClass("pleat").addClass("close").append(
       $("<div>").addClass("relative-wrapper").append(
            $("<span>").addClass("button show").html("Montrer")
       )
    ).attr("data-version",version);
    header.after(pleatHead.clone());
    var pleatFoot=$("<th/>").addClass("pleat").addClass("close").attr("data-version",version);
    find(version).last().after(pleatHead);
  }

  function findUnits(version) {
    return find(version).find(".unit");
  }

  function getVersions() {
    var versions=[];
    $("#hexapla .header tr:first-child .pleat.open").each(function() {
      versions.push($(this).data("version"));
    });
    return versions;
  }

  function getSize(unit) {
    var rowspan=unit.closest("td").attr("rowspan");
    if (rowspan) return parseInt(rowspan); 
    else return 1;
  }

  function positionSplits(context) {
    $("span.split",context).each(function() {
      var currTd=$(this).closest("td");
      var line=$(this).data("line");
      var position={};
      var tableLine=$("tr[data-line="+line+"]");
      if (tableLine.find("td:visible").length>0) {
	position=tableLine.find("td:visible").position();
        $(this).removeClass("dynamic");
      } else {
        $(this).addClass("dynamic");
      }
      var currPos=$(this).closest("td").position();
      $(this).css("top",(position.top-currPos.top-24)+"px");
    });
    positionDynamicSplits(context);
  }
  
  function positionDynamicSplits(context) {
    $("span.split.dynamic",context).each(function() {
      var unit=$(this).closest(".unit");
      var currTop=unit.position().top;
      var currLine=$(this).data("line");
      var startTop,endTop,startLine,endLine;
      var prev=$(this).prev(".split:not(.dynamic)");
      if (prev.length==1) {
        startTop=prev.position().top-currTop;
        startLine=prev.data("line");
      } else {
        startTop=0;
        startLine=unit.getLine();
      }
      var next=$(this).next(".split:not(.dynamic)");
      if (next.length==1) {
        endTop=next.position().top-currTop;
        endLine=next.data("line");
      } else {
        endTop=unit.height();
        endLine=unit.getLine()+getSize(unit);
      }
      var lineDiff=(currLine-startLine)/(endLine-startLine);
      var top=lineDiff*(endTop-startTop);
      $(this).css("top",(top-24)+"px");
    });
  }

  function positionPleats() {
    var closedPleats=$(".pleat.close:visible");
    //pleats positioning is done automatically with FF23 and Chromium 28
    //chromium has a bug, which requires to redraw the fixed elements
    closedPleats.children(":visible").redraw();
    return;
  }

  //http://forrst.com/posts/jQuery_redraw-BGv
  $.fn.redraw = function() {
    return this.hide(0, function(){$(this).show()});
  };

  function toggleShow(version) {
    find(version).toggle();
    findPleat(version).toggle();
    //when one version is edited, and we show a non edited one, pagination is ugly
    //so we toggle edited versions twice to get back to correct pagination
    //applying to both top and bottom buttons, so we do it twice
    find($(".unit.edit").getVersion("td.open")).find("input.edit").each(toggleEdit); 
    positionSplits();
  }

  $.fn.isEdited = function() {
    return this.find("textarea").length>0;
  }

  function htmlToString(unit) {
    return unit.html()
      .replace(/<br\/?>/g, "\n").replace("&lt;","<").replace("&gt;",">");
  }

  function stringToHtml(formattedString) {
     return formattedString.replace("<","&lt").replace(">","&gt;").replace(/\n$/,"\n ").replace(/\n/g, "<br>");
  }

  $.fn.getVersion = function(ancestor) {
    return this.closest(ancestor).data("version");
    return $(ancestor,$(this).closest("tr")).index($(this).closest(ancestor)) +1 ;
  }

  $.fn.getReference = function() {
    return {
      version: this.closest(".unit").data("version"),
      line: this.closest("tr").data("line")
    }
  }
  
  $.fn.getLanguage = function() {
    return find(this.getVersion("td.open")).find(".language").data("id");
  }
  
  $.fn.getLine = function() {
    return this.closest("tr").data("line");
  }

  function autoSize() {
    // Copy textarea contents; browser will calculate correct height of copy,
    // which will make overall container taller, which will make textarea taller.
    var text = stringToHtml($(this).val());
    $(this).parent().find("div.text").html(text);
    $(this).css({'width':'100%','height':'100%'});
  }
  
  function modified() {
    $(this).addClass("dirty");
    autoSize.apply(this);
    positionSplits($(this).closest(".unit"));
  }

  function toggleEdit () {
    var version=$(this).getVersion("th.open");
    find(version).find("input.edit").toggleName("Lire", "Editer");
    var units = findUnits(version);
    if (units.isEdited()) {
      find(version).first().css("width","auto");
      find(version).removeClass("edit");
    } else {
      find(version).addClass("edit");
      find(version).first().css("width",find(version).first().outerWidth()+"px");
    }
    units.each(function() {
      var unit=$(this);
      if ($(this).isEdited()) {
	var self=this;
	saveUnit.apply($(this).find('textarea'),[function () {
	  $(self).find(".text").html(stringToHtml($(self).find("textarea").val()));
	  unit.find(".split").remove();
	  unit.find(".join").remove();
	  unit.find("textarea").remove();
	  unit.removeClass("edit");
	}]);
      } else {
	$(this).addClass("edit").find("span").remove();
	var textarea=$("<textarea/>");
	textarea.val(htmlToString($(".text",this)));
	$(this).prepend(textarea);
	$(this).find(".text").css("min-height",(getSize(unit)*32)+"px");
	autoSize.apply(textarea);
	if (getVersions().indexOf(version)>0) {
	  createJoins(unit);
	  createSplits(unit);
	}
      }
    });
  }

  function getEndLine (units,index) {
    var nextIndex=index+1;
    var lastLine=0;
    if (nextIndex<units.length) {
      var nextUnit=units.eq(nextIndex);
      lastLine=nextUnit.getReference().line - 1 ;
    } else {
      lastLine=$("#hexapla").data("lines") - 1;
    }
    return lastLine;
    
  }
 
  function createJoin(unit1,unit2) {
      var p=($(unit2).offset().top-$(unit1).offset().top-$(unit1).outerHeight()+32)/(-2);
      var join=$("<span/>").addClass("join").attr("title","merge with previous").css("top",p+"px");
      unit2.prepend(join);
  }


  function createJoins(unit) {
    unit.find(".join").remove();
    var version=unit.getVersion("td.open");
    var units=findUnits(version);
    var currIndex=units.index(unit);
    if (currIndex>0) {
      var prevUnit=units.eq(currIndex-1);
      createJoin(prevUnit,unit);
    }
  }
  function createSplits(unit) {
    unit.find(".split").remove();
    var reference=unit.getReference();
    var version=reference.version;
    var currLine=reference.line;
    var units=findUnits(version);
    var currIndex=units.index(unit);
    var size=getSize(unit);
    var lastLine=currLine+size-1;
    var maxLines=$("#hexapla").data("lines");
    var currPos=unit.position();
    if (currLine<lastLine && currLine<maxLines) {
      for (var i=currLine+1; i<=lastLine; ++i) {
	var split=$("<span/>").addClass("split").attr("title","split line "+i).data("line",i);
	unit.append(split);
      }
      positionSplits();
    }
  }

  function unedit() {
    var self=this;
    saveUnit.apply(this,[function() {
       var unit=$(self).closest(".unit");
       unit.html(stringToHtml($(self).val())).removeClass("edit");
    }]);
  }

  function saveUnit(callback) {
    var self=this;
    if ($(this).hasClass("dirty")) {
      $(this).prop("disabled",true);
      var content=$(this).closest(".unit").find("textarea").val();
      editOnServer(content, $(this).getReference()).done(function(message,result) {
	if (result == "success") {
	  $(self).removeClass("dirty"); 
	  $(self).prop("disabled",false);
	  if (callback && typeof(callback) == "function") {
	     callback();
	  }
	} else {
	  alert(result+":"+message);
	}
      });
    } else {
      if (callback && typeof(callback) == "function") {
	 callback();
      }
    } 
  }

  function getPreviousUnit(unit) {
    var version=unit.getVersion("td.open");
    var units=findUnits(version);
    return $(units.eq(units.index(unit)-1));
  }

  var editOnServer = function(content, reference) {
    var id=$("#hexapla").data("id");
    return $.ajax({
      type: "PUT",
      url: "version/"+id+"?"+ $.param(reference),
      contentType: "text/plain",
      data: content
    });
  }

  $(document).ready(function() {

    $("#hexapla").on("click", ".button.hide", function() {
      //if ($("thead.header th.pleat.open:visible").length > 1) {
        toggleShow($(this).getVersion("th.open"));
      //}
    });

    $("#hexapla").on("click", ".button.show", function() {
      toggleShow($(this).getVersion("th.close"));
    });

    $("#hexapla").on("click", ".button.edit-license", function() {
      window.location=getPrefix()+"/license/"+$("#hexapla").data("id")+'/'+$(this).getVersion("th");
    });

    $("input.edit").on("click",toggleEdit);

    $("tr").on("select mouseup keyup",".unit", function (e) {
      //requires jquery.selection plugin
      var txt=$.selection();
      if (txt) {
	$("form.concordance #query").val(txt);
	var language=$(this).getLanguage();
	$("form.concordance #language").val(language);
      }
    });

    $("tr").on("click", ".join", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var version=unit.getVersion("td.open");
      var units=findUnits(version);
      var previousUnit=units.eq(units.index(unit)-1);
      if (previousUnit) {
	editOnServer("null", $(this).closest(".unit").getReference())
	  .done(function() {
            var previousContent=previousUnit.find("textarea").val();
            var thisContent=unit.find("textarea").val();
            previousUnit.find("textarea").val(previousContent+"\n"+thisContent);
            var thisLine=unit.getLine();
            var prevLine=previousUnit.getLine();
            var size=getSize(unit);
            var newSpan=thisLine-prevLine+size;
            previousUnit.closest("td").attr("rowspan",newSpan);
            previousUnit.find(".text").css("min-height",(newSpan*32)+"px");
            unit.closest("td").remove();
            createJoins(previousUnit);
            createSplits(previousUnit);
        });
      }
    });

    $.fn.setSize = function (size) {
      this.closest("td").attr("rowspan",size).find(".text").css("min-height",size*32+"px");
    }

    $("tr").on("click", ".split", function(e) {
      e.stopPropagation();
      var unit=$(this).closest(".unit");
      var line=$(this).data("line");
      var version=unit.data("version");
      editOnServer("", {
        version:version,
        line: line
      }).done(function() {
        var size=getSize(unit);
        var initialLine=unit.getLine();
        var newUnit=$("<div/>").append("<textarea>");
        var text=$("<div>").addClass("text");
        newUnit.append(text);
        autoSize.apply($("textarea",newUnit));
        newUnit.addClass("unit edit").attr("data-version",version);
        $(this).remove();
        var newTd=$("<td>").addClass("pleat open").attr("data-version",version).append(newUnit);
        newUnit.setSize(size-(line-initialLine));
        unit.setSize(line-initialLine);
        var versions=getVersions();
        var versionIndex=versions.indexOf(version);
        if (versionIndex==0) {
	  $("tr[data-line="+line+"]").prepend(newTd);
        } else {
	  var ok=false;
	  $("tr[data-line="+line+"] .unit").each(function() {
            var currVersion=$(this).data("version");
	    if (versions.indexOf(currVersion) > versions.indexOf(version)) {
	      $(this).closest("td").before(newTd);
	      ok=true;
	      return false;
	    }
	    if (versions.indexOf($(this).data("version")) +1 == versions.length) {
	      $(this).closest("td").before(newTd);
	    }
	  });
          if (!ok) {
	    $("tr[data-line="+line+"]").append(newTd);
          }
        }
        createJoins(unit);
        createSplits(unit);
        createJoins(newUnit);
        createSplits(newUnit);
        $(".tosplit").removeClass("tosplit");
      });
    });

    $("#hexapla").on('change input cut paste','textarea',modified);

    $("tr").on("focusout", ".unit.edit textarea", saveUnit);
    
    var versions=getVersions();
    const N = versions.length;
    for (var i = N-1; i>=0; i--) {
      addPleat(versions[i]);
    }
    for (var i = 2; i<N; i++) {
      toggleShow(versions[i]);
    }

  });


