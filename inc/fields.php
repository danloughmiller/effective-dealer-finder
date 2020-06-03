<?php

function effdf_add_dealer_metabox( $meta_boxes ) {
	$prefix = 'dealer_';

	$meta_boxes[] = array(
		'id' => 'effdf_dealer_metabox',
		'title' => esc_html__( 'Dealer Information', 'effdf' ),
		'post_types' => array('dealer'),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => 'false',
		'fields' => array(
            array(
                'id' => $prefix . 'location',
                'type'=>'text',
                'name'=>esc_html__('Location', 'effdf'),
                'class'=>'gautocomplete'
			),
			array('id'=>$prefix.'country',
				'type'=>'text',
				'name'=>esc_html__('Country', 'effdf'),
			),
			array(
				'id' => $prefix . 'phone',
				'type' => 'text',
				'name' => esc_html__( 'Phone', 'effdf' ),
			),
			array(
				'id' => $prefix . 'fax',
				'type' => 'text',
				'name' => esc_html__( 'Fax', 'effdf' ),
			),
			array(
				'id' => $prefix . 'email',
				'name' => esc_html__( 'Email', 'effdf' ),
				'type' => 'email',
            ),
            array(
				'id' => $prefix . 'website',
				'name' => esc_html__( 'Website', 'effdf' ),
				'type' => 'url',
			),
            array(
				'id' => $prefix . 'latitude',
				'type' => 'text',
				'name' => esc_html__( 'Latitude', 'effdf' ),
				'attributes' => array(
					'readonly' => 'readonly',
				),
            ),
            array(
				'id' => $prefix . 'longitude',
				'type' => 'text',
				'name' => esc_html__( 'Longitude', 'effdf' ),
				'attributes' => array(
					'readonly' => 'readonly',
				),
			),
		),
    );
    
    $meta_boxes = apply_filters('EFFDF_ADD_DEALER_METABOX', $meta_boxes, $prefix);
	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'effdf_add_dealer_metabox' );