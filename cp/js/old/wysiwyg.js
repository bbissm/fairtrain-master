var wDoc = null;
var wContent = "";
var wEl = null;

function iref(name) {
	return document.getElementById(name).contentWindow
		? document.getElementById(name).contentWindow.document
		: document.getElementById(name).contentDocument
}

function formatDoc(oDoc, sCmd, sValue) {
	if (!validateMode(oDoc)) { return; }
	iref("web").execCommand(sCmd, false, sValue);
	if (oDoc != null && oDoc != undefined) oDoc.focus();
}

function validateMode(oDoc) {
	if (!$(oDoc).is(":checked")) { return true; }
	alert("Uncheck \u00AB" + sModeLabel + "\u00BB.");
	oDoc.focus();
	return false;
}

function extractText(oDoc) {
	if (oDoc.innerText) { return oDoc.innerText; }
	var oContent = document.createRange();
	oContent.selectNodeContents(oDoc.firstChild);
	return oContent.toString();
}

function setDocMode(oDoc, bToSource) {
	if (bToSource) {
		var oContent = document.createTextNode(oDoc.innerHTML), oPre = document.createElement("pre");
		oDoc.innerHTML = "";
		oDoc.contentEditable = false;
		oPre.className = "rte-sourcetext";
		oPre.id = "rte-source-" + oDoc.id;
		oPre.onblur = oDoc.onblur;
		oPre.contentEditable = true;
		oPre.appendChild(oContent);
		oDoc.appendChild(oPre);
	} else {
		oDoc.innerHTML = extractText(oDoc);
		oDoc.contentEditable = true;
	}
	oDoc.focus();
}

function menuSelect() {
	if (this.selectedIndex < 1) { return; }
	var sMenuGroup = rId.exec(this.id)[0], sCmd = this.id.slice(0, - sMenuGroup.length);
	formatDoc(aEditors[sMenuGroup], sCmd, this[this.selectedIndex].value);
	this.selectedIndex = 0;
}

function buttonClick() {
	var sBtnGroup = rId.exec(this.id)[0], sCmd = this.id.slice(0, - sBtnGroup.length);
	customCommands.hasOwnProperty(sCmd) ? customCommands[sCmd](aEditors[sBtnGroup]) : formatDoc(aEditors[sBtnGroup], sCmd, this.alt || false);
}

function changeMode() {
	setDocMode(aEditors[rId.exec(this.id)[0]], this.checked);
}

function updateField() {
	var sFieldNum = rId.exec(this.id)[0];
	document.getElementById("rte-field-" + sFieldNum).value = document.getElementById("rte-mode-" + sFieldNum).checked ? extractText(this) : this.innerHTML;
}

function createMenuItem(sValue, sLabel) {
	var oNewOpt = document.createElement("option");
	oNewOpt.value = sValue;
	oNewOpt.innerHTML = sLabel || sValue;
	return oNewOpt;
}

