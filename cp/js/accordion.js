$.fn.accordion = function() {
	var single = parseInt($(this).attr("single"));
	var push = $(this).attr("push");
	var accordionconfig = 0;
	$(this).find(".config").click(function() {
		accordionconfig = 1;
	});
	$(this).find("tr.accordion_title").unbind("click").click(function() {
		if (accordionconfig == 0) {
			if (push != "" && push != undefined && push != null) {
				var pushurl = push;
				if (pushurl.indexOf("?") >= 0) {
					pushurl += "&pushid=" + $(this).attr("pushid");
				} else {
					pushurl += "?pushid=" + $(this).attr("pushid");
				}
				$.get(pushurl);
			}
			if ($(this).hasClass("active")) {
				$(this).next("tr.accordion_row").removeClass("active");
				$(this).removeClass("active");
			} else {
				if (single) {
					$(this).parents(".accordion").find(".active").removeClass("active");
				}
				$(this).next("tr.accordion_row").addClass("active");
				$(this).addClass("active");
				$('.active .auto-preview').unbind("autopreview").autopreview();
			}
		}
		accordionconfig = 0;
	});
}