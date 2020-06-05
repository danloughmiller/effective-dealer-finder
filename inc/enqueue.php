<?php

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_script(
        'google-api-js',
        'https://maps.googleapis.com/maps/api/js?key=' . effdf_get_api_key() . '&libraries=geocoder,geometry,places&callback=init_effdf_admin',
        array('effdf-admin-js'),
        null, true
    );

    wp_enqueue_script(
        'effdf-lib-js',
        plugins_url() .'/effective-dealer-finder/assets/js/effdf.lib.js'        
    );

    wp_enqueue_script(
        'effdf-admin-js',
        plugins_url() .'/effective-dealer-finder/assets/js/effdf-admin.js',
        array('effdf-lib-js')   
    );
});


//add_filter('script_loader_tag', 'effdf_add_async_attribute', 10, 2);
function effdf_add_async_attribute($tag, $handle)
{
	if ( 'google-api-js' !== $handle )
		return $tag; 
	
	return str_replace( ' src', ' async="async" src', $tag );
}
