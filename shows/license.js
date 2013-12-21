function(work, req) {
  // !code lib/mustache.js
  // !code lib/path.js

  var version=req.query.version;

  var data={id:work._id};
  data.work_title=work.title;
  data.work_creator=work.creator;
  data.work_date=work.date;
  data.text=null;

  if (version) { 
    if (version=="original") {
      data.text=work;
      data.text.creator=work.creator?work.creator:"Anonymous";
    } else if (work.translations && work.translations[version]) {
      data.text=work.translations[version];
      data.text.creator="Trad. "+version;
    }
  }
  if (!data.text) { 
    log("No text");
    start({"code":404});
    return "Not Found";
  }
  data.text.license=data.text.creativeCommons;
  data.name="license";
  data.css=true;
  data.script=true;
  data.prefix=getPrefix(req.requested_path,3);

  return Mustache.to_html(this.templates.license, data, this.templates.partials);
}
