function(head, req) {
  // !code lib/mustache.js
  // !code lib/path.js
  start({headers: {"Content-Type": "text/html;charset=utf-8"}});
  var data = {languages:[]};
  var languageData = null; 
  var authorData = null;
  var lastLanguage = null;
  var lastAuthor = null;
  while (row = getRow()) {
    var l = row.key[0];
    if (l!=lastLanguage) {
      if (languageData) {
        languageData.authors.push(authorData);
        data.languages.push(languageData);
      }
      languageData = {
        code: l,
        authors: []
      };
      lastLanguage = l;
      authorData = null;
    }
    var a = row.key[1];
    if (a!=lastAuthor) {
      if (authorData) {
        languageData.authors.push(authorData);
      }
      authorData  = {
        name: a,
        works: []
      }
      lastAuthor= a;
    }
    authorData.works.push({
      id: row.id,
      name: row.value
    });
  }
  var quote;
  var author;
  switch(Math.floor(Math.random() * 3)) {
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
  languageData.authors.push(authorData);
  data.quote = quote;
  data.author = author;
  data.languages.push(languageData);
  data.name="works";
  data.scripts=["ul-close"];
  data.css=true;
  data.prefix=getPrefix(req.requested_path,0);
  return Mustache.to_html(this.templates.works, data,this.templates.partials);
}

