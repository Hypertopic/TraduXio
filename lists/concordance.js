function(head, req) {
  // !json templates.concordance
  // !code lib/mustache.js

  function getBlock(original, unit, translation) {
    var i = unit;
    while (translation[i]==null) {
      i--;
    }
    var result = {
      original: original[i],
      translation: translation[i].replace(/\n/g, "<br/>")
    }
    while (++i<translation.length && translation[i]==null) {
      result.original += "<br/>" + original[i];
    }
    return result;
  }

  function highlight(context, pattern) {
    //TODO more efficient and safer?
    const regexp = new RegExp(pattern, "gi");
    return context.replace(regexp, "<b>" + pattern + "</b>");
  }

  function push(occurrences, context, mapping, work, translationID) {
    if (mapping) {
      var translation = work.translations[translationID];
      occurrences.push({
        context: highlight(context, req.query.query),
        mapping: mapping,
        original: {
          work_id: work._id,
          creator: work.creator, 
          title: work.title,
          publisher: work.publisher,
          date: work.date
        },
        translation: {
          creator: translationID,
          publisher: translation.publisher,
          date: translation.date
        }
      });
    }
  }

  start({headers: {"Content-Type": "text/html;charset=utf-8"}});
  var data = {
    language: req.query.language,
    query: req.query.query,
    occurrences:[]
  };
  while (row = getRow()) {
    if (row.value.translation) {
      // translation >> original
      if (row.doc.text) {
        var block = getBlock(
          row.doc.text,
          row.value.unit,
          row.doc.translations[row.value.translation].text
        );
        push(data.occurrences, block.translation, block.original, row.doc, row.value.translation);
      }
      // translation >> translations
      //TODO
    } else {
      // original >> translations
      if (row.doc.text) {
        for (var t in row.doc.translations) {
          var block = getBlock(
            row.doc.text,
            row.value.unit,
            row.doc.translations[t].text
          );
          push(data.occurrences, block.original, block.translation, row.doc, t);
        }
      }
    }
  }
  return Mustache.to_html(templates.concordance, data);
}
