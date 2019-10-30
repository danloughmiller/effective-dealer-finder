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
		//Filter
		return $this->post->post_title;
	}
	
	public function getLink() {
		//Filter
		return get_permalink($this->post->ID);
	}
	
	public function render()
	{
		$post_image_id = get_post_thumbnail_id($this->post->ID);
		$md = wp_get_attachment_metadata($post_image_id, true);
		$image_attributes = wp_get_attachment_image_src( $post_image_id , 'full');
        
        $ret = '';

		if (function_exists('fly_get_attachment_image_src') && $md) {
			
				//$flythumb = fly_get_attachment_image_src($post_image_id, array(225,200), array( 'center', 'top' ));
				$flythumb = fly_get_attachment_image_src($post_image_id, array(600,400), array('center', 'center'));
						
			/*if ($ratio>2) {
				//$flymedium = fly_get_attachment_image_src($post_image_id, array(800,600), false);
				$flymedium = fly_get_attachment_image_src($post_image_id, array(800,600), array( 'center', 'top' ));
			} else {
				//$flymedium = fly_get_attachment_image_src($post_image_id, array(800,600), true); 
				$flymedium = fly_get_attachment_image_src($post_image_id, array(800,600), false);
			}*/
			
			
			if ($flythumb) {
				$ret = '<div class="effective-dealer-post-image-container effective-post-image-container">';
				$ret .= $this->linkIt('<img src="' . $flythumb['src'] . '" />');
				$ret .='</div>';
			}
		}
		
        $ret .= parent::render();

		
		
		return $ret;
	}
	
}



	