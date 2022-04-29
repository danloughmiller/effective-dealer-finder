<?php

/**
 * Assists in adding custom columns to WP Admin tables
 */
abstract class WPAdminTableColumns {

    /** @var string|string[] $post_types */
    var $post_types = 'post';

    /** @var string[] $columns An array of $key=>$label pairs for the columns added */
    private $columns = array();

    /** @var string[] $hidden_columns A set of column keys to be hidden */
    private $hidden_columns = array();

    function __construct($post_types = 'post') {
        $this->post_types = is_array($post_types)?$post_types:array($post_types);
        
        foreach ($this->post_types as $post_type) {
            add_filter("manage_edit-{$post_type}_columns", array($this, 'manageColumns'));
            add_action("manage_{$post_type}_posts_custom_column", array($this, 'displayColumn'), 10, 2);
        }
    }

    /**
     * Set the column headers
     * @param Array $columns
     */
    public function setColumns(array $columns) {
        if (!is_array($columns)) {
            throw new \Exception(__('Argument must be an array', 'optied'));
        }

        $this->columns = $columns;
    }

    /**
     * Set the hidden column keys
     * @param Array $hidden_columns
     */
    public function setHiddenColumns(array $hidden_columns) {
        if (!is_array($hidden_columns)) {
            throw new \Exception(__('Argument must be an array', 'optied'));
        }

        $this->hidden_columns = $hidden_columns;
    }

    /**
     * Registers a new column to display
     *
     * @param Array $columns Reference variable
     * @param string $key
     * @param string $title
     * @return void
     */
    function addColumn(string $key, string $title) {
        $this->columns[$key] = $title;
    }

    /**
     * Remove a column
     *
     * @param string $column_key
     * @return void
     */
    function removeColumn($column_key) {
        unset($this->columns[$column_key]);
    }

    function hideColumn($key)
    {
        if (!array_key_exists($key, $this->hidden_columns))
            $this->hidden_columns[$key] = $key;
    }

    function manageColumns($columns) {
        
        foreach ($this->hidden_columns as $key)
        {
            unset($columns[$key]);
        }

        foreach ($this->columns as $key => $title) {
            $columns[$key] = $title;
        }

        return $columns;
    }

    function displayColumn(string $column_name, $post_id)
    {
        return 'Unknown Column: ' . $column_name;
    }
}
