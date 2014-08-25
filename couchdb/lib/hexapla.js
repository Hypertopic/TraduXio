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
  };

  this.isJoined = function(line_number) {
    for (var version_number in this.versions) {
      var version=this.versions[version_number];
      if (version.text && version.text[line_number]==null) {
        return true;
      }
    }
    return false;
  };

  this.getNotJoined = function(line_number) {
    while (this.isJoined(line_number)) {
      line_number--;
    }
    return line_number;
  };

  this.getLineVersion = function(version_number, line_number) {
    var version = this.versions[version_number];
    if (version)
      var string = version.text[line_number];
    else
      string=null;
    return string;
  };

  this.getHtmlLine = function(version_number, line_number) {
    var string = this.getLineVersion(version_number, line_number);
    if (string!=null)
      return '<div class="unit" data-line="'
        + line_number + '" data-version="'
        + this.versions[version_number].id + '">'
        + string.replace("<","&lt").replace(">","&gt;").replace(NEW_LINE, "<br/>")
        + "</div>";
    return null;
  };

  this.getLineVersions = function(line_number,plain) {
    plain=plain || false;
    var result = [];
    for (var version_number in this.versions) {
      result.push(
        plain?
          this.getLineVersion(version_number, line_number)
        : this.getHtmlLine(version_number, line_number)
      );
    }
    return result;
  };

  this.getLength = function() {
    return this.versions[0].text.length;
  };

  /**
   * PUBLIC
   * @return the versions of the corresponding unit 
   * and the line number of the next unit (or null).
   */
  this.getUnitVersions = function(line_number,plain) {
    plain=plain || false;
    const LENGTH = this.getLength();
    var line_number = this.getNotJoined(line_number);
    var unit = this.getLineVersions(line_number,plain);
    while (++line_number<LENGTH && this.isJoined(line_number)) {
      var raw =  this.getLineVersions(line_number,plain);
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
  };
  
  /**
   * PUBLIC
   * @return rows containing cells with space value
   * representing the number of rows to expand the cell.
   * To be used in a HTML table, with space as rowspan
   * attribute.
   */
  this.getRows = function() {
    var rows=[];
    var finished=false;
    var lastLines=[];
    for (var i=0; !finished; i++) {
      finished=true;
      var row={units:[],line:i};
      for (var vi in this.versions) {
	if (i < this.versions[vi].text.length) {
          finished=false;
	  if (this.versions[vi].text[i]!=null) {
	    if (lastLines[vi]) {
	      lastLines[vi].line.space=i-lastLines[vi].num;
	    }
	    var line={
	      version:this.versions[vi].id,
	      text:this.versions[vi].text[i].replace("<","&lt").replace(">","&gt;").replace(NEW_LINE, "<br/>")
	    };
	    row.units.push(line);
	    lastLines[vi]={line:line,num:i};
	  }
        }
      }
      printLines(lastLines);
      rows[i]=row;
    }
    if (finished) {
      for (var version in lastLines) {
        var lastLine=lastLines[version].line;
        lastLine.space=i-1-lastLines[version].num;
      }
    }
    printLines(lastLines);
    return rows;
  };
  
  function printLines(lastLines) {
    for (var version in lastLines) {
      var line=lastLines[version].line;
      log(line.version+":"+lastLines[version].num+" "+line.space+" rows");
    }
  }
}
