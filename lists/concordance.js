function(head, req) {
  // !json templates.concordance
  // !code lib/mustache.js
  // !code lib/hexapla.js

  function highlight(context, pattern) {
    //TODO safer so that HTML is not matched
    const regexp = new RegExp(pattern, "gi");
    return context.replace(regexp, "<b>" + pattern + "</b>");
  }

  function getHeaders(work, translation_id) {
    var translation = work.translations[translation_id];
    return {
      creator: translation_id,
      publisher: translation.publisher,
      date: translation.date
    };
  }

  function push(occurrences, context, mapping, line_number, original_header, translation_header) {
    var hexapla = new Hexapla();
    hexapla.addVersion(context);
    hexapla.addVersion(mapping);
    var unit = hexapla.getUnitVersions(line_number).versions;
    if (unit[1]) {
      occurrences.push({
        context: highlight(unit[0], req.query.query),
        mapping: unit[1],
        original: original_header,
        translation: translation_header
      });
    }
  }

  function getTranslation(work, translation_id) {
    return {
      id: translation_id,
      text: work.translations[translation_id].text
    };
  }

  start({headers: {"Content-Type": "text/html;charset=utf-8"}});
  var data = {
    language: req.query.language,
    query: req.query.query,
    occurrences:[]
  };
  while (row = getRow()) {
    var translation_id = row.value.translation;
    var line_number = row.value.unit; 
    var work = row.doc;
    var original = (work.text)? {
      id: "original",
      text: work.text
    } : null;
    var original_header = {
      work_id: work._id,
      creator: work.creator, 
      title: work.title,
      publisher: work.publisher,
      date: work.date
    };
    if (translation_id) {
      // translation >> original
      if (original) {
        var translation_header = getHeaders(work, translation_id);
        push(data.occurrences, getTranslation(work, translation_id), original, line_number, original_header, translation_header);
      }
      // translation >> translations
      //TODO
    } else {
      // original >> translations
      for (var t in work.translations) {
        var translation_header = getHeaders(work, t);
        push(data.occurrences, original, getTranslation(work, t), line_number, original_header, translation_header);
      }
    }
  }
  return Mustache.to_html(templates.concordance, data);
}
