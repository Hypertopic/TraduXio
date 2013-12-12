function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js

  function getTextLength() {
    if (o.text)
      return o.text.length;
    for each (version in o.translations)
      return version.text.length;
  }
  function getLines(text,version) {
    var lines=[];
    var line={};
    for each(var lineNum in text) {
       if (text[lineNum]) {
         if (line) {
           line.space=lineNum-line.lineNum;
           lines[line.lineNum]=line;
         }
         line={
           text:text[lineNum],
           line:lineNum,
           version:version
         }
       }
    }
    if (line) {
      line.space=lineNum-line.lineNum;
      lines[line.lineNum]=line;
    }
    
  }

  var data = {
    id: o._id,
    work_title: o.title,
    work_creator: o.creator?o.creator:"Anonymus",
    work_language: o.language,
    lines: getTextLength(),
    headers: [],
    units: [],
    raw:[],
    rows:[]
  };
  var hexapla = new Hexapla();
  if (o.text) {
    hexapla.addVersion({
      id: "original",
      text: o.text
    });
    data.raw["original"]=o.text;
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
    hexapla.addVersion({
      id: t,
      text: translation.text
    })
    data.raw[t]=translation.text;
    data.headers.push({
      id:t,
      title: translation.title,
      creator: "Trad. "+ t, //TODO i18n
      language: translation.language,
      date: translation.date,
      creativeCommons: translation.creativeCommons
    });
  }
  data.rows=hexapla.getRows();
  data.name="work";
  data.css=true;
  data.script=true;
  data.language=data.work_language;
  
  return Mustache.to_html(this.templates.work, data, this.templates.partials);
}
