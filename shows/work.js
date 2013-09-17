function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js

  function getTextLength() {
    if (o.text)
      return o.text.length;
    for each (version in o.translations)
      return version.text.length;
  }

  var data = {
    id: o._id,
    work_title: o.title,
    work_creator: o.creator,
    work_language: o.language,
    lines: getTextLength(),
    headers: [],
    units: []
  };
  var hexapla = new Hexapla();
  if (o.text) {
    hexapla.addVersion({
      id: "original",
      text: o.text
    })
    data.headers.push({
      title: o.title,
      creator: "Original",
      language: o.language,
      date: o.date,
      creativeCommons: o.creativeCommons
    });
  }
  for (var t in o.translations) {
    var translation = o.translations[t];
    hexapla.addVersion({
      id: t,
      text: translation.text
    });
    data.headers.push({
      title: translation.title,
      creator: "Trad. "+ t, //TODO i18n
      language: translation.language,
      date: translation.date,
      creativeCommons: translation.creativeCommons
    });
  }
  var unit = {next: 0};
  var first=true;
  do {
    unit = hexapla.getUnitVersions(unit.next);
    data.units.push({versions: unit.versions, first:first});
    first=false;
  } while (unit.next);
  data.name="work";
  data.css=true;
  data.script=true;
  data.language=data.work_language;
  return Mustache.to_html(this.templates.work, data, this.templates.partials);
}
