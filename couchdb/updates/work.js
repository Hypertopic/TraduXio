function(work, req) {
  var args;
  try {
    args = JSON.parse(req.body);
  } catch (e) {
    args={};
  }
  if (["PUT","POST","DELETE"].indexOf(req.method)==-1) {
    return [null,{code:405,body:"Method "+req.method+" not allowed"}];
  }
  if (work===null) {
    work=args;
    work._id=work.id || req.id || req.uuid;
    work.creator=work["work-creator"];
    delete work["work-creator"];
    if (work.original) {
        work.text=work.text || [];
        work.translations={};
    } else {
        delete work.text;
        work.translations={"first":{text:[]}};
    }
    return [work, JSON.stringify({ok:"created",id:work._id})];
  }
  var version_name = req.query.version;
  if (!version_name) {
    if (req.method=="DELETE") {
        work._deleted = true;
        return [work, JSON.stringify("document removed")];
    }
  }

  var doc;
  original=false;
  if(!version_name || version_name == "original") {
    doc = work;
    orignal=true;
  } else {
    if(!work.translations[version_name]) {
      if (req.method=="DELETE" || req.method=="PUT") {
        return [null,{code:404,body:version_name+" not found"}];
      }
      var l = 1;
      if (work.text) l=work.text.length;
      else if (work.translations) {
        for (var t in work.translations) {
          if (work.translations[t].text) {
            l=Math.max(l,work.translations[t].text.length);
          }
        }
      }
      var text=[];
      for(var i=0 ; i<l ; i++) {
        text.push("");
      }
      work.translations[version_name] = { title: "", language: "", creator:"", text: text };
    } else {
      if (req.method=="POST") {
        return [null,{code:400,body:version_name+" already exists"}];
      }
    }
    doc = work.translations[version_name];
  }
  if (req.method=="DELETE") {
    delete work.translations[version_name];
    return [work,JSON.stringify(["remove version "+version_name])];
  }
  var actions=[];
  var result={};
  if(args.hasOwnProperty("work-creator")) {
    if (doc.creator != args["work-creator"]) {
      actions.push("changed translated author from "+doc.creator+" to "+args["work-creator"]);
      doc.creator = args["work-creator"];
    }
    delete args["work-creator"];
  }
  if(args.hasOwnProperty("creator")) {
    var new_name = args["creator"];
    delete args["creator"];
    if(!new_name || typeof new_name != "string" || new_name.length == 0) {
      new_name = "Unnamed document";
    }
    if(new_name != version_name) {
      var i=2, targetName=new_name;
      while(work.translations[targetName] || targetName == "original") {
        targetName = new_name + " ("+i+")";
        i++;
      }
      new_name=targetName;
      work.translations[new_name] = doc;
      delete work.translations[version_name];
      actions.push("changed version name from "+version_name+" to "+new_name);
      version_name=new_name;
    }
    result.creator=version_name;
  }
  var keysOK=["date","language","title","text","creativeCommons"];
  for (var key in args) {
    if (keysOK.indexOf(key)!=-1) {
      if (doc[key] && doc[key] != args[key]) {
        result[key]=args[key];
        actions.push("change "+key+" from "+doc[key]+" to "+args[key]+" for "+version_name);
          doc[key]=args[key];
      } else if (!doc[key]) {
        result[key]=args[key];
        actions.push("set "+key+" to "+args[key]+" for "+version_name);
        doc[key]=args[key];
      }
    } else {
      return [work,{code:400,body:"can't set value for "+key}];
    }
  }
  result.actions=actions;
  return [work, JSON.stringify(result)];
}
