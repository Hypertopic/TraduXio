function(work, req) {
  var args = JSON.parse(req.body);
  if(args.key == "remove") {
	work._deleted = true;
	return [work, "document removed"];
  }
  var version = req.query.version;
  if(args.key == "delete") {
	delete work.translations[version];
	return [work, version + " deleted"];
  }
  var doc;
  if(version == "original") {
	doc = work;
  } else {
	if(!work.translations[version]) {
	  var text = work.text ? new Array(work.text.length) : new Array(1);
	  for(var i=0 ; i<text.length ; i++) {
		text[i] = "";
	  }
	  work.translations[version] = { title: work.title, language: work.language, text: text };
	}
	doc = work.translations[version];
  }
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
