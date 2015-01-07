<?php

class SendPress_DB_Subscribers_Tracker extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = $wpdb->prefix . $this->prefix . 'subscribers_tracker';
		$this->version     = '1.1';
	
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'email_id'        => '%d',
			'opened_at'       => '%s',
			'sent_at' 		  => '%s',
			'status'          => '%d',
			'subscriber_id'   => '%d'
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
			'sent_at' => get_gmt_from_date( date('Y-m-d H:i:s') ),
			'status'  => 0
		);
	}

	public function add( $data ){
		return $this->insert( $data , 'tracker' );
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
			subscriber_id int(11) unsigned NOT NULL,
		  	email_id int(11) unsigned NOT NULL,
		  	sent_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
		  	opened_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  	status tinyint(4) NOT NULL DEFAULT '0',
		  	PRIMARY KEY  (subscriber_id, email_id)
			) $collate;";



		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}