function createEditor(oTxtArea) {
	var nEditorId = aEditors.length, oParent = document.createElement("div"), oMenuBar = document.createElement("div"),
		oToolsBar = document.createElement("div"), oEditBox = document.createElement("div"),
		oModeBox = document.createElement("div"), oModeChB = document.createElement("input"),
		oModeLbl = document.createElement("label");

	oParent.className = "wysiwyg";
	oParent.id = oTxtArea.id || "rich-text-" + nEditorId;
	oMenuBar.className = "rte-menus";
	oToolsBar.className = "rte-tools";
	oEditBox.className = "rte-editbox";
	oEditBox.id = "rte-editbox-" + nEditorId;
	oEditBox.contentEditable = true;
	oEditBox.innerHTML = oTxtArea.innerHTML;
	aEditors.push(oEditBox);

	$(oEditBox).blur(function () {
		cms.activeContainer = "";

		var ourl = $(this).parents("*[cms=true]").eq(0).attr("action");
		if (ourl == "" || ourl == undefined) {
			ourl = $(oParent).attr("action");
			if (ourl == "" || ourl == undefined) {
				ourl = $(oParent).parent().attr("action");
			}
		}

		$.ajax({
			method: "POST",
			url: ourl,
			data: { html: $(this).html().replace(/'/g,"&apos;").replace(/\+/g,"&#43;") }
		})
			.done(function (msg) {
				dialog(parent.wysiwygSuccess, "success");
			})
			.fail(function () {
				dialog(parent.wysiwygError, "error");
			});
	});

	if (oTxtArea.form) {
		var oHiddField = document.createElement("input");
		oHiddField.type = "hidden";
		oHiddField.name = oTxtArea.name;
		oHiddField.value = oEditBox.innerHTML;
		oHiddField.id = "rte-field-" + nEditorId;
		oTxtArea.form.appendChild(oHiddField);
		oEditBox.onblur = updateField;
	}

	for (var oMenu, oMenuOpts, vOpt, nMenu = 0; nMenu < oTools.menus.length; nMenu++) {
		oMenu = document.createElement("select");
		oMenu.id = oTools.menus[nMenu].command + nEditorId;
		oMenu.onchange = menuSelect;
		oMenu.appendChild(createMenuItem(oTools.menus[nMenu].header));
		oMenuOpts = oTools.menus[nMenu].values;
		if (oMenuOpts.constructor === Array) {
			for (vOpt = 0; vOpt < oMenuOpts.length; oMenu.appendChild(createMenuItem(oMenuOpts[vOpt++])));
		} else {
			for (vOpt in oMenuOpts) { oMenu.appendChild(createMenuItem(vOpt, oMenuOpts[vOpt])); }
		}
		oMenu.selectedIndex = 0;
		oMenuBar.appendChild(document.createTextNode(" "));
		oMenuBar.appendChild(oMenu);
	}

	for (var oBtnDef, oButton, nBtn = 0; nBtn < oTools.buttons.length; nBtn++) {
		oBtnDef = oTools.buttons[nBtn];
		oButton = document.createElement("img");
		oButton.className = "rte-button";
		oButton.id = oBtnDef.command + nEditorId;
		oButton.src = oBtnDef.image;
		if (oBtnDef.hasOwnProperty("value")) { oButton.alt = oBtnDef.value; }
		oButton.title = oBtnDef.text;
		oButton.onclick = buttonClick;
		oToolsBar.appendChild(oButton);
	}

	oModeBox.className = "rte-switchmode";
	oModeChB.type = "checkbox";
	oModeChB.id = "rte-mode-" + nEditorId;
	oModeChB.onchange = changeMode;
	oModeLbl.setAttribute("for", oModeChB.id);
	oModeLbl.innerHTML = sModeLabel;
	oModeBox.appendChild(oModeChB);
	oModeBox.appendChild(document.createTextNode(" "));
	oModeBox.appendChild(oModeLbl);
	oParent.appendChild(oMenuBar);
	oParent.appendChild(oToolsBar);
	oParent.appendChild(oEditBox);
	oParent.appendChild(oModeBox);
	oTxtArea.parentNode.replaceChild(oParent, oTxtArea);
}

function toolsReady(data) {
	oTools = data;
}

function strip_tags(str) {
    str = str.toString();
    return str.replace(/<\/?[^>]+>/gi, '');
}

var oTools, nReady = 0, sModeLabel = "Show HTML", aEditors = [], rId = /\d+$/,
	customCommands = {
		"removeFormat": function (oDoc) {
			wContent = iref("web").getSelection();
			var range = wContent.getRangeAt(0);
			var element = range.startContainer;

			$(element).removeAttr('style')
			if (!$(element).hasClass("rte-editbox") && !$(element).parent().hasClass("rte-editbox")) {
				$(element).unwrap();
			}
		},
		"createLink": function (oDoc) {
			if (oDoc != null && oDoc != undefined) wDoc = oDoc;
			/*var sLnk = prompt("Write the URL here", "http:\/\/");
			if (sLnk && sLnk !== "http://"){ formatDoc(oDoc, "createlink", sLnk); alert($("#web").contents().find(':focus').html()); } */
			$.get("/cp/async/html/hyperlink", function (data) {
				$("#popup #popup_content").html(data);
				setup();
				$("#popup").fadeIn(250);

				wContent = iref("web").getSelection();
				var range = wContent.getRangeAt(0);
				var element = range.startContainer;

				$(element).find("a").each(function () {
					if ($(this).html() == wContent) {
						$("#popup input[name=url]").val($(this).attr("href"));
						$("#popup input[name=title]").val($(this).attr("title"));
						$("#popup select[name=target]").val($(this).attr("target"));
						$("#popup select[name=rel]").val($(this).attr("rel"));
						$("#popup select[name=class]").val($(this).attr("class"));
					}
				});

				$("#popup input[name=add]").click(function () {
					var url = $("#popup input[name=url]").val();
					if (url.indexOf("@") >= 0 && url.indexOf("mailto:") < 0) {
						url = "mailto:" + url;
					}

					formatDoc(wDoc, "insertHTML", "<a href=\"" + url + "\" target=\"" + $("#popup select[name=target]").val() + "\" rel=\"" + $("#popup select[name=rel]").val() + "\" class=\"" + $("#popup select[name=class]").val() + "\" title=\"" + $("#popup input[name=title]").val() + "\">" + wContent + "</a>");
					$("#popup #popup_content").html("");
					$("#popup").fadeOut(250);
				});
			});
		},
		"createImage": function(oDoc) {
			if(oDoc!=null && oDoc!=undefined) wDoc = oDoc; 

			$.get("/cp/async/html/image",function(data) { 
				$("#popup #popup_content").html(data);
				setup(); 
				$("#popup").fadeIn(250);   

				wContent = iref("web").getSelection();
				var range = wContent.getRangeAt(0);
				var element = range.startContainer;			

					$("#popup input[name=add]").click(function() {
					var imagesrc = $("#popup input[name=imagesrc]").val();
					var imagealt = $("#popup input[name=imagealt]").val();
					var img = "<img src=\""+imagesrc+"\" alt=\""+imagealt+"\" />"; 

					formatDoc(wDoc, "insertHTML", img); 
					$("#popup #popup_content").html("");
					$("#popup").fadeOut(250); 
				});
			});
		},
		"clearFormat": function(oDoc) {
			wContent = iref("web").getSelection();
			var range = wContent.getRangeAt(0); 
			var element = range.startContainer; 

			var txt = $(element).parents(".rte-editbox").html().replace(/<\/p>/g,"<\/p>\n\n");
			txt = jQuery.trim(strip_tags(txt)).split("\n\n");
			var tmp = txt;
			txt = "";
			$(tmp).each(function() {
				txt+="<p>"+this+"</p>";
			}); 

			$(element).parents(".rte-editbox").html(txt);
		}
	};

$.fn.wysiwyg = function (json = "/cp/js/wysiwyg.json") {
	var tagname = this;

	$.get(json, function (data) {
		toolsReady(data);

		$(tagname).each(function () {
			if ($(this).attr("id") == "" || $(this).attr("id") == undefined || $(this).attr("id") == null) {
				createEditor(this);
			}
		});
	});
};