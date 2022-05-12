<?php
/*
Plugin Name: Effective Dealer Finder
Plugin URI: 
Description: 
Version: 2.0.0
Author: Daniel Loughmiller / EffectWebAgency
Author URI: 
Text Domain: 
Domain Path: 
*/

require_once('lib/meta-box-master/meta-box.php');

require_once('inc/post_types.php');
require_once('inc/fields.php');
require_once('inc/google-maps/google-maps.php');

require_once('inc/enqueue.php');

// Finders
require_once('inc/dealerfinder.class.php');
require_once('inc/finders/edealer-post-finder.php');
require_once('inc/finders/edealer-default-post-finder.php');

/* Filters */
require_once('inc/dealerfilter.class.php');
require_once('inc/filters/edealer-locationfilter.php');
require_once('inc/filters/edealer-dropdownfilter.php');
require_once('inc/filters/edealer-termsfilter.php');
require_once('inc/filters/edealer-radiusfilter.php');
require_once('inc/filters/edealer-meta-value-dropdown.filter.php');
require_once('inc/filters/edealer-usemylocation.ip.class.php');
require_once('inc/filters/edealer-linksfilter.php');
require_once('inc/filters/edealer-termlinksfilter.php');
require_once('inc/filters/edealer-checklistfilter.php');
require_once('inc/filters/edealer-termcheckboxfilter.php');

// Elements
require_once('inc/elements/edealer-elements.php');
require_once('inc/elements/edealer-post-element.php');
require_once('inc/elements/edealer-default-post-element.php');

require_once('inc/admin.php');


