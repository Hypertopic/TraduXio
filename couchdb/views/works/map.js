function (o) {
  if(o.title !== undefined) {
	emit([o.language, o.creator?o.creator:"Anonymus"], o.title);
  }
}
