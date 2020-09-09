var args = "";
$.fn.dragmove = function() {
    var dragable_dragobj = null;
    var container = null;
    var startIndex = 0;
    var containerIndex = 0;
    $(this).unbind("mousedown").mousedown(function() {
        args = $(this).parents("container").eq(0).attr("arguments");
        $("#web").contents().find("container").addClass("draging");
        dragable_dragobj = this;
        startIndex = $(dragable_dragobj).parent().index();
        container = $(dragable_dragobj).parents("container").eq(0).attr("name");
        containerIndex = $(dragable_dragobj).parents("container").eq(0);
        $(dragable_dragobj).parent().css({
            opacity: "0.5"
        });
        $(dragable_dragobj).parents("container").eq(0).find("*[cms=true]").unbind("mouseup").mouseup(function() {
            $(this).find("*[cms=true]").css({
                opacity: "1"
            });
            var offset = $(dragable_dragobj).parents("container[name='" + container + "']").attr("start");
            if (offset == NaN || offset == null || offset == undefined || offset == "") offset = 0;
            else offset = parseInt(offset);
            $.get("/cp/async/container/move?moveFrom=" + (startIndex + offset) + "&moveTo=" + ($(this).index() + offset) + "&container=" + container, function() {
                $.get("/async/container/render?arguments=" + args + "&output=1&load=" + container + "&start=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("start") + "&count=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("count") + "&path=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("path"), function(data) {
                    $(containerIndex).html(data);
                    setup();
                    cms.bindContainer();
                    fireResizeiFrame();
                });
            });
            $("#web").contents().find("container").removeClass("draging");
        });
        $(dragable_dragobj).parents("container").eq(0).prev().eq(0).unbind("mouseup").mouseup(function() {
            $(this).find("*[cms=true]").css({
                opacity: "1"
            });
            var offset = $(dragable_dragobj).parents("container[name='" + container + "']").attr("start");
            if (offset == NaN || offset == null || offset == undefined || offset == "") offset = 0;
            else offset = parseInt(offset);
            $.get("/cp/async/container/move?moveFrom=" + (startIndex + offset) + "&moveTo=" + (-1 + offset) + "&container=" + container, function() {
                $.get("/async/container/render?arguments=" + args + "&output=1&load=" + container + "&start=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("start") + "&count=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("count") + "&path=" + $(dragable_dragobj).parents("container[name='" + container + "']").attr("path"), function(data) {
                    $(containerIndex).html(data);
                    setup();
                    cms.bindContainer();
                    fireResizeiFrame();
                });
            });
            $("#web").contents().find("container").removeClass("draging");
            startIndex = 0;
        });
    });
    $(this).parents("body").unbind("mouseup").mouseup(function() {
        startIndex = 0;
        $(this).find("*[cms=true]").css({
            opacity: "1"
        });
        $("#web").contents().find("container").removeClass("draging");
    });
};