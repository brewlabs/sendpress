<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*
WP_REST_Server::READABLE = ‘GET’
WP_REST_Server::EDITABLE = ‘POST, PUT, PATCH’
WP_REST_Server::DELETABLE = ‘DELETE’
WP_REST_Server::ALLMETHODS = ‘GET, POST, PUT, PATCH, DELETE’
*/


abstract class SendPress_REST_Base_v1 extends WP_REST_Controller {

	var $version = 1;
    var $namespace = 'sendpress/v';
    var $base = 'api';

    function get_api_base(){
    	return $this->namespace . $this->version . '/' . $this->base;
    }
}