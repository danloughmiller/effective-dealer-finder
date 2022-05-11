<?php

abstract class EffectiveDealer_ChecklistFilter extends EffectiveDealer_Filter
{
    public $options = array();

	/** @var string[] $selected */
	public $selected = array();
	
	public $_renderSelect2 = false;
	
	public function __construct($id, $title, $placeholder='', $options=array(), $selected)
	{
		parent::__construct($id, $title, $placeholder);
		
		$this->options = $options;
		$this->selected = explode('|', $selected);
	}
	
	protected function getFieldName() { }
	protected function renderElement()
	{
		$ret = '';
		$ret .= '<ul>';
		
		if (!empty($this->placeholder))
			$ret .= $this->renderOption("", $this->placeholder);
		
		
		if (is_array($this->options)) {
			foreach ($this->options as $key=>$value) {
                $data = @$value['data'];
                $label = $value['label'];

				$ret .= $this->renderOption($key, $label, $data);
			}
		}
		
		$ret .= '</ul>';
		
		return $ret;
	}
	
	protected function renderOption($key, $label, $data=false)
	{
		return '<li class="' . (in_array($key, $this->selected)?'effds-filter-active':'') . '"><label><span>' . $label . '</span><input name="' . $this->getFieldName() .'" type="checkbox" ' . (in_array($key, $this->selected)?'checked':'') . ' value="'.$key.'"></label></li>';
	}
	
	protected function getClasses($additional = array())
	{
		return array_merge(
			parent::getClasses($additional), 
			array('effective-dealers-checklist-filter')
		);
	}
	
	public function addOption($key, $value, $data=false)
	{
        $this->options[$key] = array('label'=>$value, 'data'=>$data);
        $this->addChildren($key, $value, $data);
    }
    
}