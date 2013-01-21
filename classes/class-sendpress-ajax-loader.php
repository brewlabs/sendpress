<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

// SendPress Required Class: SendPress_Ajax_Loader

class SendPress_Ajax_Loader{

	static $ajax_nonce = "love-me-some-sendpress-ajax-2012";
	
	function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Ajax_Loader;
			$instance->add_hooks();
		}

		return $instance;
	}

	


	function add_hooks(){
		// register the ajax process function with wordpress
		add_action("wp_ajax_sendpress_save_list", array(&$this,'save_list') );
		add_action("wp_ajax_nopriv_sendpress_save_list", array(&$this,'save_list') );

		add_action("wp_ajax_sendpress_subscribe_to_list", array(&$this,'subscribe_to_list') );
		add_action("wp_ajax_nopriv_sendpress_subscribe_to_list", array(&$this,'subscribe_to_list') );

		add_action('wp_ajax_sendpress-sendbatch', array(&$this, 'send_batch'));
		add_action('wp_ajax_sendpress-stopcron', array(&$this, 'cron_stop'));
		add_action('wp_ajax_sendpress-sendcount', array(&$this, 'sendcount'));
		add_action('sendpress_admin_scripts',array(&$this,'admin_scripts'));
		add_action('wp_ajax_sendpress-findpost', array(&$this, 'find_post'));
		add_action('wp_ajax_sendpress-list-subscription', array(&$this,'list_subscription'));
		add_action('wp_ajax_nopriv_sendpress-list-subscription', array(&$this,'list_subscription'));
	}

	function admin_scripts(){
		wp_localize_script( 'sendpress-admin-js', 'spvars', array(
	    // URL to wp-admin/admin-ajax.php to process the request
	    'ajaxurl'          => admin_url( 'admin-ajax.php' ),
	 
	    // generate a nonce with a unique ID "myajax-post-comment-nonce"
	    // so that you can check it later when an AJAX request is sent
	    'sendpressnonce' => wp_create_nonce( SendPress_Ajax_Loader::$ajax_nonce ),
	    )
		);
	}

	function verify_ajax_call(){
		$nonce = isset($_POST['spnonce']) ? $_POST['spnonce'] :  $_GET['spnonce'] ;
    	if ( ! wp_verify_nonce( $nonce, SendPress_Ajax_Loader::$ajax_nonce ) ){
        	die ( 'Busted!');
       	}
	}

	function list_subscription(){
		$this->verify_ajax_call();
		$s = NEW SendPress;
		$lid = $_POST['lid'];
		$sid = $_POST['sid'];
		$status = $_POST['status'];
		echo json_encode( $s->updateStatus($lid, $sid , $status) );
		die();
	}


	function find_post(){
		$this->verify_ajax_call();
		$q = $_GET['query'];

		$the_query = new WP_Query( 's='. $q );
		//$response = array('empty','test');
		$d = new stdClass();
		
		$d->query = $q;
		$d->suggestions = array();
		$d->data = array();
		// The Loop
		global $post;
		while ( $the_query->have_posts() ) : $the_query->the_post();
			
			$d->suggestions[]= get_the_title();
			$content = get_the_content();
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$d->data[] = array(
				"content" => $content,
				"excerpt" => get_the_excerpt(),
				"url" => get_permalink()
			);
			
		endwhile;

		// Reset Post Data
		wp_reset_postdata();

		
		
		



		// Serialize the response back as JSON
		echo json_encode($d);
		die();
	}

	function save_list(){
		global $wpdb;

		// Create the response array
		$response = array(
			'success' => false
		);

		if($_POST) {
			$s = NEW SendPress;

			// get the credit card details submitted by the form
			$listid = $_POST['id'];
			$name = $_POST['name'];
			$public = ( $_POST['public'] === '1' ) ? 1 : 0;

			$list = $s->updateList($listid, array( 'name'=>$name, 'public'=>$public ) );

			if( false !== $list ){
				$response['success'] = true;
			}else{
				$response['error'] = $list;
			}
			
		}
		// Add additional processing here
		if($response['success']) {
			// Succeess
		} else {
			// Failed
		}
		
		// Serialize the response back as JSON
		echo json_encode($response);
		die();
	}

	function subscribe_to_list(){
		global $wpdb;

		// Create the response array
		$response = array(
			'success' => false
		);

		if($_POST) {
			$s = NEW SendPress;

			// get the credit card details submitted by the form
			$first = isset($_POST['first']) ? $_POST['first'] : '';
			$last = isset($_POST['last']) ? $_POST['last'] : '';
			$email = isset($_POST['email']) ? $_POST['email'] : '';
			$listid = isset($_POST['listid']) ? $_POST['listid'] : '';

			$success = $s->subscribe_user($listid,$email,$first,$last);

			if( false !== $success ){
				$response['success'] = true;
			}else{
				$response['error'] = __('User was not subscribed to the list.','sendpress');
			}
			
		}
		// Add additional processing here
		if($response['success']) {
			// Succeess
		} else {
			// Failed
		}
		
		// Serialize the response back as JSON
		echo json_encode($response);
		die();
	}

	function cron_stop(){
		$this->verify_ajax_call();
		// Create the response array
		$response = array(
			'success' => false
		);
		
    	$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'].'/sendpress.pause';
		$Content = "Stop the cron form running\r\n";
		$handle = fopen($filename, 'w');
		fwrite($handle, $Content);
		fclose($handle);
		
		if(file_exists($filename)){
			$response['success'] = true;
		} 
	    echo json_encode($response);
 		exit;
	}

	function sendcount(){
		error_log('asdf');
		$this->verify_ajax_call();
		// Create the response array
		$sp = new SendPress;
		$response = array(
			'total' => $sp->countQueue()
		);
		echo json_encode($response);
		exit();
	}

	function send_batch(){
		$sp = new SendPress;
		$count = $sp->send_single_from_queue();
		
		echo json_encode($count);
		exit();
	}

}

