<?php

// Register Custom Post Type
function effdl_add_post_type() {

	$labels = array(
		'name'                  => _x( 'Dealers', 'Post Type General Name', 'effdl' ),
		'singular_name'         => _x( 'Dealer', 'Post Type Singular Name', 'effdl' ),
		'menu_name'             => __( 'Dealers', 'effdl' ),
		'name_admin_bar'        => __( 'Dealer', 'effdl' ),
		'archives'              => __( 'Dealer Archives', 'effdl' ),
		'attributes'            => __( 'Dealer Attributes', 'effdl' ),
		'parent_item_colon'     => __( 'Parent Dealer:', 'effdl' ),
		'all_items'             => __( 'All Dealers', 'effdl' ),
		'add_new_item'          => __( 'Add New Dealer', 'effdl' ),
		'add_new'               => __( 'Add New', 'effdl' ),
		'new_item'              => __( 'New Dealer', 'effdl' ),
		'edit_item'             => __( 'Edit Dealer', 'effdl' ),
		'update_item'           => __( 'Update Dealer', 'effdl' ),
		'view_item'             => __( 'View Dealer', 'effdl' ),
		'view_items'            => __( 'View Dealers', 'effdl' ),
		'search_items'          => __( 'Search Dealers', 'effdl' ),
		'not_found'             => __( 'Not found', 'effdl' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'effdl' ),
		'featured_image'        => __( 'Featured Image', 'effdl' ),
		'set_featured_image'    => __( 'Set featured image', 'effdl' ),
		'remove_featured_image' => __( 'Remove featured image', 'effdl' ),
		'use_featured_image'    => __( 'Use as featured image', 'effdl' ),
		'insert_into_item'      => __( 'Insert into item', 'effdl' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'effdl' ),
		'items_list'            => __( 'Items list', 'effdl' ),
		'items_list_navigation' => __( 'Items list navigation', 'effdl' ),
		'filter_items_list'     => __( 'Filter items list', 'effdl' ),
	);

	$args = array(
		'label'                 => __( 'Dealer', 'effdl' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => false,
		'publicly_queryable'    => false,
    );
    
    $args = apply_filters('EFFDF_DEALER_POST_TYPE_ARGS', $args);
    $labels = apply_filters('EFFDF_DEALER_POST_TYPE_LABELS', $labels);
    
	register_post_type( 'dealer', $args );

}
add_action( 'init', 'effdl_add_post_type', 0 );
