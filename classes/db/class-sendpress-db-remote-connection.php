<?php

class SendPress_DB_Remote_Connection extends SendPress_DB {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . $this->prefix . 'connections';
		$this->version     = '1.0';
		$this->primary_key = 'connection_id';
	}

	public function get_default( ) {
		global $wpdb;
		return $wpdb->get_row( "SELECT * FROM $this->table_name ORDER BY  $this->primary_key LIMIT 1;" );
	}
	/**
	 * Get table columns and date types
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'connection_id'   => '%d',
			'api_key'       => '%s',
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
			'active' => 0,
			'api_key' => sanitize_title(wp_hash_password(microtime()))
		);
	}

	public function create(){
		return $this->replace( array() , 'connection' );
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
connection_id int(11) unsigned NOT NULL AUTO_INCREMENT,
api_key varchar(255) DEFAULT NULL,
active int(1) DEFAULT 0,
PRIMARY KEY  (connection_id)
) $collate;\n";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}