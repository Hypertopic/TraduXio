function(old, req) {

  function Work() {
    this.data = old;
    this.isOriginal = function(version) {
      return version=="original";
    };
    this.getContent = function(version, line) {
      return (this.isOriginal(version)
        ? this.data
        : this.data.translations[version]
      ).text[line];
    };
    this.setContent = function(version, line, content) {
      if (this.isOriginal(version)) {
        this.data.text[line] = content;
      } else {
        this.data.translations[version].text[line] = content;
      }
    };
  }

  const VERSION_ID = req.query.version;
  const LINE = +req.query.line;
  var new_content = req.body;
  var work = new Work();
  var old_content = work.getContent(VERSION_ID, LINE);
  if (new_content!=old_content) {
    }
    work.setContent(VERSION_ID, LINE, new_content);
    return [work.data, VERSION_ID + " updated at line " + LINE];
  }
  return [null, VERSION_ID + " unchanged at line " + LINE];
}
