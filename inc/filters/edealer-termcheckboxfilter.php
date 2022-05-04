<?php

class EffeciveDealer_TermsCheckboxFilter extends EffectiveDealer_ChecklistFilter {

    public $taxonomy = '';
	
	public function __construct($id, $title, $placeholder='', $taxonomy=false, $selected='')
	{
		parent::__construct($id, $title, $placeholder, array(), $selected);
		
		if ($taxonomy) {
			$this->taxonomy = $taxonomy;
            $terms = get_terms(
				array(
					'taxonomy'=>$taxonomy,
					'parent'=>0
				)
			);

            if (!is_wp_error($terms)) {
                if ($terms) {
                    foreach ($terms as $t) {
                        $this->addOption($t->slug, $t->name, $t);
                    }
                }
            }
		}
    }
    
    public function addChildren($key, $value, $data=false)
    {
        if (is_a($data, 'WP_Term')) {
            $terms = get_terms(array('taxonomy'=>$this->taxonomy, 'parent'=>$data->term_id));
            if ($terms) {
				foreach ($terms as $t) {
					$this->addOption($t->slug, $t->parent==0?$t->name:'&nbsp;&nbsp;&nbsp;'.$t->name, $t);
				}
			}
        }
    }
	
	function getFieldName()
	{
		return 'edealer_filter[' . $this->taxonomy . ']';
	}
	
	protected function get_classes($additional=array())
	{
		return array_merge(
			parent::get_classes($additional), 
			array('effective-dealer-terms-filter', 'effective-dealer-terms-filter-'.$this->taxonomy)
		);
	}
	
	function constructQuery(&$args, &$tax_query, &$meta_query)
	{
		//var_dump($this->selected);
		//exit;
		if (!empty($this->selected)) {
			$values = explode('|', $this->selected);
			$tax_query[] = array(
				'taxonomy'=>$this->taxonomy,
				'field'=>'slug',
				'terms'=>$values,
				'comparison'=>'IN'
			);
		}
    }
	
    
}