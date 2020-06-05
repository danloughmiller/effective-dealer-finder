<?php

function effdf_add_dealer_metabox( $meta_boxes ) {
	$prefix = 'dealer_';


	$fields = array();

	$fields[] = array(
		'id' => $prefix . 'place_id',
		'type'=>'hidden',
		'name'=>esc_html__('Place ID', 'effdf'),
		'attributes' => array(
			'readonly' => 'readonly',
		),
	);

	$fields[] = array(
		'id' => $prefix . 'location',
		'type'=>'text',
		'name'=>esc_html__('Location', 'effdf'),
		'class'=>'gautocomplete'
	);

	$additional_fields = apply_filters('EFFDF_ENABLE_EXTENDED_ADDRESS_FIELDS', array());

	if ($additional_fields===true || (is_array($additional_fields) && in_array('address', $additional_fields))) {
		$fields[] = array('id'=>$prefix.'address',
			'type'=>'text',
			'name'=>esc_html__('Address', 'effdf'),
		);

		$fields[] = array('id'=>$prefix.'address2',
				'type'=>'text',
				'name'=>esc_html__('Address (Line 2)', 'effdf'),
		);
	}

	if ($additional_fields===true || (is_array($additional_fields) && in_array('address', $additional_fields) || in_array('city', $additional_fields))) {
		$fields[] = array('id'=>$prefix.'city',
				'type'=>'text',
				'name'=>esc_html__('City', 'effdf'),
		);
	}

	if ($additional_fields===true || (is_array($additional_fields) && in_array('address', $additional_fields) || in_array('state', $additional_fields))) {
		$fields[] = array('id'=>$prefix.'state',
				'type'=>'text',
				'name'=>esc_html__('State', 'effdf'),
		);
	}
	if ($additional_fields===true || (is_array($additional_fields) && in_array('address', $additional_fields) || in_array('postal_code', $additional_fields))) {
		$fields[] = array('id'=>$prefix.'postal_code',
				'type'=>'text',
				'name'=>esc_html__('Postal Code', 'effdf'),
		);
	}

	if ($additional_fields===true || (is_array($additional_fields) && in_array('address', $additional_fields) || in_array('country', $additional_fields))) {
		$fields[] = array('id'=>$prefix.'country',
			'type'=>'text',
			'name'=>esc_html__('Country', 'effdf'),
		);
	}
		
	$fields[] = array(
		'id' => $prefix . 'phone',
		'type' => 'text',
		'name' => esc_html__( 'Phone', 'effdf' ),
	);
	
	$fields[] = array(
		'id' => $prefix . 'fax',
		'type' => 'text',
		'name' => esc_html__( 'Fax', 'effdf' ),
	);
	
	$fields[] = array(
		'id' => $prefix . 'email',
		'name' => esc_html__( 'Email', 'effdf' ),
		'type' => 'email',
	);

	$fields[] = array(
		'id' => $prefix . 'website',
		'name' => esc_html__( 'Website', 'effdf' ),
		'type' => 'url',
	);

	$fields[] = array(
		'id' => $prefix . 'latitude',
		'type' => 'text',
		'name' => esc_html__( 'Latitude', 'effdf' ),
		'attributes' => array(
			'readonly' => 'readonly',
		)
	);

	$fields[] = array(
		'id' => $prefix . 'longitude',
		'type' => 'text',
		'name' => esc_html__( 'Longitude', 'effdf' ),
		'attributes' => array(
			'readonly' => 'readonly',
		),
	);

	$fields = apply_filters('EFFDF_ADD_DEALER_METABOX_FIELDS', $fields);


	$meta_boxes[] = array(
		'id' => 'effdf_dealer_metabox',
		'title' => esc_html__( 'Dealer Information', 'effdf' ),
		'post_types' => array('dealer'),
		'context' => 'advanced',
		'priority' => 'default',
		'autosave' => 'false',
		'fields' => $fields
    );
    
    $meta_boxes = apply_filters('EFFDF_ADD_DEALER_METABOX', $meta_boxes, $prefix);
	return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'effdf_add_dealer_metabox' );