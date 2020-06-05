function init_effdf_admin()
{
 
    console.log('Initializing EFFDF');
    //Initialize autocomplete on admin facing location fields
    var input = document.getElementById('dealer_location');
    setupLocationField(input);

}

function setupLocationField(input)
{
    if (input) {
        console.log('Initializing Autocomplete');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.setFields(['place_id', 'address_components', 'formatted_address', 'adr_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);

        autocomplete.addListener('place_changed', function() {
            var place = this.getPlace();
            console.log(place);
            var add = placeToAddress(place);
            jQuery('#dealer_address').val(add.StreetNumber.long_name + ' ' + add.StreetName.long_name);
            jQuery('#dealer_address2').val('');
            jQuery('#dealer_city').val(add.City.long_name);
            jQuery('#dealer_state').val(add.State.long_name);
            jQuery('#dealer_postal_code').val(add.Zip.long_name);
            jQuery('#dealer_country').val(add.Country.long_name);
            jQuery('#dealer_place_id').val(place.place_id);
            console.log(add);
            
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
    } else {
        console.log('No autocomplete element');
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