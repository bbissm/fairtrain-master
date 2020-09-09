function instagramApi() {
	var ud_bilder = 999;
	var insta_token = '8980758823.fb9d3f1.9e8f69e3b361480dbd14c34444045258';
	$.ajax({
		url: "https://api.instagram.com/v1/users/self/media/recent/",
		type: "GET",
		data: {
			access_token: insta_token,
			count: ud_bilder
		},
		success: function(data) {
			console.log(data);
			for (i in data.data) {
				var url = data.data[i].images.low_resolution.url;
				$(".impressionen").append('<img src="' + url + '">');
			}
		}
	})
}

function cookie() {
	$("#cookie-close").click(function() {
		$("#cookie-banner").fadeOut();
		$.get("/async/form/setCookieAccept");
	});
}

function sendForm() {
	if ($("#anmeldung_form").length > 0) {
		$(".course").click(function() {
			// console.log($(this).val());
			$(".course:checked").each(function() {
				var course = $(this).val();
				$("#course").val(course);
				$("#seminar").val(course);
			});
		});
		// e.preventDefault();
		$('#anmeldung_submit').click(function() {
			var fields = $("input").serializeArray();
			$.each(fields, function(i, field) {
				if (!field.value) {
					// console.log(field);
					$("#" + field.name).css({
						"border": "1px red solid",
						"color": "red"
					});
				} else {
					$("#" + field.name).css({
						"border": "1px #634E42 solid",
						"color": "#634E42"
					});
				}
			});
			if (!$("input:checkbox").is(":checked")) {
				console.log($(this));
				$("input:checkbox").css({
					"border": "1px red solid",
					"color": "red"
				});
				$("input:checkbox").next("label").children("p").css({
					"color": "red"
				});
			} else {
				$("input:checkbox").css({
					"border": "1px #634E42 solid",
					"color": "black"
				});
				$("input:checkbox").next("label").children("p").css({
					"color": "#634E42"
				});
			}
			var form = $("#anmeldung_form").serialize();
			console.log(form);
			$.ajax({
				"method": "post",
				"url": "/async/form/anmelden",
				async: true,
				data: {
					formular: form
				},
				success: function(data) {
					console.log(data);
				}
			});
		});
	} else if ($("#contact_form").length > 0) {
		$("#contact_submit").click(function() {
			// e.preventDefault();
			var fields = $("input").serializeArray();
			$.each(fields, function(i, field) {
				if (!field.value) {
					$("#" + field.name).css({
						"border": "1px red solid",
						"color": "red"
					});
				} else {
					$("#" + field.name).css({
						"border": "1px #634E42 solid",
						"color": "#634E42"
					});
				}
			});
			if (!$("input:checkbox").is(":checked")) {
				$("input:checkbox").css({
					"border": "1px red solid"
				});
				$("input:checkbox").next("p").css({
					"color": "red"
				});
			} else {
				$("input:checkbox").css({
					"border": "1px #634E42 solid"
				});
				$("input:checkbox").next("p").css({
					"color": "#634E42"
				});
			}
			var form = $("#contact_form").serialize();
			$.ajax({
				"method": "post",
				"url": "/async/form/contact",
				async: true,
				data: {
					formular: form
				},
				success: function(data) {
					// console.log(data);
					if ($("input:checkbox").is(":checked")) {
						if ($.trim(data)) {
							alert("Danke für Ihre Kontaktaufnahme");
							// location.reload();
						}
					}
				}
			});
		});
	}
}

function addFieldToForm() {
	$("#plz").on('keyup', function(e) {
		var plz = $("#plz").val();
		$.ajax({
			success: function(data) {
				if (plz.indexOf('8') == 0 && plz.length == 4) {
					$('.birthday').css({
						"display": "flex"
					});
					$('.birthday').find("input").prop('disabled', false);
					$('.heimatort').css({
						"display": "flex"
					});
					$('.heimatort').children("input").prop('disabled', false);
				} else {
					$('.birthday').css({
						"display": "none"
					});
					// $('.birthday').find("input").prop('disabled', true);
					$('.heimatort').css({
						"display": "none"
					});
					// $('.heimatort').children("input").prop('disabled', true);
				}
			},
		});
	});
}

function buttonActive() {
	$('.btn').on('click', function() {
		$('.btn').not($(this)).removeClass('selected');
		if ($(this).hasClass('selected')) {
			$('.btn').removeClass('selected');
		} else {
			$(this).addClass('selected');
		}
	});
}

function active_container() {
	$(".container-swipe").on({
		mouseenter: function() {
			if (!$(this).hasClass("active-container")) {
				$(this).addClass("inside");
			}
		},
		mouseleave: function() {
			$(this).removeClass("inside");
		}
	});
	$(".cat_input").click(function() {
		$('.cat_input').not($(this)).parent("label").removeClass('active-container');
		$('.cat_input').not((this)).parent("label").removeClass("inside");
		if ($(this).is(':checked')) {
			$(this).parent("label").removeClass("inside");
			$(this).parent("label").addClass("active-container");
		} else {
			$(this).parent("label").removeClass("active-container");
		}
	});
}

function getKurse() {
	if ($("#kurse").length > 0) {
		$(".cat_input").change(function() {
			$(".cat_input").not($(this)).prop('checked', false);
			var form = $("#kurse").serialize();
			// console.log(form);
			$.ajax({
				"method": "post",
				"url": "/async/kurse/getCourseAjax",
				async: true,
				data: {
					category_id: form
				},
				success: function(data) {
					if (data != null) {
						// console.log(data);
						$(".fadeout_containers").replaceWith(data);
					}
				}
			});
		});
	}
}

