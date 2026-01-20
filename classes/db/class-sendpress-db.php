<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * SendPress DB base class
 *
 * @package     SendPress
 * @subpackage  Classes/DB/SendPress DB
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.12.11
*/

abstract class SendPress_DB {

	public $prefix = 'sendpress_';	
	/**
	 * The name of our database table
	 *
	 * @access  public
	 * @since   1.0.12.11
	 */
	public $table_name;

	/**
	 * The version of our database table
	 *
	 * @access  public
	 * @since   1.0.12.11
	 */
	public $version;

	/**
	 * The name of the primary column
	 *
	 * @access  public
	 * @since   1.0.12.11
	 */
	public $primary_key;

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0.12.11
	 */
	public function __construct() {}

	/**
	 * Whitelist of columns
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  array
	 */
	public function get_columns() {
		return array();
	}

	/**
	 * Validate that a column name exists in this table's schema
	 *
	 * @access  protected
	 * @since   1.26.1.20
	 * @param   string $column Column name to validate
	 * @return  bool   True if valid column, false otherwise
	 */
	protected function is_valid_column( $column ) {
		$valid_columns = $this->get_columns();
		return array_key_exists( $column, $valid_columns );
	}

	/**
	 * Default column values
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  array
	 */
	public function get_column_defaults() {
		return array();
	}

	/**
	 * Retrieve a row by the primary key
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  object
	 */
	public function get( $row_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $this->primary_key = %d LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a row by a specific column / value
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  object|null
	 */
	public function get_by( $column, $row_id ) {
		global $wpdb;
		// Validate column against whitelist to prevent SQL injection
		if ( ! $this->is_valid_column( $column ) ) {
			return null;
		}
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the primary key
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  string|null
	 */
	public function get_column( $column, $row_id ) {
		global $wpdb;
		// Validate column against whitelist to prevent SQL injection
		if ( ! $this->is_valid_column( $column ) ) {
			return null;
		}
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $this->primary_key = %d LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieve a specific column's value by the the specified column / value
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  string|null
	 */
	public function get_column_by( $column, $column_where, $column_value ) {
		global $wpdb;
		// Validate both columns against whitelist to prevent SQL injection
		if ( ! $this->is_valid_column( $column ) || ! $this->is_valid_column( $column_where ) ) {
			return null;
		}
		return $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;", $column_value ) );
	}


	public function make_where( $where ) {
		$valid_columns = $this->get_columns();
		$wheres = array();
		foreach ( (array) array_keys( $where ) as $field ) {
			// Validate field against whitelist to prevent SQL injection
			if ( ! array_key_exists( $field, $valid_columns ) ) {
				continue;
			}
			$form = isset( $valid_columns[$field] ) ? $valid_columns[$field] : '%s';
			$wheres[] = "`$field` = {$form}";
		}

		return implode( ' AND ', $wheres );
	}

	/**
	 * Insert a new row
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  int
	 */
	public function replace( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'spnl_pre_replace_' . $type , $data );

		// Initialise column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );
		
		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->replace( $this->table_name, $data, $column_formats );

		do_action( 'spnl_post_replace_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}


	/**
	 * Insert a new row
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  int
	 */
	public function insert( $data, $type = '' ) {
		global $wpdb;

		// Set default values
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'spnl_pre_insert_' . $type , $data );

		// Initialise column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );
		
		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->insert( $this->table_name, $data, $column_formats );

		do_action( 'spnl_post_insert_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Update a row
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  bool
	 */
	public function update( $row_id, $data = array(), $where = '' ) {

		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		if( empty( $row_id ) ) {
			return false;
		}

		if( empty( $where ) ) {
			$where = $this->primary_key;
		}

		// Initialise column format array
		$column_formats = $this->get_columns();

		// Force fields to lower case
		$data = array_change_key_case( $data );

		// White list columns
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data
		$data_keys = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( false === $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Delete a row identified by the primary key
	 *
	 * @access  public
	 * @since   1.0.12.11
	 * @return  bool
	 */
	public function delete( $row_id = 0 ) {

		global $wpdb;

		// Row ID must be positive integer
		$row_id = absint( $row_id );

		if( empty( $row_id ) ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id ) ) ) {
			return false;
		}

		return true;
	}

	public function repair_table(){
	    global $wpdb;
	    $wpdb->query("REPAIR TABLE  $this->table_name");
	}

}