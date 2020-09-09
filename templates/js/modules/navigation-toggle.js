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