function init_effdf_admin()
{
 
    
    //Initialize autocomplete on admin facing location fields
    var input = document.getElementById('dealer_location');

    if (input) {
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.setFields(['address_components', 'formatted_address', 'adr_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);

        autocomplete.addListener('place_changed', function() {
            var place = this.getPlace();

            var add = placeToAddress(place);
            jQuery('#dealer_country').val(add.Country.long_name);
            console.log(add.Country);
            
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


function placeToAddress(place){
    var address = {};
    place.address_components.forEach(function(c) {
        switch(c.types[0]){
            case 'street_number':
                address.StreetNumber = c;
                break;
            case 'route':
                address.StreetName = c;
                break;
            case 'neighborhood': case 'locality':    // North Hollywood or Los Angeles?
                address.City = c;
                break;
            case 'administrative_area_level_1':     //  Note some countries don't have states
                address.State = c;
                break;
            case 'postal_code':
                address.Zip = c;
                break;
            case 'country':
                address.Country = c;
                break;
            /*
            *   . . . 
            */
        }
    });

    return address;
}