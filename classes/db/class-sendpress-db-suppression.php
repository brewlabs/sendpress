<?php

class SendPress_DB_Suppression extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . $this->prefix . 'suppression';
		$this->version     = '1.0';
		$this->primary_key = 'email_id';
	}

	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'email_id'       => '%d',
			'add_date'    => '%s',
			'email'    => '%s'
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
			'add_date'  => get_gmt_from_date( date('Y-m-d H:i:s') )
		);
	}

	public function add( $data ){
		return $this->replace( $data , 'suppression' );
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
email_id int(11) unsigned NOT NULL AUTO_INCREMENT,
email varchar(512) DEFAULT NULL,
add_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
PRIMARY KEY  (email_id)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}