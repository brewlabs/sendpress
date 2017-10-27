<?php

/**
 * Class for logging events and errors
 *
 * @package     WP Logging Class
 * @copyright   Copyright (c) 2012, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
*/

class SendPress_Logging {

	private $log_post_type = 'spnl_log';
	private $log_type = 'spnl_log_type';
	/**
	 * Class constructor.
	 *
	 * @since 1.0
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		// create the log post type
		add_action( 'init', array( $this, 'register_post_type' ) );

		// create types taxonomy and default types
		add_action( 'init', array( $this, 'register_taxonomy' ) );

		// make a cron job for this hook to start pruning
		add_action( 'spnl_logging_prune_routine', array( $this, 'prune_logs' ) );

	}

	/**
	 * Allows you to tie in a cron job and prune old logs.
	 *
	 * @since 1.1
	 * @access public
	 *
	 * @uses $this->get_logs_to_prune()     Returns array of posts via get_posts of logs to prune
	 * @uses $this->prune_old_logs()        Deletes the logs that we don't want anymore
	 */
	public function prune_logs(){

		$should_we_prune = apply_filters( 'spnl_logging_should_we_prune', true );

		if ( $should_we_prune === false ){
			return;
		}

		$logs_to_prune = $this->get_logs_to_prune();

		
		if ( isset( $logs_to_prune ) && ! empty( $logs_to_prune ) ){
			$this->prune_old_logs( $logs_to_prune );
		}

	} // prune_logs

	/**
	 * Deletes the old logs that we don't want
	 *
	 * @since 1.1
	 * @access private
	 *
	 * @param array/obj     $logs     required     The array of logs we want to prune
	 *
	 * @uses wp_delete_post()                      Deletes the post from WordPress
	 *
	 * @filter wp_logging_force_delete_log         Allows user to override the force delete setting which bypasses the trash
	 */
	private function prune_old_logs( $logs ){

		$force = apply_filters( 'spnl_logging_force_delete_log', true );

		foreach( $logs as $l ){
			wp_delete_post( $l->ID, $force );
		}

	} // prune_old_logs

	/**
	 * Returns an array of posts that are prune candidates.
	 *
	 * @since 1.1
	 * @access private
	 *
	 * @return array     $old_logs     The array of posts that were returned from get_posts
	 *
	 * @uses apply_filters()           Allows users to change given args
	 * @uses get_posts()               Returns an array of posts from given args
	 *
	 * @filter wp_logging_prune_when           Users can change how long ago we are looking for logs to prune
	 * @filter wp_logging_prune_query_args     Gives users access to change any query args for pruning
	 */
	private function get_logs_to_prune(){

		$how_old = apply_filters( 'spnl_logging_prune_when', '2 weeks ago' );

		$args = array(
			'post_type'      => $this->log_post_type,
			'posts_per_page' => '10',
			'date_query'     => array(
				array(
					'column' => 'post_date_gmt',
					'before' => (string) $how_old,
				)
			)
		);

		$old_logs = get_posts( apply_filters( 'spnl_logging_prune_query_args', $args ) );
		
		return $old_logs;

	} // get_logs_to_prune

	/**
	 * Log types
	 *
	 * Sets up the default log types and allows for new ones to be created
	 *
	 * @access      private
	 * @since       1.0
	 *
	 * @return     array
	*/

	private function log_types() {
		$terms = array(
			'sending','error', 'event','api_request'
		);

		return apply_filters( $this->log_type , $terms );
	}


	/**
	 * Registers the wp_log Post Type
	 *
	 * @access      public
	 * @since       1.0
	 *
	 * @uses 		register_post_type()
	 *
	 * @return     void
	*/

	public function register_post_type() {

		/* logs post type */
		$t = SendPress_Option::get('show_logs', false) ? true : false;
		
		$log_args = array(
			'labels'          => array( 'name' => __( 'SPNL Logs', 'sendpress' ) ),
			'public'          => false,
			'query_var'       => false,
			'rewrite'         => false,
			'show_ui'		  => $t,
			'capability_type' => 'post',
			'supports'        => array( 'title', 'editor'),
			'can_export'      => false,
			'menu_position'	=> 150
		);
		register_post_type( $this->log_post_type , apply_filters( 'spnl_logging_post_type_args', $log_args ) );

	}


	/**
	 * Registers the Type Taxonomy
	 *
	 * The Type taxonomy is used to determine the type of log entry
	 *
	 * @access      public
	 * @since       1.0
	 *
	 * @uses 		register_taxonomy()
	 * @uses 		term_exists()
	 * @uses 		wp_insert_term()
	 *
	 * @return     void
	*/

	public function register_taxonomy() {

		register_taxonomy( 'spnl_log_type', $this->log_post_type );

		$types = self::log_types();

		foreach ( $types as $type ) {
			if( ! term_exists( $type, $this->log_type ) ) {
				wp_insert_term( $type, $this->log_type );
			}
		}
	}


	/**
	 * Check if a log type is valid
	 *
	 * Checks to see if the specified type is in the registered list of types
	 *
	 * @access      private
	 * @since       1.0
	 *
	 *
	 * @return     array
	*/

	private function valid_type( $type ) {
		return in_array( $type,  $this->log_types() );
	}


