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
			'click_count' => 1
			);
	}

	/**
	 * Add subscriber click info
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add( $data ) {
		$data = $this->validate( $data );
		if($data == false) {
			return $data;
		}
		return $this->insert( $data , 'subscribers_url' );

	}

	public function validate( $data ) {
		if(	isset($data['email_id']) && isset($data['subscriber_id']) && isset($data['url_id'])  ){
			$data['email_id'] = SPNL()->validate->int( $data['email_id'] );
			if( $data['email_id'] == 0){
				return false;
			}
			$data['subscriber_id'] = SPNL()->validate->int( $data['subscriber_id'] );
			if( $data['subscriber_id'] == 0){
				return false;
			}
			$data['url_id'] = SPNL()->validate->int( $data['url_id'] );
			if( $data['url_id'] == 0){
				return false;
			}
			return $data;
		}
		return false;
	}

	/**
	 * Add or update subscriber url info
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function add_update( $data ) {
		global $wpdb;

		$data = $this->validate( $data );
		if($data == false) {
			return $data;
		}

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


	public function clicks_total_email_id( $email_id ) {
		global $wpdb;
		$q = $wpdb->prepare(" SELECT SUM(click_count) FROM $this->table_name WHERE email_id = %d ", $email_id);
		return $wpdb->get_var( $q );	
	}

	public function clicks_email_id( $email_id ) {
		global $wpdb;
		$q = $wpdb->prepare(" SELECT COUNT(*) FROM $this->table_name WHERE email_id = %d ", $email_id);
		return $wpdb->get_var( $q );
		
	}

	public function clicks_top_email_id_subscriber( $email_id , $count = 5){
		global $wpdb;
		$q = $wpdb->prepare(" SELECT SUM(click_count) as count,subscriber_id  FROM $this->table_name WHERE email_id = %d GROUP BY subscriber_id ORDER BY click_count  DESC LIMIT %d ", $email_id , $count );
		return $wpdb->get_results( $q );
	}

	public function clicks_top_email_id_url( $email_id , $count = 5){
		global $wpdb;
		$q = $wpdb->prepare(" SELECT SUM(click_count) as count,url_id  FROM $this->table_name  WHERE email_id = %d GROUP BY url_id ORDER BY click_count  DESC LIMIT %d ", $email_id , $count );
		return $wpdb->get_results( $q );
	}

	public function links_with_counts( $email_id ){
		global $wpdb;
		$url_table = SPNL()->load("Url")->table_name;
		$q = $wpdb->prepare(" SELECT COUNT(subscriber_id) as clicks, SUM(click_count) as totalclicks , su.url_id , su.url FROM $this->table_name as ssu INNER JOIN $url_table as su on su.url_id = ssu.url_id WHERE ssu.email_id = %d GROUP BY su.url_id ORDER BY totalclicks  ", $email_id );
		return $wpdb->get_results( $q );
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
		    PRIMARY KEY  (subscriber_id,email_id,url_id)
		) $collate;\n";



		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}