function init_effdf_admin()
{
 
    
    //Initialize autocomplete on admin facing location fields
    var input = document.getElementById('dealer_location');

    if (input) {
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.setFields(['formatted_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);

        autocomplete.addListener('place_changed', function() {
            var place = this.getPlace();

            console.log(place);
            
            var formatted_address = place.formatted_address;
            jQuery('#dealer_location').val(formatted_address);

            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            
            jQuery('#dealer_latitude').val(lat);
            jQuery('#dealer_longitude').val(lng);

            var phone = place.formatted_phone_number;
            if (phone) {
                jQuery('#dealer_phone').val(phone);
            }

            var website = place.website;
            if (website) {
                jQuery('#dealer_website').val(website);
            }
        });
    }

}