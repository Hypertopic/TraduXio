function(o, req) {
  // !json templates.work
  // !code lib/mustache.js
  // !code lib/hexapla.js

  var data = {
    id: o._id,
    work_title: o.title,
    work_creator: o.creator,
    work_language: o.language,
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
  do {
    unit = hexapla.getUnitVersions(unit.next);
    data.units.push({versions: unit.versions});
  } while (unit.next);
  return Mustache.to_html(templates.work, data);
}
