// !json i18n

function getPreferredLanguage() {
  var available = "en";
  var required = req.headers["Accept-Language"];
  if (required) {
    for each (var l in required.split(",")) {
      var preferred = l.substring(0,2);
      if (i18n.hasOwnProperty(preferred)) {
        available = preferred;
        break;
      }
    }
  }
  return available;
}

function localized(language) {
  var available = "en";
  var language=language || getPreferredLanguage();
  var items=i18n[language];
  if (language != available) {
    for (var item in i18n[available]) {
      if (!items[item]) items[item]=i18n[available][item];
    }
  }
  return items;
}
