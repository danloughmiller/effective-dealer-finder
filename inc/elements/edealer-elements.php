<?php


class EffectiveDealer_Element
{
	public $id = false;
	
	public $_renderTitle = true;
	public $_renderEmptyTitle = false;
	
	function __construct($id)
	{
		$this->id = $id;
	}
	
	public function getId() { return 'effective-dealer-element-'.$this->id; }
	public function getClasses($additional=array())
	{
		if (empty($additional)) 
			$additional = array();
		
		if (is_string($additional))
			$additional = array($additional);
		
		return array_merge(array('effective-dealer-element'), $additional);
	}
	public function getTitle() { }
	public function getLink() { }
	public function linkIt($html, $attrs='') {
		$link = $this->getLink();
		if (!empty($link)) {
			$ret = '<a ' . $attrs . ' href="' . $link . '">' . $html . '</a>';
			return $ret;
		}
		
		return $html;
	}
	
	public function render()
	{
        $ret = 'TBD';
		//if ($this->_renderTitle && ($this->_renderEmptyTitle || !empty($this->getTitle())))
		//	$ret .= '<span class="effective-dealer-title">' . $this->linkIt($this->getTitle()) .'</span>';
		
		
		return $ret;
    }

    public function renderInfoWindow()
    {
        return 'TBD';
    }

    function getMarker()
    {
        return array(
            'id'=>$this->getId(),
            'title'=>$this->getTitle(),
            'link'=>$this->getLink(),
            'location'=>$this->getLatLng(),
            'infoWindowHtml'=>$this->renderInfoWindow()
        );
    }
    
    function getLatLng()
    {
        return array(
            'lat'=>0,
            'lng'=>0
        );
    }
	
}