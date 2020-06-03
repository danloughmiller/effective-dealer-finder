<?php

class EffectiveDealer_Search_Filter extends EffectiveDealer_Filter
{
    public function __construct($id, $title, $placeholder='')
    {
        parent::__construct($id, $title, $placeholder);

        $this->_renderTitle=false;
    }

    protected function renderElement()
    {
        $html = '<input name="ds" type="text" class="" />';
        return $html;
    }

    protected function getClasses($additional = array())
	{
		return array_merge(
			parent::getClasses($additional), 
			array('effective-dealer-search-filter')
		);
    }
    
    function constructQuery(&$args, &$tax_query, &$meta_query)
	{
		//if (!empty($this->currentValue))
		//	$args['s'] = $this->currentValue;
	}
}