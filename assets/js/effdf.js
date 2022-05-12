//effdf_marker_click(marker, i) occurs when a marker is clicked
//effdf_marker_clear called when map data is cleared
//effdf_marker_before(map, dealer, mdata) called before a marker is created
//effdf_marker_after(map, dealer, mdata, marker) called after the marker is created
//effdf_map_before_setup(mapParams, mapEl) called before map setup
//effdf_map_after_setup(effdf_map) called after map setup
//effdf_markers_after(map, markers) called after markers added

var effdf_map;
var markerData = [];
var markers = [];
var infowindow=false;
var effdf_hold_update = false;

function init_effdf_public() {

    //Initialize Map
    var mapEl = jQuery('.effdf-map-container');
    
	if (jQuery(mapEl).length>0) {
        
        mapEl = mapEl[0];
        var mapParams = { 
            zoom: 3,
            maxZoom:9,
            center: {lat: 0, lng: 1 }
        };

        if (typeof effdf_map_before_setup === 'function') {
            mapParams = effdf_map_before_setup(mapParams, mapEl);
        }

		effdf_map = new google.maps.Map(mapEl, mapParams);

        if (typeof effdf_map_after_setup === 'function') {
            effdf_map_after_setup(effdf_map);
        }
        
        

        //Setup infowindow
        infowindow = new google.maps.InfoWindow({content:"", maxWidth: 350});
/*
        effdf_map.addListener('center_changed', function() {
            if (effdf_hold_update) return;

            var center = effdf_map.getCenter();
            effdf_setlatlng(center.lat(), center.lng());
            effdf_runAjaxUpdate();
        });
*/
 
		effdf_setMarkerData(dealer_data.dealers);
		
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

                            effdf_setlatlng(lat, lng)
                            effdf_runAjaxUpdate();
                        } else {
                            
                        }
                    });	
                    
                    return false;
                }

                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();

                effdf_setlatlng(lat, lng);

                console.log(lat);
                console.log(lng);

                effdf_runAjaxUpdate();
                
                return false; 
                
            });
            
            jQuery(this).removeAttr('disabled');
/*
            jQuery(this).on('change', function() {
                if (jQuery(this).val()=='') {
                    effdf_setlatlng('','');
                    effdf_runAjaxUpdate();
                }
            });
            */

            jQuery(this).on('keyup', function() {
                if (jQuery(this).val()=='') {
                    effdf_setlatlng('', '');
                    effdf_runAjaxUpdate();
                }
            });
        });
    }

    jQuery('.effective-dealers-checklist-filter input[type=checkbox]').on('change', function(e) {
        if (jQuery(this).is(':checked')) {
            var val = jQuery(this).val();
            if (val=='')
            {
                var ul = jQuery(this).parents('ul');
                ul.find('input[type=checkbox]').each(function() {
                    if (jQuery(this).val() != val) {
                        jQuery(this).removeAttr('checked');
                    }
                });
            } else {
                jQuery(this).parents('ul').find('input[value=""]').removeAttr('checked');
            }
        } else {
            var ul = jQuery(this).parents('ul');

            if (jQuery(ul).find(':checked').length==0) {
                var empty_el = jQuery(ul).find('input[type=checkbox][value=""]');
                jQuery(empty_el).prop('checked', true);
            }
        }

        jQuery('.effective-dealers-checklist-filter li.effds-filter-active').removeClass('effds-filter-active');
        jQuery('.effective-dealers-checklist-filter input:checked').each(function() {
            jQuery(this).parents('li').addClass('effds-filter-active');
        });
    });


    jQuery('.effective-dealer-use-my-location-ip-filter').on('click', function(e) {
        e.preventDefault();
        jQuery.ajax({
            type: 'post',
            dataType: 'json',
            url : dealer_data.ajax_url,
            data: {
                'action': 'edealer_get_ip_location',
            },
            success: function(response)
            {
                effdf_setlatlng(response.lat, response.lng);
                jQuery('input.effdf_location_search').val(response.location);
                effdf_runAjaxUpdate();
            }
        });
        return false;
    });

    jQuery('.edf-ajax-enable .effective-grid-dropdown-filter').on('change', function() {
        effdf_runAjaxUpdate();
    });

    jQuery('.edf-ajax-enable .effective-dealers-checklist-filter input[type=checkbox]').on('change', function() {
        effdf_runAjaxUpdate();
    });

    jQuery('.edf-ajax-enable .effective-dealer-links-filter a').on('click', function(e) {
        e.preventDefault();

        val = jQuery(this).data('value');
        var filter = jQuery(this).parents('.effective-dealer-links-filter');
        var el = jQuery(filter).find('input[type=hidden]');
        el.val(val);

        jQuery(filter).find('.edealer-active-link').removeClass('edealer-active-link');
        jQuery(this).addClass('edealer-active-link');

        effdf_runAjaxUpdate();

    });
}


