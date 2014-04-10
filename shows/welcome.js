function(o, req) {
  // !code lib/mustache.js
  // !code lib/hexapla.js
  // !code lib/path.js

  var data = {};
  data.name="welcome";
  data.css=true;
  data.prefix=getPrefix(req.requested_path,1);
 
  return Mustache.to_html(this.templates.welcome, data, this.templates.partials);
}
