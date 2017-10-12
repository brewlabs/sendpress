<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SendPress_Api_Loader {

	public function __construct() {
        // WP REST API.
        $this->rest_api_init();
    }

	private function rest_api_init() {
    // REST API was included starting WordPress 4.4.
	    if ( ! class_exists( 'WP_REST_Server' ) ) {
	      return;
	    }

	   
	    // Init REST API routes.
	    add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
	  }


	  public function register_rest_routes() {
        // Register settings to the REST API.
        //$this->register_wp_admin_settings();

        $controllers = array(
            // v1 controllers.
            'SendPress_REST_AutoCron_V1_Controller',
            'SendPress_REST_Sending_V1_Controller'
          
        );

        foreach ( $controllers as $controller ) {
            $this->$controller = new $controller();
            $this->$controller->register_routes();
        }
    }
	
}