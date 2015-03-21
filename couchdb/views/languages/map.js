function(o) {
  emit(o.language);
  for each (t in o.translations) {
    emit(t.language);
  }
}
