function(o, req) {
  // !json templates.work
  // !code lib/mustache.js

  function isJoined(texts, n) {
    for each (var version in texts) {
      if (version[n]==null) {
        return true;
      }
    }
    return false;
  }

  const NEW_LINE = /\n/g;

  function toHtml(string) {
    return (string!=null)
      ? '<div class="unit"><div>'
        + string.replace(NEW_LINE, "</div><div>")
        + "</div></div>"
      : null;
  }

  function getRawUnit(texts, n) {
    var result = [];
    for each (var version in texts) {
      result.push(toHtml(version[n]));
    }
    return result;
  }

  function getVersions(texts, n) {
    const LENGTH = texts[0].length;
    var block = getRawUnit(texts, n);
    while (++n<LENGTH && isJoined(texts, n)) {
      var raw =  getRawUnit(texts, n);
      for (var version in raw) {
        if (raw[version]) {
          block[version] += raw[version];
        }
      }
    }
    return {
      versions: block, 
      next: (n<LENGTH)? n : null
    };
  }

  var data = {
    work_title: o.title,
    work_creator: o.creator,
    work_language: o.language,
    headers: [],
    units: []
  };
  var texts = [];
  if (o.text) {
    texts.push(o.text);
    data.headers.push({
      title: o.title,
      language: o.language,
      date: o.date,
      creativeCommons: o.creativeCommons
    });
  }
  for (var t in o.translations) {
    var translation = o.translations[t];
    texts.push(translation.text);
    data.headers.push({
      title: translation.title,
      creator: "Trad. "+ t, //TODO i18n
      language: translation.language,
      date: translation.date,
      creativeCommons: translation.creativeCommons
    });
  }
  var block = {next: 0};
  do {
    block = getVersions(texts, block.next);
    data.units.push({versions: block.versions});
  } while (block.next);
  return Mustache.to_html(templates.work, data);
}
