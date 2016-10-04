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

function request(options) {
  return $.ajax(options)
    .retry({times:3,statusCodes:[0,409]})
    .fail(function () {
      alert("request failed");
    });
}

function find(version) {
  return $(".pleat.open[data-version='"+version+"']");
}

function findPleat(version) {
  return $(".pleat.close[data-version='"+version+"']");
}

function getTranslated(name) {
  //show function only sends requested i18n elements, so need to modify the
  //js_i18n_elements array to get them here (and load them inside the template)
  return i18n[name] || name;
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
  var pleat=$("<td/>").addClass("pleat").addClass("close")
    .prop("rowspan",$("tbody tr").length)
    .attr("data-version",version);
  var language=header.find(".language").clone(true,true);
  language.prop("title",language.html()).html(language.data("id")).removeClass("expand");
  pleat.append(language.rotate());
  pleat.append(header.find(".creator").clone(true,true).rotate());
  find(version).filter("td").first().after(pleat);
  var pleatHead=$("<th/>").addClass("pleat").addClass("close").append(
    $("<div>").addClass("relative-wrapper").append(
      $("<span>").addClass("button show").html(getTranslated("i_show"))
    )
  ).attr("data-version",version);
  header.after(pleatHead.clone());
  var pleatFoot=$("<th/>").addClass("pleat").addClass("close")
    .attr("data-version",version);
  find(version).filter("th").last().after(pleatHead);
}

function findUnits(version) {
  return find(version).find(".unit");
}

function getVersions() {
  var versions=[];
  $("#hexapla thead tr:first-child .pleat.open").each(function() {
    versions.push($(this).data("version"));
  });
  return versions;
}

function getSize(unit) {
  var rowspan=unit.closest("td").prop("rowspan");
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
      var currPos=currTd.position();
      $(this).css("top",(position.top-currPos.top-24)+"px");
    } else {
      $(this).addClass("dynamic");
    }
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
  var nbOpen=$("thead tr:first-child th.pleat.open:visible").length;
  if (nbOpen==0) {
    $("#hexapla").removeClass("full");
  } else {
    $("#hexapla").addClass("full");
    $("thead tr:first-child th.pleat.open:visible").css("width",100/nbOpen+"%");
  }
}

function toggleShow(e) {
  var version=$(this).getVersion("th");
  find(version).toggle();
  findPleat(version).toggle();
  setTimeout(function(){
    fixWidths();
    positionSplits();
  },0);
  if (e.cancelable) {
    updateUrl();
  }
}

function getDocId() {
  return $("#hexapla").data("id");
}

$.fn.isEdited = function() {
  return this.hasClass("edit");
};

function stringToHtml(formattedString) {
   return formattedString
    .replace("<","&lt").replace(">","&gt;")
    .replace(/\n$/,"\n ").replace(/\n/g, "<br>");
}

$.fn.getVersion = function(ancestor) {
  return this.closest(ancestor).data("version");
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
  if ($(this).parents().is(".box-wrapper")) {
      $(this).css({'width':'100%','height':'100%'});
  }
}

function modified() {
  $(this).addClass("dirty");
  if ($(this).is(".autosize")) {
    autoSize.apply(this);
    positionSplits($(this).closest(".unit"));
    positionSplits($(".pleat.open").not("[data-version='"+$(this).getVersion()+"']"));
  }
}

