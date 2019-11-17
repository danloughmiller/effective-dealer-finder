<?php

class EffectiveDealer_LocationFilter extends EffectiveDealer_Filter
{

    public $currentValue = '';
    public $currentLat = '';
    public $currentLng = '';

    public function __construct($id, $title, $placeholder='', $currentValue='', $currentLat='', $currentLng='')
    {
        parent::__construct($id, $title, $placeholder);

        $this->currentValue=$currentValue;
        $this->currentLat=$currentLat;
        $this->currentLng=$currentLng;


        $this->_renderTitle=false;
    }

    protected function renderElement()
    {
        $html = '<input class="effdf_location_search" name="edealer_filters[location][ds]" type="text" value="' . $this->currentValue . '" />';
        $html .= '<input class="ds-lat" name="edealer_filters[location][lat]" type="hidden" value="' . $this->currentLat . '" />';
        $html .= '<input class="ds-lng" name="edealer_filters[location][lng]" type="hidden" value="' . $this->currentLng . '" />';
        return $html;
    }

    protected function getClasses($additional = array())
	{
		return array_merge(
			parent::getClasses($additional), 
			array('effective-dealer-search-filter')
		);
    }
    
    function constructQuery(&$args, &$tax_query)
	{
        if (!empty($this->currentLat) && !empty($this->currentLng)) {
            $nearby_ids = $this->getDealersByLatLng(
                $args['post_type'],
                $this->currentLat,
                $this->currentLng
            );

            $args['post__in'] = $nearby_ids;
        }

        
    }
    
    protected function getDealersByLatLng($post_type, $lat, $lng, $maxDistance=100, $maxResults=3)
    {
        global $wpdb;
        $sql = "SELECT wp_posts.ID, pm1.meta_value as lat, pm2.meta_value as lng,
			3959 
			   * acos( cos( radians($lat) ) 
               * cos( radians(pm1.meta_value) ) 
               * cos( radians(pm2.meta_value) - radians($lng)) + sin(radians($lat)) 
               * sin( radians(pm1.meta_value))) as distance_miles,
			   
			6371 
			   * acos( cos( radians($lat) ) 
               * cos( radians(pm1.meta_value) ) 
               * cos( radians(pm2.meta_value) - radians($lng)) + sin(radians($lat)) 
               * sin( radians(pm1.meta_value))) as distance_km
	 
			FROM " . $wpdb->prefix. 'posts as wp_posts
			INNER JOIN ' . $wpdb->prefix . 'postmeta as pm1 ON pm1.post_id = wp_posts.ID AND pm1.meta_key=\'dealer_latitude\' 
			INNER JOIN ' . $wpdb->prefix . 'postmeta as pm2 ON pm2.post_id = wp_posts.ID AND pm2.meta_key=\'dealer_longitude\'
            WHERE post_type=\'' . $post_type . '\' AND post_status=\'publish\' GROUP BY wp_posts.ID
			ORDER BY distance_miles
            LIMIT ' . $maxResults ;
        
        $results = $wpdb->get_results($sql);
        
        $ids = array();
        foreach ($results as $r) {
            if (empty($maxDistance) || $r->distance_miles < $maxDistance)
                $ids[] = $r->ID;
        }
        
        return $ids;
    }
}