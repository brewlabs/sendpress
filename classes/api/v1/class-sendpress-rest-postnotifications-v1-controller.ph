<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
WP_REST_Server::READABLE = ‘GET’
WP_REST_Server::EDITABLE = ‘POST, PUT, PATCH’
WP_REST_Server::DELETABLE = ‘DELETE’
WP_REST_Server::ALLMETHODS = ‘GET, POST, PUT, PATCH, DELETE’
*/

class SendPress_REST_Postnotifications_V1_Controller extends SendPress_REST_Base_v1 {
	
	protected $controller = "/postnotifications/"; 

	function register_routes(){
		
    	register_rest_route( parent::get_api_base(), $this->controller . 'queue' , array(
    		'methods' => WP_REST_Server::READABLE,
    		'callback' => array($this,'queue_messages')
    	));
	}

	function queue_messages($request){
		//get report id from request
		$report_id = $request->report_id;

		//for testing
		$report_id = 1234;

		//get report
		






		
	}

}