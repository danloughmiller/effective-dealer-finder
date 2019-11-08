<?php

class EffectiveDealer_PostElement extends EffectiveDealer_Element
{
	public $post=null;	
	public $_image_size = 'square';
	
	function __construct($post)
	{
		if (is_object($post)) {
			parent::__construct($post->ID);
			$this->post=$post;
		} else {
			parent::__construct($post);
			$this->post = get_post($post);
		}
	}
		
	public function getClasses($additional=array())
	{
		$classes = array('effective-dealer-post-element');
		
				return array_merge(
			parent::getClasses($additional), 
			$classes
		);
	}
	
	public function getTitle() {
		return $this->post->post_title;
	}
	
	public function getLink() {
		return get_permalink($this->post->ID);
	}
	

    
    function getLatLng()
    {
        $latitude = get_post_meta($this->post->ID, 'dealer_latitude', true);
        $longitude = get_post_meta($this->post->ID, 'dealer_longitude', true);
        
        return array(
            'lat'=>floatval($latitude),
            'lng'=>floatval($longitude)
        );
    }
	
}



	