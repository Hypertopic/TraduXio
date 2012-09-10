function (o) {
  if (o.creativeCommons) {
    for each (var t in o.translations) {
      if (t.creativeCommons) {
        emit([o.language, o.creator], o.title);
        break;
      }
    }
  }
}
