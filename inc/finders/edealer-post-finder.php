<?php


class EffectiveDealer_PostFinder extends EffectiveDealerFinder
{
	public $post_type = false;
    public $taxonomies = array();
    public $additional_query_args = array();
    public $createElementCallback = false;
		
	public function __construct($grid_id, $post_type='dealer', $taxonomies = array(), $additional_query_args=array(), $createElementCallback = false)
	{
		parent::__construct($grid_id, false);
		
		$this->post_type = $post_type;
        $this->taxonomies = $taxonomies;
        $this->additional_query_args = $additional_query_args;
        $this->createElementCallback = $createElementCallback;
	}
	
	protected function constructQuery()
	{
		global $wpdb;
		
		$args = array(
			'post_type'=>$this->post_type,
			'posts_per_page'=>$this->itemsPerPage,
			'paged'=>$this->page,
            'orderby'=>'title',
            'order'=>'ASC',
            'suppress_filters'=>0
        );
        
        if (is_array($this->additional_query_args))
            $args = array_merge($args, $this->additional_query_args);
		
		//If this has filters we'll apply those now
		if (!empty($this->filters->filters) && count($this->filters->filters)>0) {
			$tax_query = array();
			
			foreach ($this->filters->filters as $filter) {
				$filter->constructQuery($args, $tax_query);
			}
			
			$args['tax_query'] = $tax_query;
			
        }
        
		return $args;
    }
    	
	function getElements()
	{
        $posts = get_posts($this->constructQuery());
					
		$elements = array();
		foreach ($posts as $r) {
            if (!empty($this->createElementCallback)) {
                $elements[] = call_user_func($this->createElementCallback, $r);
            } else {
                $elements[] = new EffectiveDealer_PostElement($r);
            }
		}
		return $elements;
	}
	
	function getElementCount() {
        $query = $this->constructQuery();
        $query['posts_per_page']=-1;
        $q = get_posts($query);
		return count($q);
	}
	
	function getPaginationLink($pindex) { 
		$link = '?edealer_page='.$pindex;
		
		if (!empty($this->filters->filters) && count($this->filters->filters)>0) {			
			foreach ($this->filters->filters as $filter) {
				if (!empty($filter->selected)) {
					$link .= '&edealer_filter[' . $filter->taxonomy . ']=' . $filter->selected;
				}
			}
		}
		
		return $link;
	}
	
	function getClasses($additional=array())
	{
		return parent::getClasses(array('effective-grid-postgrid'));
	}

}