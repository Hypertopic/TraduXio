
  $.fn.toggleName = function(name1, name2) {
    this.val(
      (this.val()==name1)? name2 : name1
    );
  };
  
  $.fn.toggleText = function(text1, text2) {
    this.text(
      (this.text()==text1)? text2 : text1
    );
  };

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
  };

  $.fn.rotate = function () {
    return $("<div>").addClass("rotated-text__wrapper").append(
      $("<div>").addClass("rotated-text").append(this)
    );
  };

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
  
  function fixWidths() {
   var nbOpen=$("thead:first-child tr:first-child th.pleat.open:visible").length;
    if (nbOpen==0) {
      $("#hexapla").removeClass("full");
    } else {
      $("#hexapla").addClass("full");
      $("thead:first-child tr:first-child th.pleat.open:visible").css("width",100/nbOpen+"%");
    }

  }

  function toggleShow(version) {
    find(version).toggle();
    findPleat(version).toggle();
    fixWidths();
    //when one version is edited, and we show a non edited one, pagination is ugly
    //so we toggle edited versions twice to get back to correct pagination
    //applying to both top and bottom buttons, so we do it twice
    find($(".unit.edit").getVersion("td.open")).find("input.edit").each(toggleEdit); 
    positionSplits();
  }

  $.fn.isEdited = function() {
    return this.hasClass("edit");
  };

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
  };

  $.fn.getReference = function() {
    return {
      version: this.closest(".unit").data("version"),
      line: this.closest("tr").data("line")
    };
  };
  
  $.fn.getLanguage = function() {
    return find(this.getVersion("td.open")).find(".language").data("id");
  };
  
  $.fn.getLine = function() {
    return this.closest("tr").data("line");
  };

  function autoSize() {
    // Copy textarea contents; browser will calculate correct height of copy,
    // which will make overall container taller, which will make textarea taller.
    var text = stringToHtml($(this).val());
    $(this).parent().find("div.text").html(text);
    if ($(this).parents().is("box-wrapper")) {
        $(this).css({'width':'100%','height':'100%'});
    }
  }
  
  function modified() {
    $(this).addClass("dirty");
    if ($(this).is(".autosize")) {
      autoSize.apply(this);
      positionSplits($(this).closest(".unit"));
    }
  }

  function toggleEdit () {
    var version=$(this).getVersion("th.open");
	var doc = find(version);
    var units = findUnits(version);
	var top = doc.first();
	var edited = doc.isEdited();
    doc.find("input.edit").toggleName("Lire", "Editer");
    if (edited) {
      top.css("width","auto");
      doc.removeClass("edit");
	  top.find("input.editedMeta").remove();
	  top.find(".delete").remove();
      if (getVersions().length==1) {
          var fulltext=$("textarea.fulltext").val();
          var lines=fulltext.split("\n\n");
            var id=$("#hexapla").data("id");
            var update=function(){
                $("#hexapla tbody tr").remove();
              lines.forEach(function(line,i) {
                var newUnit=$("<div/>");
                var text=$("<div>").addClass("text dirty");
                text.html(stringToHtml(line)).removeClass("dirty");
                newUnit.append(text);
                newUnit.addClass("unit").attr("data-version",version);
                var newTd=$("<td>").addClass("pleat open").attr("data-version",version).append($("<div>").addClass("box-wrapper").append(newUnit));
                newUnit.setSize(1);
                var tr=$("<tr/>").data("line",i).prepend(newTd);
                $("#hexapla tbody").append(tr);
              });
            };
            if ($("textarea.fulltext").is(".dirty")) {
                $.ajax({
                    type:"PUT",
                    data:JSON.stringify({key:"text",value:lines}),
                    contentType: "text/plain",
                    url:"work/"+id+"?version="+version
                }).done(update);
            } else {
                update();
            }
      }
    } else {
      doc.addClass("edit");
      top.css("width",doc.first().outerWidth()+"px");
	  if(version != "original") {
		top.find(".relative-wrapper").prepend('<span class="button delete"></span>');
		top.find(".delete").on("click", clickDeleteVersion);
	  }
      if (getVersions().length==1) {
          var fulltext="";
          var first=true;
          units.each(function() {
              if (first) first=false;
              else fulltext+="\n\n";
              fulltext+=htmlToString($(".text",this));
          });
          $("#hexapla tbody tr").remove();
          var textarea=$("<textarea/>").addClass("fulltext").val(fulltext);
          var tr=$("<tr/>").append($("<td/>").append($("<div>").addClass("unit edit").append(textarea)));
          $("#hexapla tbody").append(tr);
      }
    }
	setEditState(edited, top, "title", "Titre");
	setEditState(edited, top, "work-creator", "Auteur");
	if(version != "original")
	  setEditState(edited, top, "creator", "Traduction");
	setLangEditState(edited, top);
	setEditState(edited, top, "date", "Année");
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
		var textarea=$("<textarea/>").addClass("autosize");
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

  var languages=null;

  function fillLanguages(control,callback) {
    function updateSelect() {
      $.each(languages, function(key, o) {
        control.append("<option value=\""+key+"\">" + key + " (" + o.fr + " - " + o.en + " - " + o[key] + ")</option>");
      });
      if (typeof callback=="function")
          callback();
    };
    if (!languages) {
      $.getJSON(getPrefix() + "/shared/languages.json", function(result) {
        languages=result;
        updateSelect();
      }).fail(function() { alert("Cannot edit language field"); });;
    } else {
      updateSelect();
    }
  }
  
  function setLangEditState(isEdited, container) {
	var target = container.find(".language");
	if(isEdited) {
	  container.find("select").remove();
	  target.removeClass("edit").show();
	} else {
	  target.hide();
	  var language = $("<select></select>");
	  fillLanguages(language, function() {
		language.val(target.data("id"));
		language.addClass("editedMeta").css("width", "50%");
		language.on("change", function() {
		  var id = $("#hexapla").data("id");
		  var ref = $(this).closest("th").data("version");
		  $.ajax({
		    type: "PUT",
		    url: "work/"+id+"/"+ref,
		    contentType: 'text/plain',
		    data: JSON.stringify({"key":"language", "value": language.val()})
		  }).done(function() {
			var lang = language.find("option:selected").text();
			var lang_id = lang.substring(0, 2);
			var lang_text = lang.substring(4).split("-")[0];
			target.data("id", lang_id).attr("data-id", lang_id);
			target.text(lang_text);
			$("#hexapla").find(".close[data-version='" + ref + "']").find(".language")
			  .attr("data-id", lang_id).data("id", lang_id).attr("title", lang_text).html(lang_id);
			}).fail(function() { alert("fail!"); });
		});
		target.addClass("edit");
		target.before(language);
	  });
	}
  }
  
  function setEditState(isEdited, container, name, placeholder) {
	setEditStateForComponent(isEdited, container, name, "focusout", '<input type="text" class="editedMeta ' + name + '" />', placeholder);
  }
  
  function setEditStateForComponent(isEdited, container, name, event, textComponent, placeholder) {
	var target = container.find("." + name);
	if(isEdited) {
	  target.removeClass("edit").show();
	} else {
	  target.addClass("edit");
	  var component=$(textComponent);
	  component.attr("placeholder", placeholder);
    component.attr("title", placeholder);
	  component.on(event, function() {
		if(component.hasClass("dirty")) {
		  var id = $("#hexapla").data("id");
		  var ref = $(this).closest("th").data("version");
		  $.ajax({
		    type: "PUT",
		    url: "work/"+id+"/"+ref,
		    contentType: 'text/plain',
		    data: JSON.stringify({"key": name, "value": component.val()})
		  }).done(function(data) {
			if(name == "creator") {
			  changeVersion(ref, data);
			}
			component.val(data);
			target.text(data);
			component.removeClass("dirty");
		  }).fail(function() { alert("fail!"); });
		}
	  });
	  component.val($(target).text());
	  target.before(component);
	  target.hide();
	}
  }
  
  function changeVersion(oldVersion, newVersion) {
	$("#hexapla").find("*[data-version='" + oldVersion + "']").attr("data-version", newVersion).data("version", newVersion).find(".creator").html(newVersion);
  }
  
  function toggleAddVersion() {
	$("#addPanel").slideToggle(200);
	$("#removePanel").slideUp(200);
  }
  
  function toggleRemoveDoc() {
	$("#removePanel").slideToggle(200);
	$("#addPanel").slideUp(200);
  }
  
  function addVersion() {
	var id = $("#hexapla").data("id");
	var ref = $("#addPanel").find("input[type='text']").val();
	if(ref != "") {
	  $.ajax({
		type: "PUT",
		url: "work/"+id+"/"+ref,
		contentType: 'text/plain',
		data: JSON.stringify({"key": "creator", "value": ref})
	  }).done(function() {
		window.location.href = id + "?edit=" + ref;
	  }).fail(function() { alert("fail!"); });
	}
	return false;
  }
  
  function removeDoc() {
	if(confirm("La suppression est irréversible. Continuer ?")) {
	  $.ajax({
		type: "PUT",
		url: "work/"+$("#hexapla").data("id"),
		contentType: 'text/plain',
		data: JSON.stringify({"key": "remove"})
	  }).done(function() {
		window.location.href = "./";
	  }).fail(function(error) { alert("failed: " + error.statusText); });
	}
  }
  
  function clickDeleteVersion() {
	var ref = $(this).closest("th").data("version");
	if(confirm("Supprimer la traduction '" + ref + "' ?")) {
	  deleteVersion(ref);
	}
  }
  
  function deleteVersion(version) {
	var id = $("#hexapla").data("id");
	$.ajax({
	  type: "PUT",
	  url: "work/"+id+"/"+version,
	  contentType: 'text/plain',
	  data: JSON.stringify({"key": "delete"})
	}).done(function() {
	  window.location.reload(true);
	}).fail(function() { alert("fail!"); });
  }
  
  function openEditedVersions() {
	var version = $("#hexapla").find(".edited").last();
	var ref = version.closest("th").data("version");
	find(ref).show();
    findPleat(ref).hide();
    find($(".unit.edit").getVersion("td.open")).find("input.edit").each(toggleEdit);
    positionSplits();
	version.find(".edit").click();
	version.removeClass("edited");
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
  };

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
      window.location=getPrefix()+"/works/license/"+$("#hexapla").data("id")+'/'+$(this).getVersion("th");
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
      this.closest("td").attr("rowspan",size).find(".text").css("min-height",size*40+"px");
    };

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
        var newUnit=$("<div/>").append($("<textarea>").addClass("autosize"));
        var text=$("<div>").addClass("text");
        newUnit.append(text);
        autoSize.apply($("textarea",newUnit));
        newUnit.addClass("unit edit").attr("data-version",version);
        $(this).remove();
        var newTd=$("<td>").addClass("pleat open").attr("data-version",version).append($("<div>").addClass("box-wrapper").append(newUnit));
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

    $("#hexapla").on('change input cut paste','textarea,input.editedMeta',modified);

    $("tr").on("focusout", ".unit.edit textarea", saveUnit);
	
	$(".top").on("click", ".addVersion", toggleAddVersion);
	$(".top").on("click", ".removeDoc", toggleRemoveDoc);
	
	$("#addPanel").on("submit", addVersion);
	$("#removePanel").on("click", removeDoc);
    
    var versions=getVersions();
    const N = versions.length;
    for (var i = N-1; i>=0; i--) {
      addPleat(versions[i]);
    }
    for (var i = 2; i<N; i++) {
      toggleShow(versions[i]);
    }
    openEditedVersions();
    fixWidths();
	
    if(N==0) {
      $("#work-info").show().on("submit",function(e) {
        e.preventDefault();
        var data={};
        ["work-creator","language","title","date"].forEach(function(field) {
          data[field]=$("[name='"+field+"']","#work-info").val();
        });
        data.original=$("[name=original-work]").prop("checked");
        $.ajax({
          type:"POST",
          url:"work",
          data:JSON.stringify(data),
          contentType:"application/json",
          dataType:"json"
        }).done(function(result) {
          if (result.ok && result.id) {
            window.location.href=result.id;
          } else {
            alert("fail");
          }
        }).fail(function(){alert("fail");});
        return false;
      });

      fillLanguages($("#work-info [name=language]"));
      $(".top h1,img.removeDoc,img.addVersion").hide();
    }
    if (N==1) {
      $(".button.hide").remove();
    }

  });


