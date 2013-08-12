function(old, req) {
  const VERSION_ID = req.query.version;
  const LINE = +req.query.line;
  const NEW_CONTENT = req.body;
  const IS_ORIGINAL = (VERSION_ID=="original");
  var old_content = (IS_ORIGINAL)
    ? old.text[LINE]
    : old.translations[VERSION_ID].text[LINE];
  if (old_content!=NEW_CONTENT) {
    var o = old;
    if (IS_ORIGINAL) {
      o.text[LINE] = NEW_CONTENT;
    } else {
      o.translations[VERSION_ID].text[LINE] = NEW_CONTENT;
    }
    return [o, VERSION_ID + " updated at line " + LINE];
  }
  return [null, VERSION_ID + " unchanched at line " + LINE];
}
