$.fn.concordancify = function(default_language, default_query) {

  this.append('<input id="query" type="search" placeholder="Rechercher" value="'
    +  default_query + '" />');
  this.append('<select id="language" />');

  $.getJSON("languages", function(result) {
    $.each(result.rows, function(i, o) {
      $("#language").append("<option>" + o.key + "</option>");
    });
    $("#language").val(default_language);
  }); 

  this.submit(function(event) { //TODO jQuery 2
    event.preventDefault();
    var query = $(this).find('#query').val().toLowerCase();
    var language = $("#language").val();
    window.location.href = 'concordance?' + $.param({
      startkey: '["' + language + '","' + query + '"]',
      endkey: '["' + language + '","' + query + '\\u9999"]',
      query: query,
      language: language
    });
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
    $.getJSON("shared/languages.json",function(result) {
      languagesNames=result;
      callback(true);
    });
  } else {
     callback(true);
  }
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
});

