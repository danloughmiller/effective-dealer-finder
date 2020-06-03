<?php

class EffeciveDealer_MetaValueDropdownFilter extends EffectiveDealer_DropdownFilter {

    public $meta_key = '';
	public $_suppliedoptions = array();

	public function __construct($id, $title, $placeholder='', $meta_key=false, $selected='', $options=array())
	{
		parent::__construct($id, $title, $placeholder, array(), $selected);

		$this->meta_key = $meta_key;
		$this->_suppliedoptions = $options;

		if (!empty($this->_suppliedoptions)) {
			foreach ($this->_suppliedoptions as $key=>$option) {
				$this->addOption($key, $option);
			}
		}

    }

    function getDistinctValues($meta_key)
    {
        global $wpdb;
        $sql = 'select distinct(meta_value) FROM ' . $wpdb->prefix.'postmeta WHERE meta_key=%s ORDER BY meta_value';
        $sql = $wpdb->prepare($sql);

        return $wpdb->get_col($sql);
    }
    
    public function addChildren($key, $value, $data=false)
    {

    }
	
	function getSelectName()
	{
		return 'edealer_filter[' . $this->meta_key . ']';
	}
	
	protected function get_classes($additional=array())
	{
		return array_merge(
			parent::get_classes($additional), 
			array('effective-dealer-meta-valu-filter', 'effective-dealer-meta-value-dropdown-filter-'.$this->taxonomy)
		);
	}
	
	function constructQuery(&$args, &$tax_query, &$meta_query)
	{
		if (!empty($this->selected)) {
			$meta_query[] = array(
				'key'=>$this->meta_key,
				'value'=>$this->selected
			);
		}
    }
    
}