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
    return (string)? string.replace(NEW_LINE, "<br/>") : null;
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
          block[version] += "<br/>" + raw[version];
          log(raw[version]);
        }
      }
    }
    return {
      versions: block, 
      next: (n<LENGTH)? n : null
    };
  }

  var data = {
    title: o.title,
    creator: o.creator,
    language: o.language,
    units: []
  };
  var texts = [];
  if (o.text) {
    texts.push(o.text);
  }
  for each (var t in o.translations) {
    texts.push(t.text);
  }
  var block = {next: 0};
  do {
    block = getVersions(texts, block.next);
    data.units.push({versions: block.versions});
  } while (block.next);
  return Mustache.to_html(templates.work, data);
}
