<?php


class EffectiveDealer_Element
{
	public $id = false;
	
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
	public function getTitle() { return ''; }
	public function getLink() { return ''; }
	public function linkIt($html, $attrs='') {
		$link = $this->getLink();
		if (!empty($link)) {
			$ret = '<a ' . $attrs . ' href="' . $link . '">' . $html . '</a>';
			return $ret;
		}
		
		return $html;
	}
	
	public function render($wrap='%s')
	{
        $ret = sprintf('<a href="%s">%s</a>', $this->getLink(), $this->getTitle());
        $ret = sprintf($wrap, $ret);

		return $ret;
    }

    public function renderInfoWindow($wrap='<div class="effdf-infowindow">%s</div>')
    {
        $title = sprintf('<a href="%s">%s</a>', $this->getLink(), $this->getTitle());
        $ret = sprintf($wrap, $ret);

        return $ret;
    }

    protected function addIWElement($html, $field, $prefix='', $suffix='')
    {
        $value = get_post_meta($this->post->ID, $field, true);
        if (!empty($value))
            $html .= '<p class="' . $field . '">' . $prefix . $value . $suffix .'</p>';

        return $html;
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