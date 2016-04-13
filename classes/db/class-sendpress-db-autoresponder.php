<?php

class SendPress_DB_Autoresponder extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . $this->prefix . 'autoresponders';
		$this->version     = '1.1';
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
			'delay_time'       => '%d',
			'action_type' => '%d',
			'when_to_send'    => '%s',
			'active' => '%d',
			'list_id' => '%d'
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
			'delay_time' => 0,
			'when_to_send'  => 'immediate',
			'action_type' => 0,
			'active' => 0,
			'list_id' => 0
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
delay_time int(11) unsigned NOT NULL,
action_type int(11) unsigned NOT NULL,
list_id int(11) DEFAULT 0,
when_to_send varchar(255) DEFAULT NULL,
active int(1) DEFAULT 0,
PRIMARY KEY  (post_id)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}