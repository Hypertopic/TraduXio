function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js
  // !code localization.js

  var js_i18n_elements=[
    "i_read","i_edit","i_show","i_search_concordance",
    "i_confirm_delete","i_delete_version","i_delete_original", "i_no_title", "i_no_author"
  ];
  var i18n=localized();

  function getTextLength() {
    if (o.text)
      return o.text.length;
    for each (version in o.translations)
      return version.text.length;
  }

  var newWork = false;
  if (o===null) {
    o={translations:{},language:getMyLanguage()};
    newWork=true;
  }
  var data = {
    id: o._id,
    work_title: o.title,
    display_work_title: o.title?o.title:i18n["i_no_title"],
    work_creator: o.creator,
    display_work_creator: o.creator?o.creator:i18n["i_no_author"],
    work_language: o.language,
    original: o.text ? true : false,
    date:o.date,
    lines: getTextLength(),
    headers: [],
    units: [],
    rows:[],
    lang:i18n.lang,
    i18n:i18n
  };
  var js_i18n={};
  js_i18n_elements.forEach(function (item) {
    if (i18n[item]) js_i18n[item]=i18n[item];
  });

  if (!newWork) {
    var hexapla = new Hexapla();
    var edited_versions=req.query.edit ? req.query.edit.split("|") : [];
    var opened_versions=req.query.open ? req.query.open.split("|") : [];
    if (o.text) {
      hexapla.addVersion({
        id: "original",
        text: o.text
      });
      data.headers.push({
        id: "original",
        is_original: true,
        title: o.title,
        language: o.language,
        date: o.date,
        raw:o.text,
        creativeCommons: o.creativeCommons,
        edited: (edited_versions.indexOf("original")!=-1),
        opened: (opened_versions.indexOf("original")!=-1)
      });
    }
    for (var t in o.translations) {
      var translation = o.translations[t];
      hexapla.addVersion({
        id: t,
        text: translation.text
      });
      data.headers.push({
        id:t,
        title: translation.title,
        work_creator: translation.creator || "",
        creator: t,
        language: translation.language || "",
        date: translation.date,
        raw:translation.text,
        creativeCommons: translation.creativeCommons,
        edited: (edited_versions.indexOf(t)!== -1),
        opened: (opened_versions.indexOf(t)!== -1)
      });
    }
    data.rows=hexapla.getRows();
  }

  data.name="work";
  data.css=true;
  data.script=true;
  data.scripts=["jquery.selection","jquery.ajax-retry"];
  data.language=data.work_language;
  data.prefix="..";
  data.notext=o.text ? false : true;
  data.original=o.text ? true : (newWork ? true : false);
  data.i18n_str=JSON.stringify(js_i18n);
  if (data.headers.length==1) {
    data.justOneText=true;
    data.fulltext=data.headers[0].raw;
  }

  return Mustache.to_html(this.templates.work, data, this.templates.partials);
}
