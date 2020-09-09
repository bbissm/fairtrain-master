$.fn.sortable = function() {
    var sortable_state = 0;
    $(this).find("tr.subtitle td").css({
        cursor: "pointer"
    });
    $(this).find("tr.subtitle td").click(function() {
        $(".arrow .arrow-up,.arrow .arrow-down").remove();
        $(".arrow span").unwrap();
        var sortable_tmp = $(this).html();
        if (sortable_tmp.indexOf("<span") >= 0) {
            sortable_tmp = sortable_tmp.replace("<span>", "").replace("</span>", "");
        }
        $(this).html("<div class=\"arrow\"><span>" + sortable_tmp + "</span><div></div></div>");
        if (sortable_state == 0) {
            $(this).find(".arrow div").addClass("arrow-down");
        } else {
            $(this).find(".arrow div").addClass("arrow-up");
        }
        var sortable_arr = [];
        var sortable_sarr = [];
        var sortable_tableobj = $(this).parents("table");
        var sortable_tindex = $(this).index();
        var sortable_offset = $(sortable_tableobj).find("tr.title").length + $(sortable_tableobj).find("tr.subtitle").length;
        $(sortable_tableobj).find("tr.row").each(function() {
            sortable_arr.push($(this));
        });
        $(sortable_tableobj).find("tr.row").each(function(index) {
            var sortable_atxt = $(this).find(" > td").eq(sortable_tindex).html();
            if (sortable_atxt.match(/^\d+$/)) {
                for (var i = sortable_atxt.length; i < 10; i++) {
                    sortable_atxt = "0" + sortable_atxt;
                }
            }
            sortable_sarr.push(sortable_atxt + "////" + ($(this).index() - sortable_offset));
        });
        if (sortable_state == 0) {
            sortable_sarr.sort();
            sortable_state = 1;
        } else {
            sortable_state = 0;
        }
        $(sortable_tableobj).find("tr.row").remove();
        $(sortable_sarr).each(function() {
            var sortable_id = this.split("////");
            sortable_id = sortable_id[sortable_id.length - 1];
            $(sortable_tableobj).find("tr.subtitle").after(sortable_arr[sortable_id]);
        });
        setupIcons();
    });
};