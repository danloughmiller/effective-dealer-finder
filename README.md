# effective-dealer-finder

```
<?php

add_filter('EFFDF_GOOGLE_API_KEY', function($e) { return '{key}';});


global $dealer_finder;

add_action('wp', 'setup_dealer_finder');

function setup_dealer_finder() 
{

	if (!class_exists('EffectiveDealerFinder')) return;

	global $dealer_finder;

    $filters = new EffectiveDealer_Filters(array('update_filters'=>'Filter Results'));
    
    $search_filter = new EffectiveDealer_LocationFilter(
                            'effdf-location', 
                            'Search',
                            '',
                            !empty($_REQUEST['edealer_filters']['location']['ds'])?$_REQUEST['edealer_filters']['location']['ds']:'',
                            !empty($_REQUEST['edealer_filters']['location']['lat'])?$_REQUEST['edealer_filters']['location']['lat']:'',
                            !empty($_REQUEST['edealer_filters']['location']['lng'])?$_REQUEST['edealer_filters']['location']['lng']:''
                        );
    $filters->addFilter($search_filter);

    $filters->addFilter(
                new EffeciveDealer_TermsFilter(
                    'dealer_types', 
                    'Type', 
                    'All Types', 
                    'dealer_types', 
                    (!empty($_REQUEST['edealer_filter']['dealer_types'])?$_REQUEST['edealer_filter']['dealer_types']:'')
                )
            );
    


	$dealer_finder = new EffectiveDealer_DefaultPostFinder('dealers', 'dealer', array(), array());
    //$dealer_finder->itemsPerPage=48;
	$dealer_finder->filters = $filters;

	if (!empty($_REQUEST['edealer_page'])) $dealer_finder->setPage( $_REQUEST['edealer_page'] );



} 

add_shortcode('dealer_finder', 'add_dealer_finder');

function add_dealer_finder()
{
    global $dealer_finder;
    if (empty($dealer_finder))
        setup_dealer_finder();

    if (!empty($dealer_finder)) {
        return $dealer_finder->render();
    }
}
```
