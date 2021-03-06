<?php

class EffectiveDealer_DefaultPostElement extends EffectiveDealer_PostElement
{
    function __construct($post)
	{
        parent::__construct($post);
    }

    public function renderInfoWindow($wrap='<div class="effdf-infowindow">%s</div>')
    {
        if (!empty($this->getLink())) {
            $html = sprintf('<a href="%s"><strong>%s</strong></a>', $this->getLink(), $this->getTitle());
        } else {
            $html = '<strong>'.$this->getTitle().'</strong>';
        }
        //$html = sprintf('<a href="%s">%s</a>', $this->getLink(), $this->getTitle());
        
        $html = $this->addIWElement($html, 'dealer_location');
        $html = $this->addIWElement($html, 'dealer_phone', 'Phone: ');
        $html = $this->addIWElement($html, 'dealer_fax', 'Fax: ');
        $html = $this->addIWElement($html, 'dealer_email', 'Email: ');
        
        $website = get_post_meta($this->post->ID, 'dealer_website',true);
        if (!empty($website)) {
            $html.= '<a href="' . $website . '" target=_blank>' . $website . '</a>';
        }
        

        $ret = sprintf($wrap, $html);
        return $ret;
    }
    
}