function (o) {
  emit([o.language, o.creator?o.creator:"Anonymus"], o.title);
}
