<?php

// Prevent loading this file directly
if ( ! defined( 'SENDPRESS_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

// SendPress Required Class: SendPress_Ajax_Loader

class SendPress_Ajax_Loader {

	static $ajax_nonce = "love-me-some-sendpress-ajax-2012";
	static $priv_ajax_nonce = "love-me-some-sendpress-ajax-2012";

	static function &init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new SendPress_Ajax_Loader;
			$instance->add_hooks();
		}

		return $instance;
	}

	function add_hooks() {
		// register the ajax process function with wordpress

		add_action( "wp_ajax_sendpress_save_list", array( &$this, 'save_list' ) );
		add_action( "wp_ajax_sendpress_subscribe_to_list", array( &$this, 'subscribe_to_list' ) );
		add_action( 'wp_ajax_sendpress-autocron', array( &$this, 'autocron' ) );
		add_action( 'wp_ajax_sendpress-sendbatch', array( &$this, 'send_batch' ) );
		add_action( 'wp_ajax_sendpress-queuebatch', array( &$this, 'queue_batch' ) );
		add_action( 'wp_ajax_sendpress-stopcron', array( &$this, 'cron_stop' ) );
		add_action( 'wp_ajax_sendpress-sendcount', array( &$this, 'sendcount' ) );
		add_action( 'wp_ajax_sendpress-queuecount', array( &$this, 'queue_count' ) );
		add_action( 'sendpress_admin_scripts', array( &$this, 'admin_scripts' ) );
		add_action( 'wp_ajax_sendpress-findpost', array( &$this, 'find_post' ) );
		add_action( 'wp_ajax_sendpress-list-subscription', array( &$this, 'list_subscription' ) );
		add_action( "wp_ajax_sendpress-synclist", array( &$this, 'sync_list' ) );
		add_action( 'wp_ajax_sendpress-sendcron', array( &$this, 'sendcron' ) );

		//add_action( "wp_ajax_nopriv_sendpress_save_list", array( &$this, 'save_list' ) );
		add_action( "wp_ajax_nopriv_sendpress_subscribe_to_list", array( &$this, 'subscribe_to_list' ) );
		add_action( 'wp_ajax_nopriv_sendpress-list-subscription', array( &$this, 'list_subscription' ) );

	}

	function admin_scripts() {

	}

	function verify_ajax_call() {
		$nonce = SPNL()->validate->_string('spnonce');
		if ( ! wp_verify_nonce( $nonce, SendPress_Ajax_Loader::$priv_ajax_nonce ) ) {
			die ( 'Busted!' );
		}
	}

	function public_verify_ajax_call() {
		$nonce = SPNL()->validate->_string('spnonce');
		if ( ! wp_verify_nonce( $nonce, SendPress_Ajax_Loader::$ajax_nonce ) ) {
			die ( 'Busted!' );
		}
	}

	function list_subscription() {
		$this->public_verify_ajax_call();
		$s      = NEW SendPress;
		$lid    = SPNL()->validate->_int('lid');
		$sid    = SPNL()->validate->_int('sid');
		$status = SPNL()->validate->_int('status');
		echo json_encode( SendPress_Data::update_subscriber_status( $lid, $sid, $status ) );
		die();
	}

	function more_excerpt( $value ){
		$t = SendPress_Option::get( 'excerpt_more', false );
		if($t != false){
			return $t;
		}
		return $value;
	}


	function find_post() {
		$this->verify_ajax_call();
		$q = SPNL()->validate->_string('query');

		$the_query = new WP_Query( 's=' . $q );
		//$response = array('empty','test');
		$d = new stdClass();

		$d->query       = $q;
		$d->suggestions = array();
		$d->data        = array();
		// The Loop
		global $post;
		add_filter( 'excerpt_more', array( $this , 'more_excerpt') );
		
		while ( $the_query->have_posts() ) : $the_query->the_post();

			$t = get_the_title();
			$content          = get_the_content();
			$content          = apply_filters( 'the_content', $content );
			$content          = str_replace( ']]>', ']]&gt;', $content );
			$dx        = array(
				"content" => $content,
				"excerpt" => get_the_excerpt(),
				"url"     => get_permalink(),
				"title" => $t
			);
			$d->suggestions[] = array(
				'value' => $t,
				'data' =>$dx
				);

		endwhile;
		remove_filter( 'excerpt_more', array( $this , 'more_excerpt') );
		// Reset Post Data
		wp_reset_postdata();


		// Serialize the response back as JSON
		echo json_encode( $d );
		die();
	}

	function save_list() {
		$this->verify_ajax_call();
		global $wpdb;

		// Create the response array
		$response = array(
			'success' => false
		);
		$listid = SPNL()->validate->_int('id');
		if ($listid > 0) {
			// get the credit card details submitted by the form
		
			$name   =  sanitize_text_field( SPNL()->validate->_string('name') );
			$public = (SPNL()->validate->_string('public') === '1' ) ? 1 : 0;

			$list = SendPress_Data::update_list( $listid, array( 'name' => $name, 'public' => $public ) );

			if ( false !== $list ) {
				$response['success'] = true;
			} else {
				$response['error'] = $list;
			}

		}
		// Add additional processing here
		if ( $response['success'] ) {
			// Succeess
		} else {
			// Failed
		}

		// Serialize the response back as JSON
		echo json_encode( $response );
		die();
	}

	function subscribe_to_list() {
		//$this->verify_ajax_call();
		global $wpdb;

		// Create the response array
		$response = array(
			'success' => false
		);

		if ( $_POST ) {
			// get the credit card details submitted by the form
			$data = SPNL()->validate;
			$first  = $data->_string('first');
			if($first == null){
				$first = '';
			}
			$last   = $data->_string('last');
			if($last == null){
				$last = '';
			}
			$phone  = $data->_string('phonenumber');
			$salutation = $data->_string('salutation');
			$email  = $data->_string('email');
			$listid = $data->_string('listid');
			$formid = $data->_int('formid');
			$custom = array();
			$post_notifications = $data->_string('post_notifications');
			if( $post_notifications ){
				$custom['post_notifications'] = $post_notifications;
			}

			$custom_field_list = SendPress_Data::get_custom_fields_new();
			foreach ($custom_field_list as $key => $value) {
				$custom_field_key = $value['custom_field_key'];
				$customfieldvalue = $data->_string($custom_field_key);
						if (strlen($customfieldvalue) > 0) {
							$custom[$custom_field_key] = $customfieldvalue;
						}
			}

			$success = SendPress_Data::subscribe_user( $listid, $email, $first, $last, 2, $custom, $phone, $salutation );

			if ( false !== $success ) {
				$response['success'] = true;
				$response['exists']  = $success['already'];
			} else {
				$response['error'] = __( 'User was not subscribed to the list.', 'sendpress' );
			}

		}
		// Add additional processing here
		if ( $response['success'] ) {
			// Succeess
		} else {
			// Failed
		}

		// Serialize the response back as JSON
		echo json_encode( $response );
		die();
	}

	function autocron() {
		$this->verify_ajax_call();
		$enable = SPNL()->validate->_bool('enable');
		if ( $enable !== false ) {
			SendPress_Option::set( 'autocron', 'yes' );
			SendPress_Option::set( 'allow_tracking', 'yes' );
			//SendPress_Cron::use_iron_cron();
			/*
			$email = get_option( 'admin_email' );

			$url = "http://api.sendpress.com/senddiscountcode/" . md5( $_SERVER['SERVER_NAME'] . "|" . $email ) . "/" . $email;

			wp_remote_get( $url );
			*/

		} else {
			SendPress_Option::set( 'autocron', 'no' );
		}
		SendPress::add_cron();
		exit();
	}

	function cron_stop() {
		$this->verify_ajax_call();
		// Create the response array
		$response = array(
			'success' => false
		);

		$upload_dir = wp_upload_dir();
		$filename   = $upload_dir['basedir'] . '/sendpress.pause';
		$Content    = "Stop the cron form running\r\n";
		$handle     = fopen( $filename, 'w' );
		fwrite( $handle, $Content );
		fclose( $handle );

		if ( file_exists( $filename ) ) {
			$response['success'] = true;
		}
		echo json_encode( $response );
		exit;
	}

	function sendcount() {
		$this->verify_ajax_call();
		// Create the response arrayecho SendPress_Data::emails_active_in_queue();
		// 
		$count = SendPress_Data::emails_active_in_queue(); //emails_allowed_to_send();
		$response = array(
			'total' => $count
		);
		echo json_encode( $response );
		exit();
	}


	function sendcron() {
		$this->verify_ajax_call();
		// Create the response arrayecho SendPress_Data::emails_active_in_queue();
		$response = SendPress_Cron::run_cron_functions(true); //emails_allowed_to_send();
		echo json_encode( $response );
		exit();
	}


	function queue_count() {
		$this->verify_ajax_call();
		// Create the response array
		// 
		$count = SendPress_Data::emails_in_queue();
		$active = $count;
		 $stuck = SendPress_Data::emails_stuck_in_queue();
		$df = SendPress_Option::get( 'autocron' );
		if($df != 'yes'){
			$active = 0;
		}
		if($count == $stuck){
			$active = 0;
		}

		$url = str_replace('/', ':r:',site_url());
		$response = array(
			'total' => $count,
			'url' => $url,
			'try' => $count > 0 ? ceil($count/25) : 0,
			'active' => $active,
			'stuck' => $stuck,
			'auto' => $df,
			'version' => SENDPRESS_VERSION
		);
		echo json_encode( $response );
		exit();

	}


	function send_batch() {
		$this->verify_ajax_call();
		$count = SendPress_Manager::send_single_from_queue();
		echo json_encode( $count );
		exit();
	}

	function sync_list() {
		$this->verify_ajax_call();
		$listid = SPNL()->validate->_int('listid');
		$offset = SPNL()->validate->_int('offset');
		$role   = get_post_meta( $listid, 'sync_role', true );
		$load   = SendPress_Option::get( 'sync-per-call', 250 );
		
		$custom = apply_filters('spnl-role-sync-get-user-args', false , $listid , $offset_for_this_run , $number_to_sync_at_once , $role );
		if($custom !== false){
			$blogusers = get_users( $custom );
		} else {
			if ( $role != 'meta' ) {
				$blogusers = get_users( 'role=' . $role . '&number=' . $load . '&offset=' . $offset );
			} else {
				$meta_key     = get_post_meta( $listid, 'meta-key', true );
				$meta_value   = get_post_meta( $listid, 'meta-value', true );
				$meta_compare = get_post_meta( $listid, 'meta-compare', true );
				$blogusers    = get_users( 'meta_key=' . $meta_key . '&meta_value=' . $meta_value . '&meta_compare=' . $meta_compare . '&number=' . $load . '&offset=' . $offset );
			}
		}

		$email_list = array();
		foreach ( $blogusers as $user ) {
			SendPress_Data::update_subscriber_by_wp_user( $user->ID, array( 'email'     => $user->user_email,
			                                                                'firstname' => $user->first_name,
			                                                                'lastname'  => $user->last_name
			) );
			$email_list[] = $user->user_email;
		}
		SendPress_Data::sync_emails_to_list( $listid, $email_list );

		echo json_encode( array( "count" => count( $blogusers ), "role" => $role, "offset" => $offset ) );
		exit();
	}


	function queue_batch() {
		$this->verify_ajax_call();
		$reportid = SPNL()->validate->_int('reportid');
		$lists    = get_post_meta( $reportid, '_send_lists', true );
		$time     = get_post_meta( $reportid, '_send_time', true );
		$list     = explode( ",", $lists );
		$last     = get_post_meta( $reportid, '_send_last', true );

		$fromname = SendPress_Option::get('fromname');
		$fromemail = SendPress_Option::get('fromemail');

		$cfromname = get_post_meta( $reportid, 'custom-from-name', true );
		$cfromemail = get_post_meta( $reportid, 'custom-from-email', true );

		if(strlen($cfromname) > 0){
			$fromname = $cfromname;
		}

		if(strlen($cfromemail) > 0){
			$fromemail = $cfromemail;
		}

		//$count_last = get_post_meta($reportid, '_send_last_count', true);
		if ( $last == false ) {
			$last       = 0;
			$count_last = 0;
		}

		$x = SendPress_Data::get_active_subscribers_lists_with_id( $list, $last );

		foreach ( $x as $email ) {

			$go = array(
				'from_name'    => $fromname,
				'from_email'   => $fromemail,
				'to_email'     => $email->email,
				'emailID'      => $reportid,
				'subscriberID' => $email->subscriberID,
				//'to_name' => $email->fistname .' '. $email->lastname,
				'subject'      => '',
				'date_sent'    => $time,
				'listID'       => $email->listid
			);

			SendPress_Data::add_email_to_queue( $go );
		}

		//if()
		if ( count( $x ) == intval( SendPress_Option::get( 'queue-per-call', 1000 ) ) ) {

			$w = end( $x );
			update_post_meta( $reportid, '_send_last', $w->subscriberID );

			echo json_encode( array( "lastid" => $w->subscriberID, "count" => count( $x ) ) );

			exit();
		}
		update_post_meta( $reportid, '_send_last', - 1 );
		echo json_encode( array( "lastid" => 0, "count" => count( $x ) ) );
		exit();


	}

}

