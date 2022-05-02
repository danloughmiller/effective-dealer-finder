<?php
DEFINE('EDEALER_FILTER_PREFIX', 'egrid-');

class EffectiveDealer_Filters
{
    public $filters = array();
    public $labels = array(
        'reset_filters' => 'Reset Filters',
        'update_filters' => 'Update Filters',
    );

    function __construct($labels = array())
    {
        if (!empty($labels))
            $this->labels = array_merge($this->labels, $labels);
    }

    public function setLabel($labelKey, $value){
        if ($value===false) {
            unset($this->labels[$labelKey]);
        } else {
            $this->labels[$labelKey]=$value;
        }
    }
    
    protected function _($labelKey, $default=false)
    {
        if (array_key_exists($labelKey, $this->labels )) {
            $val = apply_filters('EFFECTIVE_DEALERS_LABEL_FILTER', $this->labels[$labelKey], $labelKey, $this);
            $val =  apply_filters('EFFECTIVE_DEALERS_FILTER_LABEL_FILTER', $val, $labelKey, $this);
            return $val;
        }

        return ($default===false?$labelKey:$default);
    }

    public function getFilters()
	{
		$filters = apply_filters(EDEALER_FILTER_PREFIX.'filters', $this->filters);
		return $filters;
	}
	
	public function addFilter($filter)
	{
		$this->filters[] = $filter;
		return $this;
    }

    
    
    public function render()
	{
        if (empty($this->getFilters()))
            return;
        
		$ret = '<div class="effective-dealers-filters"><form method=get>';
		
		$y=0;
		foreach ($this->getFilters() as $f) {
			$ret .= $f->render(++$y);
		}
		$ret .= '<div class="effective-dealers-filter-buttons">';
		if (!empty(self::_('update_filters')))
			$ret .= '	<button class="edealers-button edealers-button-update-filter" type="submit">' . self::_('update_filters') . '</button>';

		if (!empty(self::_('reset_filters')))
			$ret .= '	<a class="edealers-button edealers-button-reset-filter" href="' . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) . '">' . self::_('reset_filters') . '</a>';
		
			$ret .= '</div>';
		$ret .= '</form></div>';
		
		return $ret;
	}
}



abstract class EffectiveDealer_Filter
{
	public $id = '';
	public $title = "";
	public $placeholder = "";
	
	public $_renderTitle = true;
	public $_renderEmptyTitle = false;
	
	public function __construct($id, $title, $placeholder='')
	{
		$this->id = $id;
		$this->title = $title;
		$this->placeholder = $placeholder;
	}
		
	function render($index=false)
	{
		
		$classes = $this->getClasses(!empty($index)?'effective-dealers-filter-index-'.$index:false);
		$ret = '<div id="effective-dealers-filter-' . $this->id . '" class="' . implode(" ", $classes) . '">';
		if ($this->_renderTitle && ($this->_renderEmptyTitle || !empty($this->title)))
			$ret .= '<span class="effective-dealers-title">' . $this->title .'</span>';
		
		$ret .= $this->renderElement();
		
		$ret .= '</div>';
		
		return $ret;
	}
	
	protected function renderElement()
	{
		
	}

	function getFilterArgs()
	{
		if (!empty($args = $_GET['edealer_filter']))
			return $args;

		return array();
	}
	
	protected function getClasses($additional = array())
	{
		if (empty($additional)) 
			$additional = array();
		
		if (is_string($additional))
			$additional = array($additional);
		
		return array_merge(array('effective-dealers-filter'), $additional);
	}
	
	public function constructQuery(&$args, &$tax_query, &$meta_query)
	{

	}
	
}

