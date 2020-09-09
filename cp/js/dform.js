$.fn.dform = function() {
    $(window).keydown(function(event) {
        if (event.keyCode == 13 && $("input:focus").hasClass("nosubmit")) {
            $("input[name=search-filesearch]").click();
            event.preventDefault();
            return false;
        }
    });
    $(this).unbind("submit").submit(function() {
        var error = false;
        var data = {};
        var in1 = 0;
        var target = "#wrapper";
        if (cropperactive) {
            $(".cropper img").each(function() {
                var dat = $(this).cropper("getData");
                $(this).cropper("destroy");
                dat.src = $(this).attr("src");
                dat.targetWidth = $(this).attr("targetWidth");
                dat.targetHeight = $(this).attr("targetHeight");
                $("input[name=" + $(this).attr("target") + "]").val("crop:" + dat.x + "," + dat.y + "," + dat.width + "," + dat.height + "," + dat.src + "," + dat.targetWidth + "," + dat.targetHeight);
            });
        }
        $(this).find("input,textarea,button,select").each(function() {
            if ($(this).attr("drequired") == 1) {
                in1 = 1;
            }
            if ($(this).attr("drequired") == 1 && $(this).attr("type") == "email" && !validateEmail($(this).val())) {
                error = true;
                $(this).addClass("error");
            } else if ($(this).attr("drequired") == 1 && $(this).val() == "") {
                error = true;
                $(this).addClass("error");
            } else if ($(this).attr("dsrequired") == 1) {
                $(this).removeClass("error");
            }
        });
        if (in1 == 0) {
            error = false;
        }
        if (error) {
            return false;
        }
        var scrollTo = 0;
        if ($(this).attr("data-slide-to-element") == 1 && $(this).find("div.fileselector").length > 0) {
            scrollTo = $(this).find("div.fileselector").scrollTop();
        }
        var href = $(this).attr("action").split("/");
        if (href[2] == "async") {
            $(this).find("input,textarea,button,select").each(function() {
                if ($(this).attr("type") == "checkbox" && $(this).is(":checked")) {
                    eval("data['" + $(this).attr("name") + "']='" + encodeURI($(this).val()).replace(/'/g, "&apos;").replace(/\+/g, "&#43;") + "';");
                } else if ($(this).attr("type") != "checkbox") {
                    if ($(this).attr("type") != "file") {
                        eval("data['" + $(this).attr("name") + "']='" + encodeURI($(this).val()).replace(/'/g, "&apos;").replace(/\+/g, "&#43;") + "';");
                    }
                }
            });
            if ($(this).attr("method") == "") {
                $(this).attr("method", "GET");
            }
            if ($(this).attr("target") != "" && $(this).attr("target") != undefined && $(this).attr("target") != null) {
                target = $(this).attr("target");
            }
            $.ajaxSetup({
                async: true
            });
            $.ajax({
                url: $(this).attr("action"),
                async: true,
                data: data,
                method: $(this).attr("method")
            }).done(function(data) {
                $(target).html(data);
                cropperactive = false;
                setup();
                if (scrollTo != 0) {
                    $(target).find("div.fileselector").scrollTop(scrollTo);
                }
            });
            return false;
        }
    });
};