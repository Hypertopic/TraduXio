function(work, req) {
  if (["PUT","POST","DELETE"].indexOf(req.method)==-1) {
    return [null,{code:405,body:"Method "+req.method+" not allowed"}];
  }
  if (["PUT","POST"].indexOf(req.method)!=-1) {
    var args;
    try {
      args = JSON.parse(req.body);
    } catch (e) {
      return [null,{code:400,body:"Method "+req.method+" not allowed"}];
    }
  }
  var actions=[];
  var result={};
  if (["PUT","DELETE"].indexOf(req.method)!=-1) {
    var version_name = req.query.version;
    if (work===null) {
      return [null,{code:404,body:"Not found"}];
    }
    var doc,
        original=false,
        created=false;

    if(!version_name || version_name == "original") {
      doc = work;
      original=true;

    } else {
      if(!work.translations[version_name]) {
        return [null,{code:404,body:version_name+" not found"}];
      }
      doc = work.translations[version_name];
    }
  } else { //req.method==POST
    if (version_name) {
      return [null,{code:400,body:"Can't specify version"}];
    }
    if (work!==null) {
      if (args.creator) {
        version_name=args.creator;
        if (version_name=="original") {
          return [null,{code:400,body:"reserved name 'original'"}];
        }
        delete args.creator;
      } else {
        original=true;
      }

    } else {
      created=true;
      original=true;
      version_name="original";
      work={};
      work._id=work.id || req.uuid;
      delete work.id;
      actions.push("created new doc "+work._id);
      work.translations={};
      result.id=work._id;
      doc=work;
    }

    if (original) {
      if (args.hasOwnProperty("original") && args.original) {
        doc=work;
        doc.text=emptyText(work);
        actions.push("created original version");
        created=true;
      } else if (created) {
        work.translations["first"] = { text: emptyText(work) };
        actions.push("created first version");
        created=true;
      }
      delete args.original;
    } else if (version_name) {
      if (!work.translations[version_name]) {
        work.translations[version_name] = { title: "", language: "", creator:"", text: emptyText(work) };
        doc=work.translations[version_name];
        actions.push("created "+version_name+" version");
        created=true;
      }
    }

  }

  if (req.method=="DELETE") {
    if (!version_name && original) {
      work._deleted = true;
      actions.push("document removed");
    } else if (version_name=="original") {
      if (work.text) {
        delete work.text;
        actions.push("original version removed");
      }
    } else {
      delete work.translations[version_name];
      actions.push("Translation "+version_name+" removed");
    }
    result._deleted=true;
  } else {
    if(!original) {
      if (args.hasOwnProperty("work-creator")) {
        if (doc.creator != args["work-creator"]) {
          actions.push("changed translated author from "+doc.creator+" to "+args["work-creator"]);
          doc.creator = args["work-creator"];
          result["work-creator"]=doc.creator;
        }
        delete args["work-creator"];
      }
      if(args.hasOwnProperty("creator")) {
        var new_name = args["creator"];
        delete args["creator"];
        if(!new_name || typeof new_name != "string" || new_name.length == 0) {
          new_name = version_name;
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
    } else {
      if (args.hasOwnProperty("original")) {
        delete args.original;
        if (args.original) {
          if (!work.text) {
            work.text=[""];
            actions.push("created original version");
          }
        }
      }
      if (args.hasOwnProperty("creator")) {
        args["work-creator"]=args["creator"];
        delete args["creator"];
      }
      if (args.hasOwnProperty("work-creator")) {
        actions.push("changed original creator from "+doc["creator"]+" to "+args["work-creator"]);
        result["work-creator"]=args["work-creator"];
        doc["creator"]=args["work-creator"];
        delete args["work-creator"];
      }
    }
    var keysOK=["date","language","title","text","creativeCommons"];
    for (var key in args) {
      if (keysOK.indexOf(key)!=-1) {
        if (key=="text") {
          if (!Array.isArray(args[key])) {
            return [null,{code:400,body:"text must be an array"}];
          } else {
            actions.push("set text for "+version_name);
            doc[key]=args[key];
          }
        } else if (!typeof args[key] == "string") {
          return [null,{code:400,body:key+" must be a string"}];
        }
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
        return [null,{code:400,body:"can't set value for "+key}];
      }
    }
  }
  result.actions=actions;
  adjustLength(work);
  var code=req.query=="POST"?201:200;
  return [work, {code:code,body:JSON.stringify(result)}];

}

function adjustLength(work) {
  var l=textLength(work);
  if (work.text) expandText(work.text,l);
  if (work.translations) {
    for (var t in work.translations) {
      var translation=work.translations[t];
      if (!Array.isArray(translation.text)) translation.text=[];
      expandText(translation.text,l);
    }
  }
}

function textLength(work) {
  var l = 1;
  if (work.text) l=work.text.length;
  if (work.translations) {
    for (var t in work.translations) {
      if (work.translations[t].text) {
        l=Math.max(l,work.translations[t].text.length);
      }
    }
  }
  return l;
}

function emptyText(work) {
  return expandText([],textLength(work));
}

function expandText(text,l) {
  for(var i=text.length ; i<l ; i++) {
    text.push("");
  }
  return text;
}
