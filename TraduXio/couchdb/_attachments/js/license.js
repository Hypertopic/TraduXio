function update() {
  $("div.derivatives").removeClass("nd sa").addClass($("input[type=radio][name=derivatives]:checked").val());
  $("div.commercial").removeClass("nc").addClass($("input[type=radio][name=commercial]:checked").val());
  $("div.license-name").html(getLicense());
  $("input[type=submit]").prop("disabled",getLicense()==license);
}

function getLicense() {
  var license="by";
  var commercial=$("input[type=radio][name=commercial]:checked").val();
  if (commercial!="EMPTY") {
    license+="-"+commercial;
  }
  var derivatives=$("input[type=radio][name=derivatives]:checked").val();
  if (derivatives!="EMPTY") {
    license+="-"+derivatives;
  }
  return license;
}

function goBack() {
  window.location=getPrefix()+"/works/"+$(".full").data("id");
}

function submitLicense(e) {
  e.preventDefault();
  var id = $(".full").first().data("id");
  var tmp = document.location.pathname.split("/");
  var ref = tmp[tmp.length - 1];
  $.ajax({
    type:"PUT",
    url: $("body").data("prefix") + "/works/work/"+id+"/"+ref,
    contentType:"text/plain",
    data:JSON.stringify({"creativeCommons": getLicense()})
  }).done(goBack).fail(function() {
    alert("failed!");
  });
}

$(document).ready(function() {
  if (license.indexOf("nd")>-1) {
    $("input:radio[name='derivatives'][value='nd']").prop("checked",1);
  } else if (license.indexOf("sa")>-1) {
    $("input:radio[name='derivatives'][value='sa']").prop("checked",1);
  }
  if (license.indexOf("nc")>-1) {
    $("input:radio[name='commercial'][value='nc']").prop("checked",1);
  }

  $("input").on("change",update);
  update();

  $("form#license").on("submit",submitLicense);
  $("form#license input[name=cancel]").on("click",goBack);
});
