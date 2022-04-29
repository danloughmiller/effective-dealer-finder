<?php
require_once('WPAdminTableColumns.php');

class DealerTableColumns extends WPAdminTableColumns
{
    public function __construct()
    {
        $post_type = 'dealer';
        parent::__construct($post_type);

        $this->hideColumn('date');

        $this->addColumn('address', 'Address');
        $this->addColumn('city', 'City');
        $this->addColumn('state', 'State');
        $this->addColumn('postalcode', 'Postal Code');
        $this->addColumn('phone', 'Phone');
        $this->addColumn('email', 'Email');
        $this->addColumn('latlng', 'Lat/Lng');
    }

    function displayColumn(string $column_name, $post_id)
    {
        switch ($column_name) {
            case 'address':
                echo get_post_meta($post_id, 'dealer_address', true);
                break;
            case 'city':
                echo get_post_meta($post_id, 'dealer_city', true);
                break;
            case 'state':
                echo get_post_meta($post_id, 'dealer_state', true);
                break;
            case 'postalcode':
                echo get_post_meta($post_id, 'dealer_postal_code', true);
                break;
            case 'phone':
                echo get_post_meta($post_id, 'dealer_phone', true);
                break;
            case 'email':
                echo get_post_meta($post_id, 'dealer_email', true);
                break;
            case 'latlng':
                echo get_post_meta($post_id, 'dealer_latitude', true);
                echo ', ';
                echo get_post_meta($post_id, 'dealer_longitude', true);
        }

    }
}


new DealerTableColumns();
