<?php

class EffectiveDealer_DropdownFilter extends EffectiveDealer_Filter
{
    public $options = array();
	public $selected = '';
	
	public $_renderSelect2 = false;
	
	public function __construct($id, $title, $placeholder='', $options=array(), $selected='')
	{
		parent::__construct($id, $title, $placeholder);
		$this->options = $options;
		$this->selected = $selected;
	}
	
	protected function getSelectName() { }
	protected function renderElement()
	{
		$ret = '';
		$ret .= '<select class="' . ($this->_renderSelect2?'egrid-select2':'') . '" data-minimum-results-for-search="Infinity" name="' . $this->getSelectName() . '">';
		
		if (!empty($this->placeholder))
			$ret .= $this->renderOption("", $this->placeholder);
		
		
		if (is_array($this->options)) {
			foreach ($this->options as $key=>$value) {
                $data = @$value['data'];
                $label = $value['label'];

				$ret .= $this->renderOption($key, $label, $data);
			}
		}
		
		$ret .= '</select>';
		
		return $ret;
	}
	
	protected function renderOption($key, $label, $data=false)
	{
		return '<option ' . ($this->selected===true||$this->selected==$key?'SELECTED':'') . ' value="'.$key.'">' . $label . '</option>';
	}
	
	protected function getClasses($additional = array())
	{
		return array_merge(
			parent::getClasses($additional), 
			array('effective-grid-dropdown-filter')
		);
	}
	
	public function addOption($key, $value, $data=false)
	{
        $this->options[$key] = array('label'=>$value, 'data'=>$data);
        $this->addChildren($key, $value, $data);
    }
    
    public function addChildren($key, $value, $data=false)
    {

    }
}