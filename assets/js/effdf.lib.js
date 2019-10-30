function effdf_initialize_autocomplete(input)
{
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.setFields(['formatted_address', 'geometry', 'icon', 'name', 'formatted_phone_number', 'website']);
    
    return autocomplete;
}