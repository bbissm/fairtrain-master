function map(){
	if($("#map").length > 0){
		var map = L.map('map').setView([51.505, -0.09], 13);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		}).addTo(map);

		L.marker([51.5, -0.09], {color: '#634E42'}).addTo(map)
		.bindPopup('Fairtrain für Hund und Mensch')
	    .openPopup();
		

		map.scrollWheelZoom.disable();
		$("#map").clickToggle(function(){
			console.log("click1");
			map.scrollWheelZoom.enable();
			$("#map > div:nth-of-type(1)").removeClass("over-map");
		}, function(){
			console.log("click2");
			map.scrollWheelZoom.disable();
			$("#map > div:nth-of-type(1)").addClass("over-map");
		});
	}
}