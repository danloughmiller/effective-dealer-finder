<?php

class EffeciveDealer_RadiusFilter extends EffectiveDealer_DropdownFilter {

	
	public function __construct($id, $title, $placeholder='', $options=array(), $selected='')
	{
        if (empty($options)) {
            $options = array(
                '10'=>'10 miles',
                '25'=>'25 miles',
                '50'=>'50 miles',
                '100'=>'100 miles',
                '250'=>'250 miles',
                '99999'=>'Any distance'
            );
        }

		parent::__construct($id, $title, $placeholder, $options, $selected);
    }
    
	
	function getSelectName()
	{
		return 'edealer_filter[radius]';
	}
	
	protected function get_classes($additional=array())
	{
		return array_merge(
			parent::get_classes($additional), 
			array('effective-dealer-rqadius-filter')
		);
	}
    
    //This will be implemented by the location filter or similar
	function constructQuery(&$args, &$tax_query)
	{
		
    }
    
}