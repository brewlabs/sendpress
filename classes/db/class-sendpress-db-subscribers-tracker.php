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
		$this->version     = '1.2';

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
			'opened_count'    => '%s',
			'sent_at' 		  => '%s',
			'status'          => '%d',
			'subscriber_id'   => '%d',
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
			'status'  => 0,
			'opened_count' => 0
		);
	}

	public function add( $data ){
		return $this->replace( $data , 'tracker' );
	}

	public function get_opens_total( $email_id ) {
		global $wpdb;
		$q = $wpdb->prepare("SELECT SUM(opened_count) FROM $this->table_name WHERE email_id = %d and status > 0 ", $email_id);
		return $wpdb->get_var( $q );
	}

	public function get_most_active($limit = 10){
		global $wpdb;
		$q = $wpdb->prepare("SELECT  subscriber_id , SUM(opened_count) as count FROM $this->table_name WHERE status > 0 group by subscriber_id order by SUM(opened_count) DESC LIMIT %d ", $limit);
		return $wpdb->get_results( $q );
	}



	public function get_opens( $email_id ) {
		global $wpdb;
		$q = $wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE email_id = %d and status > 0 ", $email_id);
		return $wpdb->get_var( $q );
	}

	public function get_unsubs( $email_id ) {
		global $wpdb;
		$q = $wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE email_id = %d and status = 3 ", $email_id);
		return $wpdb->get_var( $q );
	}

	public function opens_top_email_id_subscriber( $email_id , $count = 5){
		global $wpdb;
		$q = $wpdb->prepare(" SELECT SUM(opened_count) as count , subscriber_id  FROM $this->table_name WHERE email_id = %d and status > 0 GROUP BY subscriber_id ORDER BY opened_count  DESC LIMIT %d ", $email_id , $count );
		return $wpdb->get_results( $q );
	}

	public function open( $email_id , $subscriber_id , $status = 1) {
		global $wpdb;

		// Initialise column format array
		$column_formats = $this->get_columns();

		$data = array( 'status' => $status,'opened_count' => 1, 'opened_at' => get_gmt_from_date( date('Y-m-d H:i:s') ) );
		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( false === $rows = $wpdb->update( $this->table_name, $data, array( 'email_id' => $email_id , 'subscriber_id' => $subscriber_id , 'status'=> 0  , 'opened_at' => '0000-00-00 00:00:00' ), $column_formats ) ) {
			return false;
		}

		if( $rows === 0 && $status === 1){
			$wpdb->query($wpdb->prepare(" Update " . $this->table_name ." set opened_count = opened_count+1 where email_id = %s and subscriber_id = %s ", $email_id, $subscriber_id ));
		}

		if( $rows === 0 && $status > 1 ){
			if ( false === $wpdb->update( $this->table_name, array('status' => $status ), array( 'email_id' => $email_id , 'subscriber_id' => $subscriber_id  ) ) ) {
				return false;
			}
		}


		return true;

	}

	public function unsub( $email_id , $subscriber_id , $status = 3) {
		global $wpdb;

		// Initialise column format array
		$column_formats = $this->get_columns();

		$data = array( 'status' => $status );
		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( false === $rows = $wpdb->update( $this->table_name, $data, array( 'email_id' => $email_id , 'subscriber_id' => $subscriber_id  ), $column_formats ) ) {
			return false;
		}

		return true;

	}

	public function stats( $email_id ){
		global $wpdb;
		$subs_table = SendPress_DB_Tables::subscriber_table();
		$url_table = SPNL()->load("Subscribers_Url")->table_name;
		$q = $wpdb->prepare(" SELECT su.email, st.opened_count as opens, st.opened_at,st.status , SUM(ut.click_count) as clicks FROM $this->table_name as st LEFT JOIN $url_table as ut on ut.subscriber_id = st.subscriber_id and ut.email_id = st.email_id LEFT JOIN $subs_table as su on su.subscriberID = st.subscriber_id WHERE st.email_id = %d and st.status > 0 GROUP BY st.subscriber_id order by su.email  ", $email_id );

		return $wpdb->get_results( $q );
	}

	public function total_send_list( $email_id ){
		global $wpdb;
		$subs_table = SendPress_DB_Tables::subscriber_table();
		$url_table = SPNL()->load("Subscribers_Url")->table_name;
		$q = $wpdb->prepare(" SELECT su.email, st.opened_count as opens, st.opened_at,st.status , SUM(ut.click_count) as clicks FROM $this->table_name as st LEFT JOIN $url_table as ut on ut.subscriber_id = st.subscriber_id and ut.email_id = st.email_id LEFT JOIN $subs_table as su on su.subscriberID = st.subscriber_id WHERE st.email_id = %d GROUP BY st.subscriber_id order by su.email  ", $email_id );

		return $wpdb->get_results( $q );
	}

	public function unsubscribe_list( $email_id ){
		global $wpdb;
		$subs_table = SendPress_DB_Tables::subscriber_table();
		$url_table = SPNL()->load("Subscribers_Url")->table_name;
		$q = $wpdb->prepare(" SELECT su.email, st.opened_count as opens, st.opened_at,st.status , SUM(ut.click_count) as clicks FROM $this->table_name as st LEFT JOIN $url_table as ut on ut.subscriber_id = st.subscriber_id and ut.email_id = st.email_id LEFT JOIN $subs_table as su on su.subscriberID = st.subscriber_id WHERE st.email_id = %d and st.status = 3 GROUP BY st.subscriber_id order by su.email  ", $email_id );

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
sent_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
opened_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
status tinyint(4) NOT NULL DEFAULT '0',
tracker_type tinyint(4) NOT NULL DEFAULT '0',
opened_count int(11) unsigned NOT NULL,
PRIMARY KEY  (subscriber_id,email_id),
KEY tracker_type (tracker_type)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
