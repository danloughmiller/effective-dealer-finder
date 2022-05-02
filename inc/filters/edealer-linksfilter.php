<?php

class EDLink
{
    public $href;
    public $label;

    public $is_selected=false;

    function __construct($href, $label, $is_selected=false)
    {
        $this->href = $href;
        $this->label = $label;
        $this->is_selected = $is_selected;
    }
}

class EffectiveDealer_LinksFilter extends EffectiveDealer_Filter
{
    var $links = array();

    function __construct($id, $title)
    {
        parent::__construct($id, $title, '');
    }

    function addLink($href, $label, $is_selected=false)
    {
        $this->links[] = new EDLink($href, $label, $is_selected);
    }

    protected function getFieldName() { }
	protected function renderElement()
	{
		$ret = '';
		$ret .= '<ul class="edealer-links-filter" data-name="' . $this->getFieldName() . '">';
		

		if (is_array($this->links)) {
			foreach ($this->links as $link) {
				$ret .= '<li>' . $this->renderLink($link) . '</li>';
			}
		}
		
		$ret .= '</ul>';
		
		return $ret;
	}

    protected function renderLink(EDLink $link)
	{
		return '<a ' . ($link->is_selected?'class="edealer-active-link"':'') . ' href="' . $link->href . '">' . $link->label . '</a>';
	}

}