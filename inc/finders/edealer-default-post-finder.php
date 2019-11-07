<?php
/*
 * This implemented a default dealer view that utilizes the default
 * fields setup by this plugin
 */

class EffectiveDealer_DefaultPostFinder extends EffectiveDealer_PostFinder
{
    public function __construct($grid_id, $post_type='dealer', $taxonomies = array(), $additional_query_args=array(), $createElementCallback = false)
	{
        if (empty($createElementCallback))
            $createElementCallback = array($this, 'createDefaultDealerElement');

        parent::__construct($grid_id, $post_type, $taxonomies , $additional_query_args, $createElementCallback);
    }
    
    protected function createDefaultDealerElement($post)
    {
        return new EffectiveDealer_DefaultPostElement($post);
    }
}