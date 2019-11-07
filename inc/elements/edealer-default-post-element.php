<?php

class EffectiveDealer_DefaultPostElement extends EffectiveDealer_PostElement
{
    function __construct($post)
	{
        parent::__construct($post);
    }
    
    public function render()
	{
        return 'DEFAULT POST ELEMENT';
    }

    public function renderInfoWindow()
    {
        return 'DEFAULT INFOWINDOW';
    }
}