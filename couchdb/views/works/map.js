function (o) {
  if(o.title !== undefined) {
    var languages=[];
    if (o.translations) {
      for (var trname in o.translations) {
        var tr=o.translations[trname];
        if (tr.language && languages.indexOf(tr.language)==-1) {
          languages.push(tr.language);
        }
      }
    }
    emit([o.language, o.creator?o.creator:"Anonymus"], {
      title:o.title,
      original:o.text?true:false,
      languages: languages
    });
  }
}
