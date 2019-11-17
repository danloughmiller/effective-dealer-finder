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
        jQuery('.effective-dealer-search-filter input.effdf_location_search').each(function() {
            var autocomplete = new google.maps.places.Autocomplete(this);
            autocomplete.setFields(['formatted_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);

            autocomplete.addListener('place_changed', function() {
                var place = this.getPlace();
                if (!place || !place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    var geocoder = new google.maps.Geocoder();
                    var address = jQuery(input).val();
                    geocoder.geocode({'address': address}, function(results, status) {
                        if (status === 'OK') {
                            var loc = results[0].geometry.location;
                            var lat = loc.lat();
                            var lng = loc.lng();

                            jQuery('input.ds-lat').val(lat);
                            jQuery('input.ds-lng').val(lng);
                            effdf_runDealerSearch()
                        } else {
                            
                        }
                    });	
                    
                    return false;
                }

                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();

                jQuery('input.ds-lat').val(lat);
                jQuery('input.ds-lng').val(lng);
                effdf_runDealerSearch()
                
                return false; 
                
            });
        });
    }
    
    

}



function effdf_runDealerSearch() 
{
	jQuery(document).ready(function($) {
		var data = {
			'action': 'distributor_search', 
        };
        
        jQuery('.effective-dealers-filters *[name]').each(function() {
            data[jQuery(this).attr('name')] = jQuery(this).val();
        });

        console.log(data);

        jQuery('.effective-dealers-filters > form').submit();
        
		
		jQuery('#distributor-results-container').html('<div class="dealer-spinner"></div>');

		jQuery.post(distributor_data.ajax_url, data, function(response) {
			jQuery('#distributor-results-container').html(response.html);
			setMarkerData(response.markers);
			
			
		}, 'json'); 
	});
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
        var mdata = { 
            position: dealer.location,
            map: map
        };
        console.log('icon: ' + dealer.icon);
        if (dealer.icon != undefined && dealer.icon != '') {
            var icon = {
                url: dealer.icon,
            };

            if (dealer.icon_width)
            icon.scaledSize = new google.maps.Size(dealer.icon_width, dealer.icon_height);

             mdata = { 
                position: dealer.location,
                map: map,
                icon: icon
            };            
        }

        console.log(mdata);

        var marker = new google.maps.Marker(mdata);

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