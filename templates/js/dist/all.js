// function buttonActive(){
// 	$('.btn').on('click', function(){
// 		if($(this).hasClass('selected')){
// 			$(this).removeClass('selected');
// 		}else{
// 			$(this).addClass('selected');
// 		}
// 	    if($(".row_article").hasClass("hide")){
// 			$(".row_article").removeClass("hide");
// 			$(".row_article").fadeIn(700);
// 		}else{
// 			$(".row_article").addClass("hide");
// 			$(".row_article").fadeOut(700);
// 		}
// 		if($(".article").hasClass("hide")){
// 			$(".article").removeClass("hide");
// 			$(".article").fadeIn(700);
// 		}else{
// 			$(".article").addClass("hide");
// 			$(".article").fadeOut(700);
// 		}
// 	});

// 	$('.selected_btn').on('click', function(){
// 		if($(this).hasClass('selected')){
// 			$(this).removeClass('selected');
// 		}else{
// 			$(this).addClass('selected');
// 		}
// 	});


// 	$('.filter-btn-a,.filter-btn-b').on('click', function(){
// 		$(this).addClass('selected');
// 		if($(this).hasClass('filter-btn-a')){
// 			$('.filter-btn-b').removeClass('selected');
// 			$('.filter-btn-a').addClass('selected');
// 		}else{
// 			$('.filter-btn-a').removeClass('selected');
// 			$('.filter-btn-b').addClass('selected');
// 		}
	

// 		if($(".row_article").hasClass("hide")){
// 			$(".row_article").removeClass("hide");
// 			$(".row_article").fadeIn(700);
// 		}else{
// 			$(".row_article").addClass("hide");
// 			$(".row_article").fadeOut(700);
// 		}
// 	});
	
// }
// function active_container(){
// 	$(".container-swipe").on({
// 		click: function() {
// 		    $(this).toggleClass("active-container");
// 		    $(this).removeClass("inside");
// 		    if($(".row_article").hasClass("hide")){
// 		 		$(".row_article").removeClass("hide");
// 		 		$(".row_article").fadeIn(700);
// 		 	}else{
// 		 		$(".row_article").addClass("hide");
// 				$(".row_article").fadeOut(700);
// 		 	}
// 		}, 

// 		mouseenter: function() {
// 		  	if(!$(this).hasClass("active-container")){
// 		    	$(this).addClass( "inside" );
// 		  	}
// 		},

// 		mouseleave: function() {
// 	    	$(this).removeClass("inside");
// 		}
// 	});
// }
// function map(){
// 	(function($) {
// 		$.fn.clickToggle = function(func1, func2) {
// 			var funcs = [func1, func2];
// 			this.data('toggleclicked', 0);
// 			this.click(function() {
// 				var data = $(this).data();
// 				var tc = data.toggleclicked;
// 				$.proxy(funcs[tc], this)();
// 				data.toggleclicked = (tc + 1) % 2;
// 			});
// 			return this;
// 		};
// 	}(jQuery));
// 	if($("#map").length > 0){
// 		var map = L.map('map').setView([51.505, -0.09], 13);

// 		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
// 		    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
// 		}).addTo(map);

// 		L.marker([51.5, -0.09], {color: '#634E42'}).addTo(map)
// 		.bindPopup('Fairtrain für Hund und Mensch')
// 	    .openPopup();
		
		

// 		map.scrollWheelZoom.disable();
// 		$("#map").clickToggle(function(){
// 			console.log("click1");
// 			map.scrollWheelZoom.enable();
// 			$("#map > div:nth-of-type(1)").removeClass("over-map");
// 		}, function(){
// 			console.log("click2");
// 			map.scrollWheelZoom.disable();
// 			$("#map > div:nth-of-type(1)").addClass("over-map");
// 		});
// 	}
// }
// function navigation() {
// 	var hamburger = document.getElementById('toggle-nav');
// 	hamburger.addEventListener('click', function(e) {
// 		var lang = document.getElementById('bottom-nav');
// 		var list = document.getElementById('nav-list');
// 		var topNav = document.getElementById('top-nav');
// 		var navContainer = document.getElementById('nav-box');
// 		var bd = document.getElementsByTagName('body')[0];

// 		lang.classList.toggle("hide");
// 		list.classList.toggle("hide");
// 		topNav.classList.toggle("resize-top");
// 		navContainer.classList.toggle("resize");
// 		bd.classList.toggle('no-scroll');
// 	});
// }
// function slider_container(){
// 	$('.slider_container').slick({
// 		slidesToShow: 2,
// 	    slidesToScroll: 1,
// 	    dots: false,
// 	    arrows: true,
//   		nextArrow: '<i class="fas fa-chevron-right myArrowNext"></i>',
//   		prevArrow: '<i class="fas fa-chevron-left myArrowPrev"></i>',
// 	});
// 	$('.slider-header').slick({
// 		slidesToShow: 1,
// 	    slidesToScroll: 1,
// 	    dots: true,
// 	    arrows: false
// 	});
// }

// function sendForm(){
// 	$("form.user_form, form.js_user_form").unbind("ajaxForm").ajaxForm({ 
// 		success: function(res, status, xhr, form) {
// 			form.find("input[type=submit]").after("<p style=\"display: none;\" class=\"form_message\">" + res + "</p>");
// 			form.find("input[type=submit]").slideUp(function(){ 
// 				$(".form_message").slideDown();
// 			});
//     	}
// 	});
// }

// function styleInput(){
// 	var count = 1;
// 	var obj = $("form.user_form input[type=radio], form.user_form input[type=checkbox], form.js_user_form input[type=radio], form.js_user_form input[type=checkbox]");

// 	obj.each(function(){
// 		var text = $(this).attr("text");
// 		var checked = $(this).prop("checked") ? " selected" : "";
// 		var name = $(this).attr("name");
// 		var value = $(this).attr("value");
// 		var id = $(this).attr("id") == undefined ? $(this).attr("type")+"_"+count : $(this).attr("id");
// 		if($(this).next().attr("for")!="") {
// 			$(this).next().remove();
// 		}
// 		$(this).after("<label for=\""+id+"\" class=\"styledInput\"><div class=\""+$(this).attr("type")+checked+"\"></div>"+text+"<br /></label>");
// 		$(this).attr("id", id);
// 		count++;
// 		$(this).hide();
// 	});

// 	obj.unbind("change").change(function(){
// 		var id = $(this).attr("id");
// 		var type = $(this).attr("type");
// 		var name = $(this).attr("name");
// 		if(type == "checkbox"){
// 			$("label[for="+id+"]").find("div").toggleClass("selected");
// 		}else{

// 			$("input[name="+name+"]").each(function(){
// 				var fr = $(this).attr("id");
// 				$("label[for="+fr+"] div").removeClass("selected");
// 			});

// 			$("label[for="+id+"] div").addClass("selected");
// 		}
// 	}); 
// }

// document.addEventListener('DOMContentLoaded',function(){
// 	navigation();
// 	slider_container();
// 	buttonActive();
// 	active_container();
// 	map();
// 	styleInput();
// });
