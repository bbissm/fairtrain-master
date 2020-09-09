function buttonActive(){
	$('.btn').on('click', function(){
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
		}else{
			$(this).addClass('selected');
		}
		
	    if($(".row_article").hasClass("hide")){
			$(".row_article").removeClass("hide");
			$(".row_article").fadeIn(700);
		}else{
			$(".row_article").addClass("hide");
			$(".row_article").fadeOut(700);
		}

		if($(".article").hasClass("hide")){
			$(".article").removeClass("hide");
			$(".article").fadeIn(700);
		}else{
			$(".article").addClass("hide");
			$(".article").fadeOut(700);
		}
	});

	$('.selected_btn').on('click', function(){
		if($(this).hasClass('selected')){
			$(this).removeClass('selected');
		}else{
			$(this).addClass('selected');
		}
	});


	$('.filter-btn-a,.filter-btn-b').on('click', function(){
		$(this).addClass('selected');
		if($(this).hasClass('filter-btn-a')){
			$('.filter-btn-b').removeClass('selected');
			$('.filter-btn-a').addClass('selected');
		}else{
			$('.filter-btn-a').removeClass('selected');
			$('.filter-btn-b').addClass('selected');
		}
	

		if($(".row_article").hasClass("hide")){
			$(".row_article").removeClass("hide");
			$(".row_article").fadeIn(700);
		}else{
			$(".row_article").addClass("hide");
			$(".row_article").fadeOut(700);
		}
	});
	
}