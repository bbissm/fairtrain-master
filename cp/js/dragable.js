$.fn.dragable = function() {
    var dragable_dragobj = null;
    var dragable_tableobj = this;
    var dclass = "";
    var initIndex = 0;
    var iconClicked = 0;
    $(".icon").unbind("mousedown").mousedown(function() {
        iconClicked = 1;
    });
    $(this).find("tr.row").css({
        cursor: "pointer"
    });
    $(this).find("tr.row").unbind("mousedown").mousedown(function() {
        if (iconClicked == 0) {
            var dcl = $(this).attr("parent");
            if (dcl == null || dcl == undefined || dcl == "") {
                dcl = "";
            }
            if (dcl.length > 0) {
                dclass = ".row[parent=" + dcl + "]";
            } else {
                dclass = ".row";
            }
            initIndex = $(this).parents("table").eq(0).find(dclass).index(this);
            dragable_dragobj = this;
            $(this).css({
                opacity: 0.2
            });
            $(this).parents("table").eq(0).find(dclass).mousemove(function() {
                $(dragable_tableobj).find("tr").removeClass("dragdrop");
                $(this).addClass("dragdrop");
            });
            $(this).parents("table").eq(0).find(dclass).eq(0).prev("tr").mousemove(function() {
                $(dragable_tableobj).find("tr").removeClass("dragdrop");
                $(this).addClass("dragdrop");
            });
            $(this).parents("table").eq(0).find(dclass).unbind("mouseup").mouseup(function() {
                $(dragable_tableobj).find("tr").unbind("mousemove").removeClass("dragdrop").css({
                    opacity: 1
                });
                if (this != dragable_dragobj) {
                    var data = {
                        source: $(this).parents("form").eq(0).attr("source"),
                        parent: $(this).attr("parent"),
                        startPos: initIndex,
                        endPos: $(this).parents("table").find(dclass).index(this)
                    };
                    $.ajax({
                        url: $(this).parents("form").eq(0).attr("action"),
                        data: data
                    }).done(function(data) {
                        if ($("#popup_content").html() != "") {
                            $("#popup_content").html(data);
                        } else {
                            $("#wrapper").html(data);
                        }
                        setup();
                    });
                }
            });
            $(this).parents("table").eq(0).find(dclass).eq(0).prev("tr").unbind("mouseup").mouseup(function() {
                $(dragable_tableobj).find("tr").unbind("mousemove").removeClass("dragdrop").css({
                    opacity: 1
                });
                if (this != dragable_dragobj) {
                    var data = {
                        source: $(this).parents("form").eq(0).attr("source"),
                        parent: $(this).parents("table").find(dclass).eq(0).attr("parent"),
                        startPos: initIndex,
                        endPos: -1
                    };
                    $.ajax({
                        url: $(this).parents("form").eq(0).attr("action"),
                        data: data
                    }).done(function(data) {
                        if ($("#popup_content").html() != "") {
                            $("#popup_content").html(data);
                        } else {
                            $("#wrapper").html(data);
                        }
                        setup();
                    });
                }
            });
        }
        iconClicked = 0;
    });
    $(document).mouseup(function() {
        $(dragable_tableobj).find("tr").unbind("mousemove").removeClass("dragdrop").css({
            opacity: 1
        });
        iconClicked = 0;
    });
};