function toggleEdit (e) {
  var version=$(this).getVersion("th");
  var doc = find(version);
  var units = findUnits(version);
  var top = doc.first();
  var edited = doc.isEdited();
  function applyToggle() {
    if (edited) {
      top.css("width","auto");
      doc.removeClass("edit");
    } else {
      doc.addClass("edit");
      top.css("width",doc.first().outerWidth()+"px");
    }
    doc.find("input.edit").toggleName(getTranslated("i_read"), getTranslated("i_edit"));
  }
  if (getVersions().length==1) {
    if (edited) {
      var fulltext=$("textarea.fulltext").val();
      $("textarea.fulltext").prop("disabled",true);
      var lines=fulltext.split("\n\n");
      var id=getDocId();
      var update=function(){
        $("#hexapla tbody tr.fulltext").hide();
        $("#hexapla tbody tr:not(.fulltext)").remove();
        lines.forEach(function(line,i) {
          var newUnit=$("<div/>");
          var text=$("<div>").addClass("text");
          text.html(stringToHtml(line));
          newUnit.append(text);
          newUnit.addClass("unit").attr("data-version",version);
          var newTd=$("<td>").addClass("pleat open").attr("data-version",version)
            .append($("<div>").addClass("box-wrapper").append(newUnit));
          newUnit.setSize(1);
          var tr=$("<tr/>").attr("id",i).attr("data-line",i).prepend(newTd);
          $("#hexapla tbody").append(tr);
        });
        applyToggle();
        $("textarea.fulltext").removeClass("dirty")
      };
      if ($("textarea.fulltext").is(".dirty")) {
        request({
          type:"PUT",
          data:JSON.stringify({text:lines}),
          contentType: "text/plain",
          url:"work/"+id+"?version="+version
        })
        .done(update)
        .always(function() {
          $("textarea.fulltext").prop("disabled",false);
        });
      } else {
        update();
      }
    } else {
      $("#hexapla tbody tr:not(.fulltext)").hide();
      $("#hexapla tbody tr.fulltext").show();
      $("textarea.fulltext").prop("disabled",false);
      applyToggle();
    }
  } else {
    if (edited) {
      var unitsOk=0;
      function finish() {
        units.find(".split").remove();
        units.find(".join").remove();
        units.removeClass("edit");
        applyToggle();
      }
      units.each(function() {
        var unit=$(this);
        var textarea=unit.find("textarea");
        saveUnit.apply(textarea,[function () {
          unit.find(".text").html(stringToHtml(textarea.val()));
          unitsOk++;
          if (unitsOk==units.length) {
            finish();
          }
        }]);
      });
    } else {
      units.find("textarea").prop("disabled",false);
      units.addClass("edit").find("span").remove();
      units.each(function () {
        var unit=$(this);
        unit.find(".text").css("min-height",(getSize(unit)*32)+"px");
        if (getVersions().indexOf(version)>0) {
          createJoins(unit);
          createSplits(unit);
        }
      });
      applyToggle();
    }
  }
  if (e.hasOwnProperty("cancelable")) {
    //means it is an event, and as such toggle occured on user action
    updateUrl();
    fixWidths();
  }
}

var languages=null;

function fillLanguages(controls,callback) {
  function updateSelect() {
    $.each(languages, function(key, o) {
      var label=key + " (" + [ o.fr, o.en, o[key] ].join(" - ") + ")";
      controls.append($("<option>").val(key).text(label));
    });
    controls.each(function(i,c) {
      var control=$(c);
      control.val(control.data("language"));
      if (control.prop("placeholder")) {
        control.prepend($("<option>").text(control.prop("placeholder")));
      }
    });
    if (typeof callback=="function")
      callback();
  };
  if (!languages) {
    $.getJSON(getPrefix() + "/shared/languages.json", function(result) {
      languages=result;
      updateSelect();
    });
  } else {
    updateSelect();
  }
}

function updateUrl() {
  var opened=$("thead th.open:visible").not(".edit")
    .map(function() {
      return $(this).getVersion("th");
    }).toArray().join("|");
  var edited=$("thead th.edit:visible")
    .map(function() {
      return $(this).getVersion("th");
    }).toArray().join("|");
  var suffix="";
  if (opened) {
    suffix+="open="+encodeURIComponent(opened);
  }
  if (edited) {
    suffix = suffix ? suffix + "&" :"";
    suffix+="edit="+encodeURIComponent(edited);
  }
  suffix = suffix ? "?"+suffix:"";

  window.history.pushState("object or string","",getDocId()+suffix);
}

function changeVersion(oldVersion, newVersion) {
  $("#hexapla").find("*[data-version='" + oldVersion + "']")
    .attr("data-version", newVersion).data("version", newVersion)
    .find(".creator").html(newVersion);
  updateUrl();
}

function toggleAddVersion() {
  $("#addPanel").slideToggle(200);
  $("#removePanel").slideUp(200);
}

function toggleRemoveDoc() {
  $("#removePanel").slideToggle(200);
  $("#addPanel").slideUp(200);
}

