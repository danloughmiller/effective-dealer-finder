function effdf_initialize_autocomplete(input)
{
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.setFields(['formatted_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);
    
    return autocomplete;
}



function effdf_setMarkerData(data) 
{
    var map = effdf_map;
	markerData = data;
	
	//Setup new marker data
	for(i=0; i<markers.length; i++){
		markers[i].setMap(null);
	}
	
	var bounds = new google.maps.LatLngBounds();
	
	markers = data.map(function(dealer, i) { 
	  var marker = new google.maps.Marker({ 
		position: dealer.location,
		map: map
	  });
	  
	  bounds.extend(dealer.location);
	  
	  google.maps.event.addListener(marker, 'click', (function(marker, i) {
		  return function() {
			  infowindow.setContent(markerData[i].infoWindowHtml);
			  infowindow.open(map, marker); 
		  }
	  })(marker,i));
	  
	  return marker;
	});
	
	map.fitBounds(bounds);
}