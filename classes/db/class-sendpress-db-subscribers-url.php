<?php

class SendPress_DB_Subscribers_Url extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;

		$this->table_name  = $wpdb->prefix . $this->prefix . 'subscribers_url';
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
			'subscriber_id' => '%d',
			'email_id'    	=> '%d',
			'url_id'   		=> '%d',
			'clicked_at'    => '%s',
			'click_count' 	=> '%d'
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
			'clicked_at' => get_gmt_from_date( date('Y-m-d H:i:s') ),
			'click_count' => 0
			);
	}

	/**
	 * Add subscriber click info
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add( $data ) {
		return $this->insert( $data , 'subscribers_url' );
	}

	/**
	 * Add or update subscriber url info
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add_update( $data ) {
		global $wpdb;
		if( ( $surl = $this->get_by_all( $data ) ) !== NULL ){
			if ( false === $wpdb->update( $this->table_name, array('click_count' => $surl->click_count + 1) , $data ) ) {
				return false;
			}
			return true;
		} else {
			$this->add( $data );
			return true;
		}
		return false;
	}

	/**
	 * Add or update subscriber url info
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_by_all( $data ) {
		global $wpdb;
		$where = $this->make_where($data);
		return $wpdb->get_row( $wpdb->prepare(  "SELECT * FROM $this->table_name WHERE $where LIMIT 1;", $data ) );
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
		    url_id int(11) unsigned NOT NULL,
		    clicked_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
		    click_count int(11) unsigned NOT NULL,
		    PRIMARY KEY  ( subscriber_id , email_id , url_id )
		) $collate;";



		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}