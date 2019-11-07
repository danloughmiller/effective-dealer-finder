var effdf_map;
var markerData = [];
var markers = [];
var infowindow=false;

function init_effdf_public() {

    //Initialize Map
    var mapEl = jQuery('.effdf-map-container');
    
	if (jQuery(mapEl).length>0) {
        
        mapEl = mapEl[0];
		effdf_map = new google.maps.Map(mapEl, { 
          zoom: 3,
		  center: {lat: 0, lng: 1 }
        });

        //Setup infowindow
        infowindow = new google.maps.InfoWindow({content:"", maxWidth: 350});
 
		effdf_setMarkerData(dealer_data.dealers);
		

		//var markerCluster = new MarkerClusterer(map, markers,
        //    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'}); 

    }
    
    

}

console.log(dealer_data.dealers);