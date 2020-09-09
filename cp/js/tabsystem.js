$.fn.tabsystem = function() {
	var push = $(this).attr("push");
	$(this).find(".tabs .tab").click(function() {
		if (push != "" && push != undefined && push != null) {
			var pushurl = push;
			if (pushurl.indexOf("?") >= 0) {
				pushurl += "&pushid=" + $(this).index();
			} else {
				pushurl += "?pushid=" + $(this).index();
			}
			$.get(pushurl);
		}
		var index = $(this).index();
		$(this).parents(".tabsystem").eq(0).find(".active").removeClass("active");
		$(this).addClass("active");
		$(this).parents(".tabsystem").eq(0).find(".tabcontent").eq(index).addClass("active");
	});
}