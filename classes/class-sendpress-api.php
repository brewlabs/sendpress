<?php
/**
 * SendPress
 *
 * This class provides a front-facing JSON/XML API that makes it possible to
 * query data SendPress
 *
 * @since       1.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class SendPress_API {

	/**
	 * API Version
	 */
	const VERSION = '1.0';

	/**
	 * Pretty Print?
	 *
	 * @var bool
	 * @access private
	 * @since 1.5
	 */
	private $pretty_print = false;

	/**
	 * Log API requests?
	 *
	 * @var bool
	 * @access private
	 * @since 1.5
	 */
	public $log_requests = true;

	/**
	 * Is this a valid request?
	 *
	 * @var bool
	 * @access private
	 * @since 1.5
	 */
	private $is_valid_request = false;

	/**
	 * User ID Performing the API Request
	 *
	 * @var int
	 * @access private
	 * @since 1.5.1
	 */
	private $user_id = 0;

	
	/**
	 * Response data to return
	 *
	 * @var array
	 * @access private
	 * @since 1.5.2
	 */
	private $data = array();

	/**
	 *
	 * @var bool
	 * @access private
	 * @since 1.7
	 */
	private $override = true;

	public function __construct() {
		add_action( 'init',                     array( $this, 'add_endpoint'     ) );
		add_action( 'template_redirect',        array( $this, 'process_query'    ), -1 );
		add_filter( 'query_vars',               array( $this, 'query_vars'       ) );
		add_action( 'show_user_profile',        array( $this, 'user_key_field'   ) );
		add_action( 'edit_user_profile',        array( $this, 'user_key_field'   ) );
		add_action( 'personal_options_update',  array( $this, 'update_key'       ) );
		add_action( 'edit_user_profile_update', array( $this, 'update_key'       ) );
		add_action( 'edd_process_api_key',      array( $this, 'process_api_key'  ) );

		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;

		// Allow API request logging to be turned off
		$this->log_requests = apply_filters( 'spnl_api_log_requests', $this->log_requests );

	}

	
	public function add_endpoint( $rewrite_rules ) {
		error_log('here');
		add_rewrite_endpoint( 'spnl-api', EP_ALL );
	}

	public function query_vars( $vars ) {
		$vars[] = 'token';
		$vars[] = 'key';
		$vars[] = 'query';
		$vars[] = 'type';
		$vars[] = 'number';
		$vars[] = 'date';
		$vars[] = 'startdate';
		$vars[] = 'enddate';
		$vars[] = 'customer';
		$vars[] = 'discount';
		$vars[] = 'format';
		$vars[] = 'id';
		$vars[] = 'email';

		return $vars;
	}

	private function validate_request() {
		global $wp_query;

		$this->override = false;

        // Make sure we have both user and api key
		if ( ! empty( $wp_query->query_vars['spnl-api'] ) && ! empty( $wp_query->query_vars['token'] ) ) {
			if ( empty( $wp_query->query_vars['token'] ) || empty( $wp_query->query_vars['key'] ) ) {
				$this->missing_auth();
			}

			// Retrieve the user by public API key and ensure they exist
			if ( ! ( $user = $this->get_user( $wp_query->query_vars['key'] ) ) ) :
				$this->invalid_key();
			else :
				$token  = urldecode( $wp_query->query_vars['token'] );
				$secret = get_user_meta( $user, 'spnl_user_secret_key', true );
				$public = urldecode( $wp_query->query_vars['key'] );

				if ( hash_equals( md5( $secret . $public ), $token ) )
					$this->is_valid_request = true;
				else
					$this->invalid_auth();
			endif;
		} elseif ( !empty( $wp_query->query_vars['spnl-api'] ) && $wp_query->query_vars['spnl-api'] == 'errors' ) {
			$this->is_valid_request = true;
			$wp_query->set( 'key', 'public' );
		}
	}

	
	public function get_user( $key = '' ) {
		global $wpdb, $wp_query;

		if( empty( $key ) )
			$key = urldecode( $wp_query->query_vars['key'] );

		if ( empty( $key ) ) {
			return false;
		}

		$user = get_transient( md5( 'spnl_api_user_' . $key ) );

		if ( false === $user ) {
			$user = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'spnl_user_public_key' AND meta_value = %s LIMIT 1", $key ) );
			set_transient( md5( 'spnl_api_user_' . $key ) , $user, DAY_IN_SECONDS );
		}

		if ( $user != NULL ) {
			$this->user_id = $user;
			return $user;
		}

		return false;
	}

	
	private function missing_auth() {
		$error = array();
		$error['error'] = __( 'You must specify both a token and API key!', 'edd' );

		$this->data = $error;
		$this->output( 401 );
	}

	private function invalid_auth() {
		$error = array();
		$error['error'] = __( 'Your request could not be authenticated!', 'edd' );

		$this->data = $error;
		$this->output( 401 );
	}

	private function invalid_key() {
		$error = array();
		$error['error'] = __( 'Invalid API key!', 'edd' );

		$this->data = $error;
		$this->output( 401 );
	}


	public function process_query() {
		global $wp_query;

		// Check for edd-api var. Get out if not present
		if ( ! isset( $wp_query->query_vars['spnl-api'] ) )
			return;

		// Check for a valid user and set errors if necessary
		$this->validate_request();

		// Only proceed if no errors have been noted
		if( ! $this->is_valid_request )
			return;

		if( ! defined( 'SPNL_DOING_API' ) ) {
			define( 'SPNL_DOING_API', true );
		}

		// Determine the kind of query
		$query_mode = $this->get_query_mode();

		$data = array();

		switch( $query_mode ) :

			case 'errors' :
				
				$type = isset( $wp_query->query_vars['type'] )   ? $wp_query->query_vars['type']   : null;

				$data = $this->get_errors( $type );

				break;

			case 'stats' :

				$data = $this->get_stats( array(
					'type'      => isset( $wp_query->query_vars['type'] )      ? $wp_query->query_vars['type']      : null,
					'product'   => isset( $wp_query->query_vars['product'] )   ? $wp_query->query_vars['product']   : null,
					'date'      => isset( $wp_query->query_vars['date'] )      ? $wp_query->query_vars['date']      : null,
					'startdate' => isset( $wp_query->query_vars['startdate'] ) ? $wp_query->query_vars['startdate'] : null,
					'enddate'   => isset( $wp_query->query_vars['enddate'] )   ? $wp_query->query_vars['enddate']   : null
				) );

				break;

			case 'products' :

				$product = isset( $wp_query->query_vars['product'] )   ? $wp_query->query_vars['product']   : null;

				$data = $this->get_products( $product );

				break;

			case 'customers' :

				$customer = isset( $wp_query->query_vars['customer'] ) ? $wp_query->query_vars['customer']  : null;

				$data = $this->get_customers( $customer );

				break;

			case 'sales' :

				$data = $this->get_recent_sales();

				break;

			case 'discounts' :

				$discount = isset( $wp_query->query_vars['discount'] ) ? $wp_query->query_vars['discount']  : null;

				$data = $this->get_discounts( $discount );

				break;

		endswitch;

		// Allow extensions to setup their own return data
		$this->data = apply_filters( 'spnl_api_output_data', $data, $query_mode, $this );

		// Log this API request, if enabled. We log it here because we have access to errors.
		//$this->log_request( $this->data );

		// Send out data to the output function
		$this->output();
	}

	/**
	 * Determines the kind of query requested and also ensure it is a valid query
	 *
	 * @access private
	 * @since 1.5
	 * @global $wp_query
	 * @return string $query Query mode
	 */
	public function get_query_mode() {
		global $wp_query;

		// Whitelist our query options
		$accepted = apply_filters( 'spnl_api_valid_query_modes', array(
			'errors'
		) );

		$query = isset( $wp_query->query_vars['spnl-api'] ) ? $wp_query->query_vars['spnl-api'] : null;
		$error = array();
		// Make sure our query is valid
		if ( ! in_array( $query, $accepted ) ) {
			$error['error'] = __( 'Invalid query!', 'sendpress' );

			$this->data = $error;
			$this->output();
		}

		return $query;
	}

	/**
	 * Get page number
	 *
	 * @access private
	 * @since 1.5
	 * @global $wp_query
	 * @return int $wp_query->query_vars['page'] if page number returned (default: 1)
	 */
	public function get_paged() {
		global $wp_query;

		return isset( $wp_query->query_vars['page'] ) ? $wp_query->query_vars['page'] : 1;
	}


	/**
	 * Number of results to display per page
	 *
	 * @access private
	 * @since 1.5
	 * @global $wp_query
	 * @return int $per_page Results to display per page (default: 10)
	 */
	public function per_page() {
		global $wp_query;

		$per_page = isset( $wp_query->query_vars['number'] ) ? $wp_query->query_vars['number'] : 10;

		if( $per_page < 0 && $this->get_query_mode() == 'customers' )
			$per_page = 99999999; // Customers query doesn't support -1

		return apply_filters( 'edd_api_results_per_page', $per_page );
	}

	/**
	 * Retrieve the output format
	 *
	 * Determines whether results should be displayed in XML or JSON
	 *
	 * @since 1.5
	 *
	 * @return mixed|void
	 */
	public function get_output_format() {
		global $wp_query;

		$format = isset( $wp_query->query_vars['format'] ) ? $wp_query->query_vars['format'] : 'json';

		return apply_filters( 'spnl_api_output_format', $format );
	}

	/**
	 * Sets up the dates used to retrieve earnings/sales
	 *
	 * @access public
	 * @since 1.5.1
	 * @param array $args Arguments to override defaults
	 * @return array $dates
	*/
	public function get_dates( $args = array() ) {
		$dates = array();

		$defaults = array(
			'type'      => '',
			'product'   => null,
			'date'      => null,
			'startdate' => null,
			'enddate'   => null
		);

		$args = wp_parse_args( $args, $defaults );

		$current_time = current_time( 'timestamp' );

		if ( 'range' === $args['date'] ) {
			$startdate          = strtotime( $args['startdate'] );
			$enddate            = strtotime( $args['enddate'] );
			$dates['day_start'] = date( 'd', $startdate );
			$dates['day_end']   = date( 'd', $enddate );
			$dates['m_start']   = date( 'n', $startdate );
			$dates['m_end']     = date( 'n', $enddate );
			$dates['year']      = date( 'Y', $startdate );
			$dates['year_end'] 	= date( 'Y', $enddate );
		} else {
			// Modify dates based on predefined ranges
			switch ( $args['date'] ) :

				case 'this_month' :
					$dates['day']       = null;
					$dates['m_start']   = date( 'n', $current_time );
					$dates['m_end']     = date( 'n', $current_time );
					$dates['year']      = date( 'Y', $current_time );
				break;

				case 'last_month' :
					$dates['day']     = null;
					$dates['m_start'] = date( 'n', $current_time ) == 1 ? 12 : date( 'n', $current_time ) - 1;
					$dates['m_end']   = $dates['m_start'];
					$dates['year']    = date( 'n', $current_time ) == 1 ? date( 'Y', $current_time ) - 1 : date( 'Y', $current_time );
				break;

				case 'today' :
					$dates['day']       = date( 'd', $current_time );
					$dates['m_start']   = date( 'n', $current_time );
					$dates['m_end']     = date( 'n', $current_time );
					$dates['year']      = date( 'Y', $current_time );
				break;

				case 'yesterday' :

					$year               = date( 'Y', $current_time );
					$month              = date( 'n', $current_time );
					$day                = date( 'd', $current_time );

					if ( $month == 1 && $day == 1 ) {

						$year -= 1;
						$month = 12;
						$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );

					} elseif ( $month > 1 && $day == 1 ) {

						$month -= 1;
						$day   = cal_days_in_month( CAL_GREGORIAN, $month, $year );

					} else {

						$day -= 1;

					}

					$dates['day']       = $day;
					$dates['m_start']   = $month;
					$dates['m_end']     = $month;
					$dates['year']      = $year;

				break;

				case 'this_quarter' :
					$month_now = date( 'n', $current_time );

					$dates['day']           = null;

					if ( $month_now <= 3 ) {

						$dates['m_start']   = 1;
						$dates['m_end']     = 3;
						$dates['year']      = date( 'Y', $current_time );

					} else if ( $month_now <= 6 ) {

						$dates['m_start']   = 4;
						$dates['m_end']     = 6;
						$dates['year']      = date( 'Y', $current_time );

					} else if ( $month_now <= 9 ) {

						$dates['m_start']   = 7;
						$dates['m_end']     = 9;
						$dates['year']      = date( 'Y', $current_time );

					} else {

						$dates['m_start']   = 10;
						$dates['m_end']     = 12;
						$dates['year']      = date( 'Y', $current_time );

					}
				break;

				case 'last_quarter' :
					$month_now = date( 'n', $current_time );

					$dates['day']           = null;

					if ( $month_now <= 3 ) {

						$dates['m_start']   = 10;
						$dates['m_end']     = 12;
						$dates['year']      = date( 'Y', $current_time ) - 1; // Previous year

					} else if ( $month_now <= 6 ) {

						$dates['m_start']   = 1;
						$dates['m_end']     = 3;
						$dates['year']      = date( 'Y', $current_time );

					} else if ( $month_now <= 9 ) {

						$dates['m_start']   = 4;
						$dates['m_end']     = 6;
						$dates['year']      = date( 'Y', $current_time );

					} else {

						$dates['m_start']   = 7;
						$dates['m_end']     = 9;
						$dates['year']      = date( 'Y', $current_time );

					}
				break;

				case 'this_year' :
					$dates['day']       = null;
					$dates['m_start']   = null;
					$dates['m_end']     = null;
					$dates['year']      = date( 'Y', $current_time );
				break;

				case 'last_year' :
					$dates['day']       = null;
					$dates['m_start']   = null;
					$dates['m_end']     = null;
					$dates['year']      = date( 'Y', $current_time ) - 1;
				break;

			endswitch;
		}

		/**
		 * Returns the filters for the dates used to retreive earnings/sales
		 *
		 * @since 1.5.1
		 * @param object $dates The dates used for retreiving earnings/sales
		 */

		return apply_filters( 'spnl_api_stat_dates', $dates );
	}

	public function get_errors( $product = null ) {
		return  SPNL()->log->get_logs(0, 'sending',  1);;
	}


	private function log_request( $data = array() ) {
		if ( ! $this->log_requests )
			return;

		global $spnl_logs, $wp_query;

		$query = array(
			'spnl-api'     => $wp_query->query_vars['spnl-api'],
			'key'         => $wp_query->query_vars['key'],
			'token'       => $wp_query->query_vars['token'],
			'query'       => isset( $wp_query->query_vars['query'] )       ? $wp_query->query_vars['query']       : null,
			'type'        => isset( $wp_query->query_vars['type'] )        ? $wp_query->query_vars['type']        : null,
			'date'        => isset( $wp_query->query_vars['date'] )        ? $wp_query->query_vars['date']        : null,
			'startdate'   => isset( $wp_query->query_vars['startdate'] )   ? $wp_query->query_vars['startdate']   : null,
			'enddate'     => isset( $wp_query->query_vars['enddate'] )     ? $wp_query->query_vars['enddate']     : null,
			'id'          => isset( $wp_query->query_vars['id'] )          ? $wp_query->query_vars['id']          : null,
			'email'       => isset( $wp_query->query_vars['email'] )       ? $wp_query->query_vars['email']       : null,
		);

		$log_data = array(
			'log_type'     => 'api_request',
			'post_excerpt' => http_build_query( $query ),
			'post_content' => ! empty( $data['error'] ) ? $data['error'] : '',
		);

		$log_meta = array(
			'request_ip' => edd_get_ip(),
			'user'       => $this->user_id,
			'key'        => $wp_query->query_vars['key'],
			'token'      => $wp_query->query_vars['token']
		);

		$spnl_logs->insert_log( $log_data, $log_meta );
	}


	/**
	 * Retrieve the output data
	 *
	 * @access public
	 * @since 1.5.2
	 * @return array
	 */
	public function get_output() {
		return $this->data;
	}

	/**
	 * Output Query in either JSON/XML. The query data is outputted as JSON
	 * by default
	 *
	 * @author Daniel J Griffiths
	 * @since 1.5
	 * @global $wp_query
	 *
	 * @param int $status_code
	 */
	public function output( $status_code = 200 ) {
		global $wp_query;

		$format = $this->get_output_format();

		status_header( $status_code );

		do_action( 'spnl_api_output_before', $this->data, $this, $format );

		switch ( $format ) :

			case 'xml' :

				
				$xml = SendPress_Array2XML::createXML( 'edd', $this->data );
				echo $xml->saveXML();

				break;

			case 'json' :

				header( 'Content-Type: application/json' );
				if ( ! empty( $this->pretty_print ) )
					echo json_encode( $this->data, $this->pretty_print );
				else
					echo json_encode( $this->data );

				break;


			default :

				// Allow other formats to be added via extensions
				do_action( 'spnl_api_output_' . $format, $this->data, $this );

				break;

		endswitch;

		do_action( 'spnl_api_output_after', $this->data, $this, $format );

		spnl_die();
	}

	
	/**
	 * Generate the public key for a user
	 *
	 * @access private
	 * @since 1.9.9
	 * @param string $user_email
	 * @return string
	 */
	private function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
		return $public;
	}

	/**
	 * Generate the secret key for a user
	 *
	 * @access private
	 * @since 1.9.9
	 * @param int $user_id
	 * @return string
	 */
	private function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );
		return $secret;
	}

	/**
	 * Retrieve the user's token
	 *
	 * @access private
	 * @since 1.9.9
	 * @param int $user_id
	 * @return string
	 */
	private function get_token( $user_id = 0 ) {
		$user = get_userdata( $user_id );
		return hash( 'md5', $user->spnl_user_secret_key . $user->spnl_user_public_key );
	}

	
}