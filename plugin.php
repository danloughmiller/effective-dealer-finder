<?php
/*
Plugin Name: Effective Dealer Finder
Plugin URI: 
Description: 
Version: 1.0
Author: Daniel Loughmiller / EffectWebAgency
Author URI: 
Text Domain: 
Domain Path: 
*/

require_once('lib/meta-box-master/meta-box.php');

require_once('inc/post_types.php');
require_once('inc/fields.php');
require_once('inc/google-maps/google-maps.php');


require_once('inc/dealerfinder.class.php');
require_once('inc/dealerfilter.class.php');

require_once('inc/filters/dealersearch.filter.class.php');

// Finders
require_once('inc/finders/effectivedealerpostfinder.php');

// Elements
require_once('inc/elements/edealer-elements.php');
require_once('inc/elements/edealer-post-element.php');

/* HOOKS/FILTERS */
//EFFDF_GOOGLE_API_KEY - Filter to provide api key for google maps/places



add_action('admin_enqueue_scripts', function() {
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