function getSeminar() {
	if ($("#seminar").length > 0) {
		$(".cat_input").change(function() {
			$(".cat_input").not($(this)).prop('checked', false);
			var category = $(".cat_input").val();
			// console.log(category);
			var form = $("#seminar").serialize();
			// console.log(form);
			$.ajax({
				"method": "post",
				"url": "/async/seminar/getSeminarAjax",
				async: true,
				data: {
					category_id: form
				},
				success: function(data) {
					// console.log(data);
					$(".fadeout_containers").html(data);
				}
			});
		});
	}
}

function getLinks() {
	if ($("#links").length > 0) {
		$(".cat_input").change(function() {
			$(".cat_input").not($(this)).prop('checked', false);
			var category = $(".cat_input").val();
			// console.log(category);
			var form = $("#links").serialize();
			// console.log(form);
			$.ajax({
				"method": "post",
				"url": "/async/links/getLinksAjax",
				async: true,
				data: {
					category_id: form
				},
				success: function(data) {
					// console.log(data);
					$(".container_articles").html(data);
				}
			});
		});
	}
}

function map() {
	(function($) {
		$.fn.clickToggle = function(func1, func2) {
			var funcs = [func1, func2];
			this.data('toggleclicked', 0);
			this.click(function() {
				var data = $(this).data();
				var tc = data.toggleclicked;
				$.proxy(funcs[tc], this)();
				data.toggleclicked = (tc + 1) % 2;
			});
			return this;
		};
	}(jQuery));
	if ($("#map").length > 0) {
		var map = L.map('map').setView([51.505, -0.09], 13);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		}).addTo(map);
		L.marker([51.5, -0.09], {
			color: '#634E42'
		}).addTo(map).bindPopup('Fairtrain für Hund und Mensch').openPopup();
		map.scrollWheelZoom.disable();
		var tooltips = document.querySelectorAll('#map span');
		// if(!$("#map > div:nth-of-type(1)").hasClass("over-map")){
		window.onmousemove = function(e) {
			// alert();
			var x = (e.clientX + 20) + 'px',
				y = (e.clientY + 20) + 'px';
			for (var i = 0; i < tooltips.length; i++) {
				tooltips[i].style.top = y;
				tooltips[i].style.left = x;
			}
		};
		// }
		var enter_key = "c";
		$("#map").on("keypress", function(e) {
			// console.log(e);
			if (e.key == enter_key) {
				map.scrollWheelZoom.disable();
				$("#map > div:nth-of-type(1)").addClass("over-map");
			}
		});
		$("#map").click(function() {
			map.scrollWheelZoom.enable();
			$("#map > div:nth-of-type(1)").removeClass("over-map");
		});
	}
}

function navigation() {
	var hamburger = document.getElementById('toggle-nav');
	hamburger.addEventListener('click', function(e) {
		var lang = document.getElementById('bottom-nav');
		var list = document.getElementById('nav-list');
		var topNav = document.getElementById('top-nav');
		var navContainer = document.getElementById('nav-box');
		var bd = document.getElementsByTagName('body')[0];
		lang.classList.toggle("hide");
		list.classList.toggle("hide");
		topNav.classList.toggle("resize-top");
		navContainer.classList.toggle("resize");
		bd.classList.toggle('no-scroll');
	});
}

function navi() {
	$(".test").parent("div").addClass("header-container");
	var input = $("#header").val();
	// console.log(input);
	// if(input == "home"){
	// 	$(".header-img div").css({"height":"75vh"});
	// }else if(input == "ueber-uns" || input == "about-us" || input == "Su di noi"){
	// 	$(".header-img div").css({"height":"62vh"});
	// }
	$(".network_table").parent(".container").css({
		"max-width": "1050px"
	});
	$(".network_table").parent(".container").find(" > p").css({
		"max-width": "910px",
		"margin": "0 auto",
		"margin-bottom": "100px"
	});
}

function slider_container() {
	$('.slider_container').each(function() {
		if (!$(this).hasClass("slider slick-initialized")) {
			$(this).slick({
				slidesToShow: 2,
				slidesToScroll: 1,
				dots: false,
				arrows: true,
				nextArrow: '<i class="fas fa-chevron-right myArrowNext"></i>',
				prevArrow: '<i class="fas fa-chevron-left myArrowPrev"></i>',
			});
		}
	});
	$('.slider-header').each(function() {
		if (!$(this).hasClass("slider slick-initialized")) {
			$(this).slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				dots: true,
				arrows: false
			});
		}
	});
}

function sliderResize() {
	$('.slider-header').each(function() {
		$(this).css({
			height: ($(window).width() / 16 * 9) + "px",
			"max-height": "75vh"
		});
	});
}
function checkIframe() {
	if (inIframe() == true && ownWindowScroll > 200) {
		$("nav.startup").css('pointer-events', 'none');
	} else if ($("nav.startup").css('pointer-events') == "none") {
		$("nav.startup").css('pointer-events', 'all');
	}
}

function inIframe() {
	try {
		return window.self !== window.top;
	} catch (e) {
		return true;
	}
}
document.addEventListener('DOMContentLoaded', function() {
	cookie();
	slider_container();
	sliderResize();
	sendForm();
	getKurse();
	getSeminar();
	getLinks();
	navi();
	buttonActive();
	active_container();
	navigation();
	if (location.href == "http://fairtrain-web2019.dev.dimaster.ch/") {
		location.href = "http://fairtrain-web2019.dev.dimaster.ch/de/";
	}
});
$(window).resize(function() {
	sliderResize();
	if(inIframe() == true){
		slider_container();
	}
});