/**
 * Retrieves an updated list of markers and shop results
 * 
 * @param {*} lat 
 * @param {*} lng 
 */
function effdf_setlatlng(lat, lng)
{
    jQuery('input.ds-lat').val(lat);
    jQuery('input.ds-lng').val(lng);
}

function effdf_runAjaxUpdate() 
{
    effdf_hold_update=true;
    jQuery('.edf-loader').show();

		var data = {
			'action': 'edf_ajax_update',
            'dealer_finder_id': dealer_data.dealer_finder_id
        };
        
        //Assemble current filter data
        jQuery('.effective-dealers-filters *[name][type!=checkbox]').each(function() {
            if (jQuery(this).val() != '')
                data[jQuery(this).attr('name')] = jQuery(this).val();
        });

        jQuery('.effective-dealers-filters *[name][type=checkbox]:checked').each(function() {
            var name = jQuery(this).attr('name');
            
            if (name in data) {
                data[jQuery(this).attr('name')] += "|" + jQuery(this).val();
            } else {
                data[jQuery(this).attr('name')] = jQuery(this).val();
            }
            
        });

        console.log(data);
		
		jQuery('.effective-dealer-elements-container').html('<div class="dealer-spinner"></div>');

		jQuery.get(dealer_data.ajax_url, data, function(response) {
            var ul = jQuery('ul', response.elements);
			jQuery('.effective-dealer-elements-container').html(ul);
			effdf_setMarkerData(response.dealers);
            jQuery('.edf-loader').hide();
            effdf_hold_update=false;
		}, 'json'); 
} 

/*
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
*/

function effdf_setMarkerData(data) 
{
    var map = effdf_map;
	markerData = data;
	
	//Setup new marker data
	for(i=0; i<markers.length; i++){
		markers[i].setMap(null);
	}
    markers = [];
    if (typeof effdf_marker_clear === 'function') {
        mdata = effdf_marker_clear();
    }
	
	var bounds = new google.maps.LatLngBounds();
	
	markers = data.map(function(dealer, i) { 
        var mdata = { 
            position: dealer.location,
            map: map,
            element_id: dealer.id
        };
        //console.log('icon: ' + dealer.icon);
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

        if (typeof effdf_marker_before === 'function') {
            mdata = effdf_marker_before(map, dealer, mdata);
        }

        var marker = new google.maps.Marker(mdata);

        bounds.extend(dealer.location);

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                if (typeof effdf_marker_click === 'function') {
                    mdata = effdf_marker_click(marker, i);
                }
                infowindow.setContent(markerData[i].infoWindowHtml);
                infowindow.open(map, marker); 
            }
        })(marker,i));

        if (typeof effdf_marker_after === 'function') {
            effdf_marker_after(map, dealer, mdata, marker);
        }
	  
	  return marker;
	});
    //effdf_fit_to_bounds();	
	map.fitBounds(bounds, 50);

    if (typeof effdf_markers_after === 'function') {
        effdf_markers_after(map, markers);
    }
}

function effdf_fit_to_bounds()
{
    var bounds = new google.maps.LatLngBounds();
    for(i=0;i<markers.length;i++)
    {
        bounds.extend(markers[i].position);
    }
    effdf_map.fitBounds(bounds, 50);
}