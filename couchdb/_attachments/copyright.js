function submit() {
  $("button").attr("disabled", "disabled");
  var form = $("form");
  $.ajax({
    type: "POST",
    url: form.data("url"),
    dataType: "json",
    data: {
      captcha: $("#recaptcha_response_field").val(),
      challenge: $("#recaptcha_challenge_field").val(),
      link: $("#link").find("input").val(),
      "wrong-ref": form.find("input[name='wrongref']").is(":checked"),
      "is-protected": form.find("input[name='protected']").is(":checked"),
      details: $("#details").find("textarea").val(),
      email: form.find("input[type='email']").val()
    }
  }).done(checkCaptchaResult).fail(function(err) { checkCaptchaResult(err.responseText); });
  return false;
}

function checkCaptchaResult(data) {
  if(data === "valid") {
    created();
  } else {
    $("#recaptcha_reload").click();
    $("form").append("<div>" + (data ? data : "Serveur indisponible") + "</div>");
    $("button").removeAttr("disabled");
  }
}

function created() {
  $("<h4>Signalement effectué</h4>").appendTo($("#middle").find("form")).fadeOut(1000, function() {
    document.location.href = getLink();
  });
}

function getLink() {
  return $("#link").find("input").val();
}

$(document).ready(function() {
  $("#link").find("input").val(document.referrer);
  $("#header").find("form.concordance").remove();
  $("#middle").on("submit", "form", submit);
});