function toggleEditDoc() {
  $("#work-info").slideToggle(200);
}

function addVersion() {
  var id = getDocId();
  var ref = $("#addPanel").find("input[name='work-creator']").val();
  if(ref != "") {
    request({
      type: "POST",
      url: "work/"+id+"/"+ref,
      contentType: 'text/plain',
      data: JSON.stringify({creator: ref})
    }).done(function() {
      window.location.href = id + "?edit=" + ref;
    });
  }
  return false;
}

function removeDoc() {
  if(confirm(getTranslated("i_confirm_delete"))) {
    request({
      type: "DELETE",
      url: "work/"+getDocId(),
      contentType: 'text/plain'
    }).done(function() {
      window.location.href = "./";
    });
  }
}

function clickDeleteVersion() {
  var ref = $(this).closest("th").data("version");
  if(confirm(getTranslated("i_delete_version").replace("%s", ref))) {
    deleteVersion(ref);
  }
}

function deleteVersion(version) {
  var id = getDocId();
  request({
    type: "DELETE",
    url: "work/"+id+"/"+version,
    contentType: 'text/plain'
  }).done(function() {
    window.location.reload(true);
  });
}

function createJoin(unit1,unit2) {
    var p=($(unit2).offset().top-$(unit1).offset().top-$(unit1).outerHeight()+32)/(-2);
    var join=$("<span/>").addClass("join").prop("title","merge with previous").css("top",p+"px");
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
      var split=$("<span/>").addClass("split").prop("title","split line "+i).data("line",i);
      unit.append(split);
    }
    setTimeout(positionSplits,0);
  }
}

function saveUnit(callback) {
  var textarea=$(this);
  if (textarea.hasClass("dirty")) {
    textarea.prop("disabled",true);
    var content=textarea.val();
    editOnServer(content, textarea.getReference())
    .done(function(message,result) {
      if (result == "success") {
        textarea.removeClass("dirty");
        if (callback && typeof(callback) == "function") {
          callback();
        }
      } else {
        alert(result+":"+message);
      }
    })
    .always(function() {
      textarea.prop("disabled",false);
    });
  } else {
    if (callback && typeof(callback) == "function") {
      callback();
    }
  }
}

function saveMetadata() {
  var elem=$(this);
  var inputType=elem.prop("tagName");
  if(inputType!="INPUT" || elem.hasClass("dirty")) {
    var id = getDocId();
    var ref = elem.closest("th").data("version");
    var modify={};
    var newValue=elem.val();
    var name=elem.prop("name");
    modify[name]=newValue;
    request({
      type: "PUT",
      url: "work/"+id+"/"+ref,
      contentType: 'text/plain',
      data: JSON.stringify(modify),
      dataType: "json"
    }).done(function(result) {
      var target=elem.siblings("div.metadata."+name);
      newValue=result[name] || newValue;
      elem.val(newValue);
      if(name == "creator") {
        changeVersion(ref, newValue);
      }
      if (inputType=="INPUT") {
        target.text(newValue);
        elem.removeClass("dirty");
      }
      if (name=="language") {
        var lang_id = elem.val();
        fixLanguages(target.data("id",lang_id));
        fixLanguages($("pleat.close[data-version='" + ref + "']")
          .find(".language").data("id", lang_id));
      }
    });
  }
}

function getPreviousUnit(unit) {
  var version=unit.getVersion("td.open");
  var units=findUnits(version);
  return $(units.eq(units.index(unit)-1));
}

var editOnServer = function(content, reference) {
  return request({
    type: "PUT",
    url: "version/"+getDocId()+"?"+ $.param(reference),
    contentType: "text/plain",
    data: content
  });
};

