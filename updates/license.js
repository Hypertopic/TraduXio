function(work, req) {
  const licenses=[ "by", "by-nc", "by-sa", "by-nc-sa", "by-nd", "by-nc-nd" ];
  
  var license=req.body;
  var version=req.query.version;

  var message="";
  var text={};
  if (version) { 
    if (version=="original") {
      text=work;
    } else if (work.translations && work.translations[version]) {
      text=work.translations[version];
    } else {
      message="Version "+version+" inexistant";
    }
    if (text) {
      if (license) {
	if (licenses.indexOf(license)>-1) {
	  if (text.creativeCommons) {
            if (license != text.creativeCommons) {
	      message="License for "+version+" modified from "+text.creativeCommons+" to "+license;
            } else {
              message="License "+license+" for "+version+" unchanged";
            }
          } else {
            message="License for "+version+" set to "+license;
          }
	  text.creativeCommons=license;
	} else {
	  message="License "+license+" invalid";
	}
      } else {
	message="No license specified";
      }
    }
  } else {
    message="No version specified";
  }
  return [work,message];
}
