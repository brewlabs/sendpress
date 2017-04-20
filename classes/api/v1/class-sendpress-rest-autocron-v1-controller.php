<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
WP_REST_Server::READABLE = ‘GET’
WP_REST_Server::EDITABLE = ‘POST, PUT, PATCH’
WP_REST_Server::DELETABLE = ‘DELETE’
WP_REST_Server::ALLMETHODS = ‘GET, POST, PUT, PATCH, DELETE’
*/

class SendPress_REST_AutoCron_V1_Controller extends SendPress_REST_Base_v1{

	function register_routes(){

		register_rest_route( parent::get_api_base(), '/hello', array(
    		'methods' => 'GET',
    		'callback' => array($this,'hello_world')
    	));

	}

	function hello_world(){
		return "Suprise Motherfuckers!";
	}

}