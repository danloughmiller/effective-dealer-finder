function init_effdf_admin()
{
 
    
    //Initialize autocomplete on admin facing location fields
    var input = document.getElementById('dealer_location');

    if (input) {
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.setFields(['formatted_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);

        autocomplete.addListener('place_changed', function() {
            var place = this.getPlace();
            
            var formatted_address = place.formatted_address;
            $('#dealer_location').val(formatted_address);

            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            
            $('#dealer_latitude').val(lat);
            $('#dealer_longitude').val(lng);

            var phone = place.formatted_phone_number;
            if (phone) {
                $('#dealer_phone').val(phone);
            }

            var website = place.website;
            if (website) {
                $('#dealer_website').val(website);
            }
        });
    }

}