$.fn.concordancify = function() {
  
  default_language=$("body").data("language") || currentLanguage;
  default_query=$("body").data("query") || "";

  this.append('<input id="query" type="search" results="5" name="query" placeholder="Rechercher" value="'
    +  default_query + '" />');
  this.append('<select id="language" name="language"/>');
  
  var form=this; 

  getLanguageNames(function() {
    $.getJSON(getPrefix()+"/languages", function(result) {
      $.each(result.rows, function(i, o) {
	$("#language").append("<option value=\""+o.key+"\">" + o.key + " - " + getLanguageName(o.key) + "</option>");
      });
      $("#language").val(default_language);
    }); 
  });
  
  var submitForm=function(event) { //TODO jQuery 2
    event.preventDefault();
    var query = form.find('#query').val().toLowerCase();
    var language = $("#language").val();
    window.location.href = getPrefix()+'/works/concordance?' + $.param({
      startkey: '["' + language + '","' + query + '"]',
      endkey: '["' + language + '","' + query + '\\u9999"]',
      query: query,
      language: language
    });
  }

  this.on("submit",submitForm);
  $(".submit",form).on("click",submitForm);
  $("#language",form).on("keypress",function(e) {
      if(e.which == 13) {
	  submitForm(e);
      }
  });
}

var languagesNames;
var currentLanguage='fr';

function getLanguageName(id,target) {
  var result=id;
  target=target || currentLanguage;
  if (languagesNames[id]) {
    var list=languagesNames[id];
    if(list[target]) {
      return list[target];
    } else if (list['en']) {
      return list['en'];
    } else if (list[id]) {
      return list[id];
    } else {
      result=list[Object.keys(list)[0]];
    }
  }
  return result;
}

function getLanguageNames(callback) {
  if (! languagesNames) {
    $.getJSON(getPrefix()+"/shared/languages.json",function(result) {
      languagesNames=result;
      callback(true);
    });
  } else {
     callback(true);
  }
}

$.fn.outerHtml = function() {
  return this.clone().wrap("<div>").parent().html();
}

function getPrefix() {
  return $("body").data("prefix");
}

$(document).ready(function() {
  getLanguageNames(function() {
    $(".language").each(function() {
      var lang=this;
      var langID=$(lang).data("id");
      var langName=getLanguageName(langID);
      if ($(lang).is(".expand")) {
	$(lang).text(langName);
	$(lang).attr('title',langID);
      } else {
	$(lang).attr('title',langName);
      }
    });
  });
  $("form.concordance").concordancify();
});

