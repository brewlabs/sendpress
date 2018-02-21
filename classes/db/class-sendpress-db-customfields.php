<?php

class SendPress_DB_Customfields extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . $this->prefix . 'customfields';
		$this->version     = '1.3';
		$this->primary_key = 'id';
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'id'   => '%d',
			'label'  => '%s',
			'slug' => '%s',
			'old_slug' => '%s'
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_column_defaults() {
		return array(
			'label' => "",
			'slug'  => "",
			'old_slug' => ""
		);
	}

	public function get_all() {
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM $this->table_name;", ARRAY_A );
	}

	public function add( $data ){
		return $this->replace( $data , 'customfields' );
	}
	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		$collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if( ! empty($wpdb->charset ) ){
                  $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }
              
            if( ! empty($wpdb->collate ) ){
                 $collate .= " COLLATE $wpdb->collate";
            }
               
        }

$sql = " CREATE TABLE {$this->table_name} (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
label varchar(255) DEFAULT NULL, 
slug varchar(255) DEFAULT NULL, 
old_slug varchar(255) DEFAULT NULL, 
PRIMARY KEY  (id)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}