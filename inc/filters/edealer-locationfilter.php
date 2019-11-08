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
		//if (!empty($this->currentValue))
		//	$args['s'] = $this->currentValue;
	}
}