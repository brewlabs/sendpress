<?php

class SendPress_DB_Schedules extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . $this->prefix . 'schedules';
		$this->version     = '1.0';
		$this->primary_key = 'post_id';
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'post_id'   => '%d',
			'email_id'       => '%d',
			'when_to_send'    => '%s',
			'title' => '%s',
			'active' => '%d'
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
			'when_to_send'  => 'immediate',
			'active' => 0
		);
	}

	public function add( $data ){
		return $this->replace( $data , 'autoresponder' );
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
post_id int(11) unsigned NOT NULL,
email_id int(11) unsigned NOT NULL,
when_to_send varchar(255) DEFAULT NULL,
title varchar(1000) DEFAULT NULL,
active int(1) DEFAULT 0,
PRIMARY KEY  (post_id)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}