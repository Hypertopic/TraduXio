function(o) {
  const MAX_SIZE = 124;
  const NB_WORDS = 10;

  var ideograms=["\\u3400-\\u9FFF","\\u3040-\\u30FF"].join("");
  var punctuation_signs=["'","`","\\-","\\uff0c","\\u3002"].join("");

  var regex="["+ideograms+"]|[^\\s"+punctuation_signs+ideograms+"]+";

  function format(text, begin) {
    var SUB_WORD_MATCHER=new RegExp(regex,"g"),
        s=text.substr(begin),
        n=0, end=-1, m;
    while ((m=SUB_WORD_MATCHER.exec(s)) && n<NB_WORDS && end < MAX_SIZE) {
      n++;
      if (m.index+m[0].length > MAX_SIZE && n>1) {
        n=NB_WORDS; //force stop
      } else {
        end=m.index+m[0].length;
      }
    }

    return s.substr(0, end).toLowerCase();
  }

  const WORD_MATCHER = new RegExp(regex,"g");

  if (o.translations) {
    var nb_translations=Object.keys(o.translations).length;

    if (nb_translations) {
      if (o.language) for (var i in o.text) {
        var text = o.text[i];
        if (text && text.length<1024) {
          var match;
          while ((match = WORD_MATCHER.exec(text))) {
            var begin = match.index;
            emit([o.language, format(text, begin)], {unit: i, char: begin});
          }
        }
      }
      for (var t in o.translations) {
        var translation = o.translations[t];
        if (translation.language) for (var i in translation.text) {
          var text = translation.text[i];
          if (text && text.length<1024) {
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
  }
}
