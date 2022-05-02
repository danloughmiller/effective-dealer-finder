<?php

class EffeciveDealer_TermLinksFilter extends EffectiveDealer_LinksFilter {

    public $taxonomy = '';
	
	public function __construct($id, $title, $taxonomy=false, $selected='', $placeholder=false)
	{
		parent::__construct($id, $title, $selected);
		
		if ($taxonomy) {
			$this->taxonomy = $taxonomy;
            $terms = get_terms(
				array(
					'taxonomy'=>$taxonomy,
					'parent'=>0
				)
			);

			if (!empty($placeholder))
			{
				$this->addLink(false, $placeholder, '', empty($selected));
			}

            if (!is_wp_error($terms)) {
                if ($terms) {
                    foreach ($terms as $t) {
						$args = $this->getFilterArgs();
						unset($args[$this->taxonomy]);
						$args[$this->taxonomy] = $t->slug;
						$filters = array('edealer_filter'=>$args);
						$query = http_build_query($filters);
                        $this->addLink('?'.$query, $t->name, $t->slug, $t->slug==$selected);
                    }
                }
            }
		}
    }
    
	
	function getFieldName()
	{
		return 'edealer_filter[' . $this->taxonomy . ']';
	}
	

	
	function constructQuery(&$args, &$tax_query, &$meta_query)
	{
		if (!empty($this->selected)) {
			$tax_query[] = array(
				'taxonomy'=>$this->taxonomy,
				'field'=>'slug',
				'terms'=>$this->selected
			);
		}
    }
	
    
}