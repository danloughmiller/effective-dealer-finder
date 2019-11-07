function init_effdf_public() {
console.log('yu');
    //Initialize Map
    var mapEl = jQuery('.effdf-map-container');
    console.log(mapEl);
	if (jQuery(mapEl).length>0) {
        console.log('x');
        mapEl = mapEl[0];
		map = new google.maps.Map(mapEl, { 
          zoom: 3,
		  center: {lat: 0, lng: 1 }
        });
 
		setMarkerData(dealer_data.dealers);
		

		//var markerCluster = new MarkerClusterer(map, markers,
        //    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'}); 

    }
    
    

}

console.log(dealer_data.dealers);