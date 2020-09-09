function slider_container(){
	$('.slider_container').slick({
		slidesToShow: 2,
	    slidesToScroll: 1,
	    dots: false,
	    arrows: true,
  		nextArrow: '<i class="fas fa-chevron-right myArrowNext"></i>',
  		prevArrow: '<i class="fas fa-chevron-left myArrowPrev"></i>',
	});

}