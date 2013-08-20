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

