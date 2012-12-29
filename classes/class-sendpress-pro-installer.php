<?php
// SendPress Required Class: SendPress_Pro_Installer
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_Pro_Installer
*
* @uses     
*
* @package  SendPress
* @author   Jared Harbour
* @license  See SENPRESS
* @since 	0.8.8.5     
*/
class SendPress_Pro_Installer {

	function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Pro_Installer;
			$instance->_init();
		}

		return $instance;
	}

	function _init(){
		add_filter('plugins_api_result', array( $this, 'get_pro_details' ),10,3);
	}
	
	function get_pro_details( $res, $action, $args ){
		if( $action === 'plugin_information' && $args->slug === 'sendpress-pro' ){

			if( class_exists('SendPress_Option') ){
				$license = SendPress_Option::get('api_key');

				$api_params = array( 
		            'edd_action'=> 'get_version', 
		            'license'   => $license, 
		            'name' => urlencode( SENDPRESS_PRO_NAME ) // the name of our product in EDD
		        );
		        
		        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		        if ( is_wp_error( $response ) )
		            return false;

		        $data = json_decode( wp_remote_retrieve_body( $response ) );

		        $obj = new stdClass();
				$obj->name = $data->name;
				$obj->slug = $args->slug;
				$obj->version = $data->version;
				$obj->download_link = $data->package;

				$res = $obj;

			}
			
		}

		return $res;
	}

}

