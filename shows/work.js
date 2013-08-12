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

  function toHtml(string, line, version) {
    return (string!=null)
      ? '<div class="unit" data-line="'
        + line + '" data-version="'
        + version + '"><div>'
        + string.replace(NEW_LINE, "</div><div>")
        + "</div></div>"
      : null;
  }

  function getRawUnit(texts, n, headers) {
    var result = [];
    for (var version in texts) {
      result.push(toHtml(texts[version][n], n, headers[version].id));
    }
    return result;
  }

  function getVersions(texts, n, headers) {
    const LENGTH = texts[0].length;
    var block = getRawUnit(texts, n, headers);
    while (++n<LENGTH && isJoined(texts, n)) {
      var raw =  getRawUnit(texts, n, headers);
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
    id: o._id,
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
      id: "original",
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
      id: t,
      title: translation.title,
      creator: "Trad. "+ t, //TODO i18n
      language: translation.language,
      date: translation.date,
      creativeCommons: translation.creativeCommons
    });
  }
  var block = {next: 0};
  do {
    block = getVersions(texts, block.next, data.headers);
    data.units.push({versions: block.versions});
  } while (block.next);
  return Mustache.to_html(templates.work, data);
}
