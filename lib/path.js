log(req);

function getPrefix(path,length) {
  
  log(path);
  
  for (var i=0; i< length; i++) {
    path.pop();
  }

  path.unshift("");
 
  return path.join('/');
}
