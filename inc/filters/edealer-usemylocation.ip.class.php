<?php

class EffectiveDealer_UseMyLocation_IP_Filter extends EffectiveDealer_Filter
{
    public function __construct($id, $title, $placeholder='')
    {
        parent::__construct($id, $title, $placeholder);
        $this->_renderTitle=false;
    }

    protected function renderElement()
    {
        $html = '<button class="' . implode(' ', $this->getClasses()) . '">' . $this->title . '</button>';
        return $html;
    }

    protected function getClasses($additional = array())
	{
		return array_merge(
			parent::getClasses($additional), 
			array('effective-dealer-use-my-location-filter','effective-dealer-use-my-location-ip-filter')
		);
    }
    
    function constructQuery(&$args, &$tax_query, &$meta_query)
	{
		//if (!empty($this->currentValue))
		//	$args['s'] = $this->currentValue;
	}
}

add_action("wp_ajax_edealer_get_ip_location", "ajax_edealer_get_ip_location");
add_action("wp_ajax_nopriv_edealer_get_ip_location", "ajax_edealer_get_ip_location");

function ajax_edealer_get_ip_location() {
    $api_key = apply_filters('EFFDF_IPSTACK_API_KEY', '');
    $url = 'http://api.ipstack.com/' . $_SERVER['REMOTE_ADDR'] . '?access_key=' . $api_key;
    $contents = wp_remote_get($url);

    if (!empty($contents['body']))
    {
        $body = json_decode($contents['body']);
        $latitude = $body->latitude;
        $longitude = $body->longitude;
        $location = $body->city . ', ' . $body->region_name;

        echo json_encode(array(
            'lat'=>$latitude,
            'lng'=>$longitude,
            'location'=>$location
        ));
    }

    

    exit;
}