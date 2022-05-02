<?php

class EDLink
{
    public $href;
    public $label;

    public $is_selected=false;

    function __construct($href, $label, $term, $is_selected=false)
    {
        $this->href = $href;
        $this->label = $label;
        $this->term = $term;
        $this->is_selected = $is_selected;
    }
}

class EffectiveDealer_LinksFilter extends EffectiveDealer_Filter
{
    var $links = array();
    var $selected = '';

    function __construct($id, $title, $selected)
    {
        parent::__construct($id, $title);
        $this->selected = $selected;
    }

    function addLink($href, $label, $term, $is_selected=false)
    {
        $this->links[] = new EDLink($href, $label, $term, $is_selected);
    }

    function getClasses($additional = array())
    {
        $classes = parent::getClasses($additional);
        $classes[] = 'effective-dealer-links-filter';

        return $classes;
    }

    protected function getFieldName() { }
	protected function renderElement()
	{
		$ret = '';
        $ret .= '<input type=hidden name="' . $this->getFieldName() . '" value="' . $this->selected . '" />';
		$ret .= '<ul data-name="' . $this->getFieldName() . '">';
		

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
		return '<a ' . ($link->is_selected?'class="edealer-active-link"':'') . ' href="' . $link->href . '" data-value="' . $link->term . '">' . $link->label . '</a>';
	}

}