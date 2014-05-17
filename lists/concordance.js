function(head, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js

  function highlight(context, pattern) {
    //TODO safer so that HTML is not matched
    const regexp = new RegExp("("+pattern+")", "gi");
    return context.replace(regexp, "<b>$1</b>");
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
    const HTML_CONTENT = /<div>[^<]+<\/div>/g;
    var hexapla = new Hexapla();
    hexapla.addVersion(context);
    hexapla.addVersion(mapping);
    var unit = hexapla.getUnitVersions(line_number).versions;
    if (unit[1] && unit[1].trim()!="") {// && HTML_CONTENT.test(unit[1])) {
      occurrences.push({
        context: highlight(unit[0], req.query.query),
        mapping: unit[1],
        original: original_header,
        translation: translation_header
      });
    } else {log("don't push "+unit[1]);}
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
  if (req.query.query) {
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
      creator: work.creator?work.creator:"Anonymus", 
      title: work.title,
      publisher: work.publisher,
      date: work.date
    };
    if (translation_id) {
      var translation = getTranslation(work, translation_id);
      var translation_header = getHeaders(work, translation_id);
      // translation >> original
      if (original) {
        push(data.occurrences, translation, original, line_number, original_header, translation_header);
      }
      // translation >> translations
      for (var t in work.translations) {
        if (t!=translation_id) {
          push(data.occurrences, translation, getTranslation(work, t), line_number, original_header, [translation_header, getHeaders(work, t)]);
        }
      }
    } else {
      // original >> translations
      for (var t in work.translations) {
        push(data.occurrences, original, getTranslation(work, t), line_number, original_header, getHeaders(work, t));
      }
    }
  }
  }
  data.name="concordance";
  data.css=true;
  data.script=true;
  data.prefix=getPrefix(req.requested_path,1);

  return Mustache.to_html(this.templates.concordance, data, this.templates.partials);
}
