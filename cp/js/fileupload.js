var asyncFileUploadIndex = 0;
var uploading = 0;
var uploadUrl = "";
var fileobject = [];
$.fn.fileupload = function() {
    var obj = this;
    uploadUrl = $(this).parents("form").eq(0).attr("action");
    if ($(this).attr("url") != "" && $(this).attr("url") != null && $(this).attr("url") != undefined) {
        uploadUrl = $(this).attr("url");
    }
    if ($(this).attr("target") != "" && $(this).attr("target") != undefined && $(this).attr("target") != null) {
        container = $(this).attr("target");
    } else {
        container = "#wrapper";
    }
    /*$(this).find("div").eq(0).unbind("click").click(function() {
        $(this).parents(".dragdropupload").eq(0).find("input[type=file]").click();
    });*/
    $(this).find("input[type=file]").eq(0).unbind("change").bind("change", function(e) {
        var files = $(this).prop("files");
        $(files).each(function() {
            asyncFileUpload(url, this, obj);
        });
    });
    $(this).unbind("dragover").bind("dragover", function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).addClass("over");
        console.log("over");
    });
    $(this).unbind("dragleave").bind("dragleave", function(e) {
        e.stopPropagation();
        e.preventDefault();
        $(this).removeClass("over");
        console.log("left");
    });
    $(this).unbind("drop").bind("drop", function(e) {
        e.stopPropagation();
        e.preventDefault();
        obj = this;
        $(this).removeClass("over");
        var newUrl = $(this).attr("url");
        if (newUrl == "" || newUrl == undefined || newUrl == null) newUrl = uploadUrl;
        if (e.originalEvent.dataTransfer) {
            if (e.originalEvent.dataTransfer.files.length) {
                $(e.originalEvent.dataTransfer.files).each(function() {
                    //asyncFileUpload(uploadUrl,this,obj);
                    asyncFileUpload(newUrl, this, obj);
                });
            }
        }
    });
};

function asyncFileUpload(url, file, obj) {
    var formdata = new FormData();
    formdata.append('file', file);
    $.ajaxSetup({
        async: true
    });
    $.ajax({
        url: url,
        async: true,
        data: formdata,
        processData: false,
        contentType: false,
        type: 'POST',
        obj: obj,
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            var fileindex = asyncFileUploadIndex;
            asyncFileUploadIndex++;
            uploading++;
            this.fileindex = fileindex;
            $(this.obj).find("#progress").append("<div id=\"du" + fileindex + "\"><div class=\"filename\">" + this.data.get("file").name + "</div><div class=\"progress\"></div></div>");
            fileobject[fileindex] = this.obj;
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        $(fileobject[fileindex]).find("#progress").find("#du" + fileindex).find(".progress").css({
                            width: parseInt(100.0 / e.total * e.loaded) + "%"
                        });
                    }
                }, false);
            }
            return myXhr;
        },
        success: function(data) {
            $(this.obj).find("#progress").find("#du" + this.fileindex).find(".progress").css({
                width: "100%"
            });
            uploading--;
            console.log(uploading);
            if (uploading == 0) {
                $(container).html(data);
                setup();
            }
        },
        error: function() {
            $(this.obj).find("#progress").find("#du" + this.fileindex).find(".progress").css({
                width: "100%",
                backgroundColor: "rgba(0,200,0,0.5)"
            });
        }
    });
}