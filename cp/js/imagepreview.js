$.fn.imagepreview = function() {
	var otm = null;
	var href = $(this).attr("href");
	$(this).unbind("mouseover").mouseover(function() {
		$(this).attr("href", $(this).attr("href").toLowerCase());
		if ($(this).attr("href").indexOf(".jpg") >= 0 || $(this).attr("href").indexOf(".jpeg") >= 0 || $(this).attr("href").indexOf(".png") >= 0 || $(this).attr("href").indexOf(".bmp") >= 0) {
			$.get("/cp/async/media/resize?src=" + href + "&width=200&height=200", function(data) {
				$("#imagepreview").html("<img src=\"" + $.trim(data) + "\" alt=\"preview\" />");
				$("#imagepreview").css({
					left: cmx + "px",
					top: cmy + "px"
				});
				$("#imagepreview").show();
			});
		}
	});
	$(this).unbind("mouseout").mouseout(function() {
		$("#imagepreview").hide();
		clearTimeout(otm);
	});
};