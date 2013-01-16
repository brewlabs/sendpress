<?php
// SendPress Required Class: SendPress_Pro_Manager
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_Pro_Manager
*
* @uses     
*
* @package  SendPress
* @author   Jared Harbour
* @license  See SENPRESS
* @since 	0.8.8.5     
*/
class SendPress_Pro_Manager {

	function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Pro_Manager;
			$instance->_init();
		}

		return $instance;
	}

	function _init(){
		add_filter('plugins_api_result', array( $this, 'get_pro_details' ),10,3);
		add_action( 'admin_head', array( $this, 'check_api_key' ) );
	}

	function check_api_key(){
		if( class_exists('SendPress_Option') ){
			$key = SendPress_Option::get('api_key');
			if ( false === ( $state = get_transient( 'sendpress_key_state' ) ) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    $sendpress_key_state = $this->check_key($key);
			    set_transient( 'sendpress_key_state', $sendpress_key_state['state'], $sendpress_key_state['transient_time'] );
			    $state = $sendpress_key_state['state'];
			}
			
			if( $state !== 'valid' && !empty($key) ){
				add_action('sendpress_notices', array($this, 'key_notice'));
				SendPress_Option::set('api_key','');
			}
		}

	}

	function check_key($key){

        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'check_license', 
            'license'   => $key, 
            'item_name' => urlencode( SENDPRESS_PRO_NAME ) // the name of our product in EDD
        );
        
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        // if response didn't come back, lets set the transient and try again in a day.
        if ( is_wp_error( $response ) )
            return array('state'=>'valid', 'transient_time'=>DAY_IN_SECONDS);

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
        return array('state'=>$license_data->license, 'transient_time'=>SENDPRESS_TRANSIENT_LENGTH);
	}

	function key_notice(){
		echo '<div class="alert alert-error">';
			echo "<b>";
			_e('Alert','sendpress');
			echo "</b>&nbsp;-&nbsp;";
			printf(__('Your API key is either invalid or in use on another site. Need help? Visit <a href="http://sendpress.com/support/">SendPress Support</a>','sendpress'), get_transient( 'sendpress_key_state' ) );
	    echo '</div>';
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

