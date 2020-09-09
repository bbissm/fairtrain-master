var inGet = false;
var timer;
$.fn.filesearch = function() {
	var rootObject = this;
	$(this).find("input[name='search-filesearch']").unbind("click").click(function() {
		var obj = $(this).parent().find("input[name='find-filesearch']");
		var target = $(this).parent().find("input[name='target-filesearch']");
		var type = $(this).parent().attr("type");
		if (inGet == false) {
			inGet = true;
			$.get("/cp/async/media/" + type + "?search=" + obj.val() + "&target=" + target.val(), function(data) {
				inGet = false;
				obj.parent().parent().html(data);
				$('.auto-preview').autopreview();
				setTimeout(function() {
					$(".fileselector").scroll(function() {
						$('.auto-preview').autopreview();
					});
				}, 1000);
				setup();
			});
		}
	});
}