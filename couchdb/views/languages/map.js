function(o) {
  if (o.language) {
    emit(o.language);
    if (o.translations) {
      for each (t in o.translations) {
	emit(t.language);
      }
    }
  }
}
