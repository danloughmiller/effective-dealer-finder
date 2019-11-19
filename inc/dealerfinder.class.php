<?php


class EffectiveDealerFinder
{
    public $dealer_finder_id = '';

    public $filters = null;

    public $_renderMap = true;
    public $_renderFilters = true;
    public $_renderPages = false;
	public $_renderSinglePagePagination = false;
    
    /* Map */
    public $mapSize = array('100%', '500px');

    /* Elements */
    public $elements = array();
    
    /* Paging */
    public $paged = true;
	public $itemsPerPage = 9999;
	public $page = 1;

    function __construct($dealer_finder_id, $filters = array(), $mapSize = array('100%', '500px'))
    {
        $this->dealer_finder_id = $dealer_finder_id;

        if (!empty($filters)) {
			$this->filters = $filters;
		} else {
			$this->filters = new EffectiveDealer_Filters();
        }
        
        $this->mapSize = $mapSize;
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'google-api-js',
            'https://maps.googleapis.com/maps/api/js?key=' . effdf_get_api_key() . '&libraries=geocoder,geometry,places&callback=init_effdf_public',
            array('effdf-js'), '1.1', true
        );
    
        wp_enqueue_script(
            'effdf-lib-js',
            plugins_url() .'/effective-dealer-finder/assets/js/effdf.lib.js' ,
            array('jquery'), '1.1', true
        );

        $map_data = $this->getMapData();

        wp_enqueue_script(
            'effdf-js',
            plugins_url() .'/effective-dealer-finder/assets/js/effdf.js',
            array('effdf-lib-js'), '1.1.5', true
        );

        wp_localize_script( 'effdf-js', 'dealer_data', $map_data ); 
    
        wp_enqueue_script('jquery');
        
        wp_enqueue_style('effdf-css', plugins_url() .'/effective-dealer-finder/assets/css/effdf.css', array(), '1.2.8');
    }

    function getMapData()
    {
        $map_data = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'dealers' => $this->getElementMarkers()
        );

        return $map_data;
    }

    public function render()
	{
        $this->enqueue();

		$ret = '<div id="effect-dealers-'.$this->dealer_finder_id . '" class="' . implode(' ', $this->getClasses()) . '">';
		
		if ($this->_renderFilters && !empty($this->filters))
            $ret .= $this->renderFilters();
        
        $ret .= $this->renderMap();
		$ret .= $this->renderElements();	

        if ($this->_renderPages && 
            $this->paged && 
            (   
                $this->_renderSinglePagePagination ||
                $this->getPageCount()>1
            )
        ) {
                
                $ret .= $this->renderPagination();
        }
        
        $ret .= '</div>';
        		
		return $ret;
    }

    public function renderFilters()
    {
        return $this->filters->render();
    }
    
    public function renderMap()
    {
        $s = '<div id="effdf-map-container-' . $this->dealer_finder_id . '" class="effdf-map-container" style="width:' . $this->mapSize[0] . ';height:' . $this->mapSize[1] . ';"></div>';
        return $s;
    }
	
	function renderElements()
	{
		$ret  = '<div class="effective-dealer-elements-container">';
		$ret .= 	'<ul class="effective-dealer-elements">';
		
		//TODO: Filter
		$elements = $this->getElements();
		
		if (is_array($elements) && count($elements)>0) {
			foreach ($elements as $element) {
				$ret .= $this->renderElement($element);
			}
        }
        
        $ret .= 	'</ul>';
		$ret .= '</div>';
		
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
	
	function getElements($applyFilters=true) {}
    function getElementCount() { }
    function getElementMarkers() {
        $markers = array();

        foreach ($this->getElements() as $el) {
            $markers[] = $el->getMarker();
        }

        return $markers;
    }
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