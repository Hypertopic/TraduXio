function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js
  // !code localization.js

  return Mustache.to_html(this.templates.welcome, {
    i18n: localized()
  });
}
