function getPrefix(path,length) {

  for (var i=0; i< length; i++) {
    path.pop();
  }

  path.unshift("");

  return path.join('/');
}
