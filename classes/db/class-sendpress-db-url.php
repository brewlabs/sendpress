<?php

class SendPress_DB_Url extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = $wpdb->prefix . $this->prefix . 'url';
		$this->version     = '1.1';
		$this->primary_key = 'url_id';
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'url_id'    => '%d',
			'url'       => '%s',
			'hash' 		=> '%s'
		);
	}

	public function hash( $url = '' ){
		return wp_hash( $url , 'sendpress' );
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_column_defaults() {
		return array();
	}

	/**
	 * Add url to db
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add( $url ){

		if( strpos( $url , '{sp-') != false && strpos( $url , '}' ) != false ) {
			$url = esc_url( $url );
		}

		return $this->insert( array( 'url' => $url , 'hash' => $this->hash( $url ) ) , 'url' );
	}

	/**
	 * Checks if a url has a hash
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function exists( $url = '' ) {
		return (bool) $this->get_column_by( 'url_id', 'hash', $this->hash( $url ) );
	}

	/**
	 * Checks if a url has a hash
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get( $url = '' ) {
		return $this->get_column_by( 'url_id', 'hash', $this->hash( $url ) );
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
		url_id int(11) unsigned NOT NULL AUTO_INCREMENT,
		url text,
		hash varchar(255) DEFAULT NULL, 
		PRIMARY KEY  (url_id),
		KEY hash (hash)
		) $collate; \n";



		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}