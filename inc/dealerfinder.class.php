<?php


class EffectiveDealerFinder
{
    public $dealer_finder_id = '';

    public $filters = null;

    public $_renderFilters = true;
	public $_renderPages = true;
	public $_renderSinglePagePagination = false;
	
    public $elements = array();
    
    public $paged = true;
	public $itemsPerPage = 50;
	public $page = 1;

    function __construct($dealer_finder_id, $filters = array())
    {
        $this->dealer_finder_id = $dealer_finder_id;

        if (!empty($filters)) {
			$this->filters = $filters;
		} else {
			$this->filters = new EffectiveDealer_Filters();
		}
    }

    public function render()
	{
		$ret = '<div id="effect-dealers-'.$this->dealer_finder_id . '" class="' . implode(' ', $this->getClasses()) . '">';
		
		if ($this->_renderFilters && !empty($this->filters))
			$ret .= $this->filters->render();
		
		$ret .= '<div class="effective-dealer-elements-container">';
		$ret .= 	'<ul class="effective-dealer-elements">';
		
		$ret .= $this->renderElements();
		
		$ret .= 	'</ul>';
		$ret .= '</div>';

		if ($this->_renderPages && $this->paged && ($this->_renderSinglePagePagination || $this->getPageCount()>1)) {
            $ret .= $this->renderPagination();
        }
        
		$ret .= '</div>';
		
		return $ret;
	}
	
	function renderElements()
	{
		
		$ret = '';
		
		//Filter
		$elements = $this->getElements();
		
		if (is_array($elements) && count($elements)>0) {
			foreach ($elements as $element) {
				$ret .= $this->renderElement($element);
			}
		}
		
		return $ret;
	}
	
	function renderElement($el)
	{
        $ret = '<li id="' . $el->getId() . '" class="' . implode(" ",$el->getClasses()) . '">';
        $ret .= '<div class="effect-dealer-element-content">';
        $ret .= $el->render();
        $ret .= '</div>';
		$ret .= '</li>';
		return $ret;
	}
	
	function renderPagination()
	{
		
		$ret = '';
		$ret .= '<div class="effective-dealer-pagination effective-pagination">';
		$ret .= '	<ul>';
		
		
		$ret .= $this->renderPaginationElements();
		
		
		$ret .= '	</ul>';
		$ret .= '</div>';
		
		$ret .= '</div>';
		
		return $ret;
	}
	
	function renderPaginationElements()
	{
		$x = $this->page;
		$x = max($x-4,1);
		$y=min($x+8, $this->getPageCount());
				
		$ret = '';
		
        $ret .= $this->renderPaginationElement(1, '&laquo;', 'edealer-page-link-first');

		for ($i=$x;$i<=$y;$i++) {
            
			    $ret .= $this->renderPaginationElement($i);
		}
		$ret .= $this->renderPaginationElement($this->getPageCount(), '&raquo;', 'edealer-page-link-last');
		
		return $ret;
	}
	
	function renderPaginationElement($pindex, $label='', $class='')
	{
		$ret = '<li class="edealer-page-link-' . $pindex . ' ' . $class . ' ' . ($pindex==$this->page?'egrid-current-page ':'') . (abs($pindex-$this->page)<=1?'egrid-close-page':'') . '"><a href="' . $this->getPaginationLink($pindex) . '">' . (!empty($label)?$label:$pindex) . '</a></li>';
		return $ret;
	}
	
	function getPaginationLink($pindex) { 
		return '?edealer_page='.$pindex;
	}
	
	function getElements() {}
	function getElementCount() { }
	function getPageCount()	{ 
        return ceil($this->getElementCount() / $this->itemsPerPage);
    }
	function setPage($page) { $this->page = $page; }
	
	function getClasses($additional=array())
	{
		if (empty($additional)) 
			$additional = array();
		
		if (is_string($additional))
			$additional = array($additional);
		
		return array_merge(array('effective-dealer-finder'), $additional);
	}
    
}