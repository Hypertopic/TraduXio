function(o, req) {
  // !json templates.work
  // !code lib/mustache.js

  const NEW_LINE = /\n/g;
  function toHtml(string) {
    return string.replace(NEW_LINE, "<br/>");
  }

  const CURRENT = (req.query.translation)? req.query.translation
    : Object.keys(o.translations)[0];
  const TRANSLATION = o.translations[CURRENT].text;
  var data = {
    title: o.title,
    creator: o.creator,
    language: o.language,
    current: CURRENT,
    others: [],
    units: []
  };
  for (var t in o.translations) {
    if (t!=CURRENT) {
      data.others.push(t);
    }
  }
  var start = 0;
  var sources = [];
  for (var i = 0; i<TRANSLATION.length; i++) {
    if (TRANSLATION[i]!=null && i>start) {
      data.units.push({
        sources: sources,
        target: toHtml(TRANSLATION[start])
      });
      start = i;
      sources = [];
    }
    sources.push(toHtml(o.text[i]));
  }
  data.units.push({
    sources: sources,
    target: toHtml(TRANSLATION[start])
  });
  return Mustache.to_html(templates.work, data);
}
