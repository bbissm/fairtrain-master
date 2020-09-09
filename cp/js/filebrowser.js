var wind = null;
var fieldname = null;
var specialintv = null;
var specialcounter = null;

function FileBrowser(field_name, url, type, win) {
  wind = win;
  fieldname = field_name;
  $.get("/cp/async/html/" + type, function(data) {
    $("#popup #popup_content", window.parent.document).html(data);
    parent.setup(1);
    $("#popup", window.parent.document).fadeIn(250);
    if (specialintv != null && specialintv != undefined) clearInterval(specialintv);
    specialintv = setInterval(function() {
      if ($("#popup #popup_content a", window.parent.document).attr("binding") == "" || $("#popup #popup_content a", window.parent.document).attr("binding") == null || $("#popup #popup_content a", window.parent.document).attr("binding") == undefined) {
        $("#popup #popup_content a", window.parent.document).attr("binding", "1");
        $("#popup #popup_content a", window.parent.document).on("click", function() {
          setTimeout(function() {
            if ($("#popup #popup_content input[name=url]", window.parent.document).val().length > 0 && $("#popup #popup_content input[name=url]", window.parent.document).val().indexOf("crop:") < 0) {
              wind.document.getElementById(fieldname).value = $("#popup #popup_content input[name=url]", window.parent.document).val();
              $("#popup", window.parent.document).hide();
            }
          }, 500);
        });
      }
    }, 500);
  });
  return false;
}