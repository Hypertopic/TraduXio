function(o) {
  const SIZE = 24;
  function format(text, begin) {
    return text.substr(begin, SIZE).toLowerCase();
  }

  const WORD_MATCHER = /\S+/g;
  for (var i in o.text) {
    var text = o.text[i];
    if (text) {
      var match;
      while ((match = WORD_MATCHER.exec(text))) {
	var begin = match.index;
	emit([o.language, format(text, begin)], {unit: i, char: begin});
      }
    }
  }
  for (var t in o.translations) {
    var translation = o.translations[t];
    for (var i in translation.text) {
      var text = translation.text[i];
      if (text) {
        var match;
        while ((match = WORD_MATCHER.exec(text))) {
          var begin = match.index;
          emit(
            [translation.language, format(text, begin)], 
            {unit: i, char: begin, translation: t}
          );
        }
      }
    }
  }
}