$(document).ready(function() {

  $("#hexapla").on("click", ".button.hide", toggleShow);
  $("#hexapla").on("click", ".button.show", toggleShow);

  $("#hexapla").on("click", ".button.edit-license", function() {
    window.location=getPrefix()+"/works/license/"+getDocId()+'/'+$(this).getVersion("th");
  });

  $("input.edit").on("click",toggleEdit);

  $("tr").on("mouseup select",".unit", function (e) {
    //requires jquery.selection plugin
    var txt=$.selection(),language;
    var unit=$(this);
    if (txt && (language=unit.getLanguage())) {
      e.stopPropagation();
      var menu=$("<div/>").addClass("context-menu");
      menu.append($("<div/>").addClass("item concordance")
        .append(getTranslated("i_search_concordance")+": <em>"+txt+"</em>"));

      menu.css({top:e.pageY,left:e.pageX});
      $("body .context-menu").remove();
      $("body").append(menu);
      $(".context-menu .concordance").on("click",function() {
        $("form.concordance #query").val(txt);
        $("form.concordance #language").val(language);
        $("form.concordance").submit();
      });
      $(".context-menu .item").on("click",function() {
        $("body .context-menu").remove();
      });
    }
  });

  $("body").on("mouseup",".context-menu",function(e) {
    e.stopPropagation();
  });

  $("body").on("mouseup",function(e) {
    $("body .context-menu").remove();
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
          previousUnit.closest("td").prop("rowspan",newSpan);
          previousUnit.find(".text").css("min-height",(newSpan*32)+"px");
          unit.closest("td").remove();
          createJoins(previousUnit);
          createSplits(previousUnit);
      });
    }
  });

  $.fn.setSize = function (size) {
    this.closest("td").prop("rowspan",size).find(".text")
      .css("min-height",size*40+"px");
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
      var newTd=$("<td>").addClass("pleat open").attr("data-version",version)
        .append($("<div>").addClass("box-wrapper").append(newUnit));
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
  $("thead").on("focusout","input.editedMeta", saveMetadata);
  $("thead").on("change","select.editedMeta", saveMetadata);
  $("#hexapla").on("click","span.delete", clickDeleteVersion);

  $(".editedMeta").each(function() {
    $(this).prop("placeholder",$(this).prop("title"));
  })

  $(".top").on("click", "#addVersion", toggleAddVersion);
  $(".top").on("click", "#removeDoc", toggleRemoveDoc);
  $(".top").on("click", "#editDoc", toggleEditDoc);

  $("#addPanel").on("submit", addVersion);
  $("#removePanel").on("click", removeDoc);

  var versions=getVersions();
  const N = versions.length;
  for (var i = N-1; i>=0; i--) {
    addPleat(versions[i]);
  }
  if ($("th.pleat.opened,th.pleat.edited").length==0) {
    //hide all translations except the 2 first ones
    $("thead tr th.open.pleat").filter(":gt(1)").each(toggleShow);
  } else {
    $("thead tr th.open.pleat").not(".opened").not(".edited")
      .each(toggleShow);
  }
  $("#hexapla th.edited").removeClass("edited").not(".edit").each(toggleEdit);

  fixWidths();

  fillLanguages($("select[name=language]"));

  if(N==0) {
    $("#work-info").show().on("submit",function(e) {
      e.preventDefault();
      var data={};
      ["work-creator","language","title","date"].forEach(function(field) {
        data[field]=$("[name='"+field+"']","#work-info").val();
      });
      data.original=$("[name=original-work]").prop("checked");
      request({
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
      });
      return false;
    });

    $(".top h1, .workButton").hide();
  } else {
    $("#work-info").on("submit",function(e) {
      e.preventDefault();
      var data={};
      ["work-creator","language","title","date"].forEach(function(field) {
        data[field]=$("[name='"+field+"']","#work-info").val();
      });
      var id=getDocId();
      data.original=$("[name=original-work]","#work-info").prop("checked");
      request({
        type:"PUT",
        url:"work/"+id+"/original",
        data:JSON.stringify(data),
        contentType:"application/json",
        dataType:"json"
      }).done(function(result) {
        $("#work-info").hide();
        $(".top h1 span.title").text(data.title);
        $(".top h1 span.creator").text(data["work-creator"]);
        $(".top h1 span.language").data("id",data["language"]);
        fixLanguages($(".top h1"));
      });
      return false;
    });
  }
  if (N==1) {
    $(".button.hide").remove();
  }

});

$(window).load(function() {
  if (window.location.hash) {
    $("tr"+window.location.hash+" .unit").addClass("highlight");
    setTimeout(function() {
      $("tr"+window.location.hash+" .unit").removeClass("highlight");
    },500);
  }
});