	/**
	 * Create new log entry
	 *
	 * This is just a simple and fast way to log something. Use self::insert_log()
	 * if you need to store custom meta data
	 *
	 * @access      private
	 * @since       1.0
	 *
	 * @uses 		self::insert_log()
	 *
	 * @return      int The ID of the new log entry
	*/

	public function add( $title = '', $message = '', $parent = 0, $type = null ) {

		$log_data = array(
			'post_title'   => $title,
			'post_content' => $message,
			'post_parent'  => $parent,
			'log_type'     => $type
		);

		return $this->insert_log( $log_data );

	}


	/**
	 * Stores a log entry
	 *
	 * @access      private
	 * @since       1.0
	 *
	 * @uses 		wp_parse_args()
	 * @uses 		wp_insert_post()
	 * @uses 		update_post_meta()
	 * @uses 		wp_set_object_terms()
	 * @uses 		sanitize_key()
	 *
	 * @return      int The ID of the newly created log item
	*/

	public function insert_log( $log_data = array(), $log_meta = array() ) {

		$defaults = array(
			'post_type'    => $this->log_post_type,
			'post_status'  => 'publish',
			'post_parent'  => 0,
			'post_content' => '',
			'log_type'     => false
		);

		$args = wp_parse_args( $log_data, $defaults );

		do_action( 'spnl_pre_insert_log' );

		// store the log entry
		$log_id = wp_insert_post( $args );

		// set the log type, if any
		if( $log_data['log_type'] && $this->valid_type( $log_data['log_type'] ) ) {
		
			wp_set_object_terms( $log_id, $log_data['log_type'], $this->log_type, false );
		}


		// set log meta, if any
		if( $log_id && ! empty( $log_meta ) ) {
			foreach( (array) $log_meta as $key => $meta ) {
				update_post_meta( $log_id, '_spnl_log_' . sanitize_key( $key ), $meta );
			}
		}

		do_action( 'spnl_post_insert_log', $log_id );

		return $log_id;

	}


	/**
	 * Update and existing log item
	 *
	 * @access      private
	 * @since       1.0
	 *
	 * @uses 		wp_parse_args()
	 * @uses 		wp_update_post()
	 * @uses 		update_post_meta()
	 *
	 * @return      bool True if successful, false otherwise
	*/
	public function update_log( $log_data = array(), $log_meta = array() ) {

		do_action( 'spnl_pre_update_log', $log_id );

		$defaults = array(
			'post_type'   => $this->log_post_type,
			'post_status' => 'publish',
			'post_parent' => 0
		);

		$args = wp_parse_args( $log_data, $defaults );

		// store the log entry
		$log_id = wp_update_post( $args );

		if( $log_id && ! empty( $log_meta ) ) {
			foreach( (array) $log_meta as $key => $meta ) {
				if( ! empty( $meta ) )
					update_post_meta( $log_id, '_spnl_log_' . sanitize_key( $key ), $meta );
			}
		}

		do_action( 'spnl_post_update_log', $log_id );

	}


	/**
	 * Easily retrieves log items for a particular object ID
	 *
	 * @access      private
	 * @since       1.0
	 *
	 * @uses 		self::get_connected_logs()
	 *
	 * @return      array
	*/

	public function get_logs( $object_id = 0, $type = null, $paged = null ) {
		return $this->get_connected_logs( array( 'post_parent' => $object_id, 'paged' => $paged, 'log_type' => $type ) );

	}


	/**
	 * Retrieve all connected logs
	 *
	 * Used for retrieving logs related to particular items, such as a specific purchase.
	 *
	 * @access  private
	 * @since 	1.0
	 *
	 * @uses 	wp_parse_args()
	 * @uses 	get_posts()
	 * @uses 	get_query_var()
	 * @uses 	self::valid_type()
	 *
	 * @return  array / false
	*/

	public function get_connected_logs( $args = array() ) {

		$defaults = array(
			'post_parent'    => 0,
			'post_type'      => $this->log_post_type,
			'posts_per_page' => 10,
			'post_status'    => 'publish',
			'paged'          => get_query_var( 'paged' ),
			'log_type'       => false
		);

		$query_args = wp_parse_args( $args, $defaults );

		if( $query_args['log_type'] && $this->valid_type( $query_args['log_type'] ) ) {

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $this->log_type,
					'field'    => 'slug',
					'terms'    => $query_args['log_type']
				)
			);

		}
		
		$logs = get_posts( $query_args );

		if( $logs )
			return $logs;

		// no logs found
		return false;

	}


	/**
	 * Retrieves number of log entries connected to particular object ID
	 *
	 * @access  private
	 * @since 	1.0
	 *
	 * @uses 	WP_Query()
	 * @uses 	self::valid_type()
	 *
	 * @return  int
	*/

	public function get_log_count( $object_id = 0, $type = null, $meta_query = null ) {

		$query_args = array(
			'post_parent'    => $object_id,
			'post_type'      => $this->log_post_type,
			'posts_per_page' => 5,
			'post_status'    => 'publish'
		);

		if( ! empty( $type ) && $this->valid_type( $type ) ) {

			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $this->log_type,
					'field'    => 'slug',
					'terms'    => $type
				)
			);

		}

		if( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		$logs = new WP_Query( $query_args );

		return (int) $logs->found_posts;

	}

}
