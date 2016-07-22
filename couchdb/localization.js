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
  return i18n[language || getPreferredLanguage()];
}
