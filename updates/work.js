function(work, req) {
  var version = req.query.version;
  var doc = (version == "original") ? work : work.translations[version];
  var args = JSON.parse(req.body);
  if(args.key == "work-creator") {
	doc.creator = args.value;
  } else if(args.key == "creator") {
	var name = args.value;
	if(name == undefined) {
	  name = "Unnamed document";
	}
	if(name != version) {
	  while(work.translations[name] || name == "original" || name.length == 0) {
		name += "(2)";
	  }
	  work.translations[name] = doc;
	  delete work.translations[version];
	  return [work, name];
	} else {
	  return [work, version];
	}
  } else {
	doc[args.key] = args.value;
  }
  return [work, args.value];
}
