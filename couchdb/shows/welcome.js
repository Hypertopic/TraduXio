function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js
 
  return Mustache.to_html(this.templates.welcome, {});
}
