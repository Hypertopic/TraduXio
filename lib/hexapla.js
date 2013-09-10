/**
 * A literary work in different versions
 */
function Hexapla() {

  const NEW_LINE = /\n/g;

  this.versions = [];

  /**
   * PUBLIC
   * @param version e.g. {id:'original', text:['Hello world!']}
   */
  this.addVersion = function(version) {
    this.versions.push(version);
  }

  this.isJoined = function(line_number) {
    for each (var version in this.versions) {
      if (version.text[line_number]==null) {
        return true;
      }
    }
    return false;
  }

  this.getNotJoined = function(line_number) {
    while (this.isJoined(line_number)) {
      line_number--;
    }
    return line_number;
  }

  this.getLineVersion = function(version_number, line_number) {
    var version = this.versions[version_number];
    var string = version.text[line_number];
    return (string!=null)
      ? '<span class="unit" data-line="'
        + line_number + '" data-version="'
        + version.id + '">'
        + string.replace("<","&lt").replace(">","&gt;").replace(NEW_LINE, "<br/>")
        + "</span>"
      : null;
  }

  this.getLineVersions = function(line_number) {
    var result = [];
    for (var version_number in this.versions) {
      result.push(this.getLineVersion(version_number, line_number));
    }
    return result;
  }

  this.getLength = function() {
    return this.versions[0].text.length;
  }

  /**
   * PUBLIC
   * @return the versions of the corresponding unit 
   * and the line number of the next unit (or null).
   */
  this.getUnitVersions = function(line_number) {
    const LENGTH = this.getLength();
    var line_number = this.getNotJoined(line_number);
    var unit = this.getLineVersions(line_number);
    while (++line_number<LENGTH && this.isJoined(line_number)) {
      var raw =  this.getLineVersions(line_number);
      for (var version_number in raw) {
        if (raw[version_number]) {
          unit[version_number] += raw[version_number];
        }
      }
    }
    return {
      versions: unit, 
      next: (line_number<LENGTH)? line_number : null
    };
  }
}
