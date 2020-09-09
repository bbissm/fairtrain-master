var autopreviewloaded = [];
$.fn.isInViewport = function() {
	var elementTop = $(this).offset().top;
	if (elementTop == 0) elementTop = 1000000;
	var elementBottom = elementTop + $(this).outerHeight();
	var viewportTop = $(window).scrollTop();
	var viewportBottom = viewportTop + $(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};
$.fn.autopreview = function() {
	$(this).each(function() {
		var otm = $(this);
		var href = $(this).attr("href");
		if ($(this).attr("href").toLowerCase().indexOf(".jpg") >= 0 || $(this).attr("href").toLowerCase().indexOf(".jpeg") >= 0 || $(this).attr("href").toLowerCase().indexOf(".png") >= 0 || $(this).attr("href").toLowerCase().indexOf(".bmp") >= 0) {
			if ($(this).isInViewport()) {
				//autopreviewloaded.push(href);
				if ($(otm).html() == "") {
					$(otm).html("<a></a>");
					$.get("/cp/async/media/resize?src=" + href + "&width=100&height=100", function(data) {
						$(otm).html("<img src=\"" + $.trim(data) + "\" alt=\"preview\" />");
					});
				}
			}
		}
	});
};