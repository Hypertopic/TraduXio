function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js
  var data = {};
  data.prefix = "..";
  data.name = "copyright";
  data.script = true;
  data.css = true;
  data.public_key = "6LeEL_QSAAAAAJ2jLuZ9FcV7sIik7VFAHRHl1wPv";
  data.server_url = "http://localhost:1337";
  return Mustache.to_html(this.templates.copyright, data, this.templates.partials);
}
