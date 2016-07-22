function animateQuote(index) {
  var citation = $("#citation").fadeIn(500);
  citation.animate({"margin-top": "5px"}, 8000, "swing", function() {
    citation.fadeOut(500, function() {
      var quote;
      var author;
      switch(index) {
        case 0:
          quote = "Du point de vue de la communauté, le langage n'est pas seulement un fait social, "
            + "[...] il est par l'altérité [...] le fondement de toute association humaine.";
          author = "Eugenio Coseriu";
          break;
        case 1:
          quote = "La traduction révèle alors le texte à lui-même : en quelque sorte, le texte semble inachevé tant qu'il n'est pas traduit.";
          author = "François Rastier";
          break;
        default:
          quote = "Une langue est un filet jeté sur la réalité des choses. Une autre langue est un autre filet. Il est rare que les mailles coïncident.";
          author = "Maurice Carrez";
          break;
      }
      citation.find(".quote").text(quote);
      citation.find(".author").text(author);
      animateQuote(index >= 2 ? 0 : index + 1);
    });
  });
}

function initQuote() {
  $("#citation").find(".quote").text("Une langue est un filet jeté sur la réalité des choses. "
    + "Une autre langue est un autre filet. Il est rare que les mailles coïncident.");
  $("#citation").find(".author").text("Maurice Carrez");
}

$(document).ready(function() {
  initQuote();
  animateQuote(1);
});
