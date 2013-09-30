function(head, req) {
  // !code lib/mustache.js
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
  languageData.authors.push(authorData);
  data.languages.push(languageData);
  data.name="works";
  data.scripts=["ul-close"];
  data.css=true;
  return Mustache.to_html(this.templates.works, data,this.templates.partials);
}

