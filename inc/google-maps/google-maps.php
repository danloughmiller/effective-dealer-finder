<?php

function effdf_get_api_key()
{
    return apply_filters('EFFDF_GOOGLE_API_KEY', '');
}



add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script(
        'google-api-js',
        'https://maps.googleapis.com/maps/api/js?key=' . effdf_get_api_key() . '&libraries=geocoder,geometry,places&callback=init_effdf_admin',
        array('effdf-admin-js')
    );
});