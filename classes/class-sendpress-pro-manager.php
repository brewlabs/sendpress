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

	static function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Pro_Manager;
			$instance->_init();
		}

		return $instance;
	}

	function _init(){
		//add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins_filter' ) );
		add_filter('plugins_api', array( $this, 'plugins_api_filter' ),10,3);
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
			
		
		//add_filter('plugins_api_result', array( $this, 'get_pro_details' ),10,3);
		if( defined('SENDPRESS_PRO_VERSION') ){	
			add_action( 'admin_head', array( $this, 'check_api_key' ) );
		}
	}

	function pre_set_site_transient_update_plugins_filter( $_transient_data ) {


		if( empty( $_transient_data ) ) return $_transient_data;

		if( defined('SENDPRESS_PRO_VERSION') ){
			$to_send = array( 'slug' => 'sendpress-pro' );

			$api_response = $this->api_request( 'plugin_latest_version', $to_send );

			if( false !== $api_response && is_object( $api_response ) && isset( $api_response->new_version ) ) {
				if( version_compare( SENDPRESS_PRO_VERSION, $api_response->new_version, '<' ) )
					$_transient_data->response['sendpress-pro/sendpress-pro.php'] = $api_response;
			}
		}
		return $_transient_data;
	}

	/**
	 * Disable SSL verification in order to prevent download update failures
	 *
	 * @param array $args
	 * @param string $url
	 * @return object $array
	 */
	function http_request_args( $args, $url ) {
		// If it is an https request and we are performing a package download, disable ssl verification
		if( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = false;
		}
		return $args;
	}


	function plugins_api_filter( $_data, $_action = '', $_args = null ) {
		if ( ( $_action != 'plugin_information' ) || !isset( $_args->slug ) || ( $_args->slug != 'sendpress-pro' ) ) return $_data;

		$to_send = array( 'slug' => 'sendpress-pro' );

		$api_response = $this->api_request( 'plugin_information', $to_send );
		if ( false !== $api_response ) $_data = $api_response;

		return $_data;
	}


	/**
     * get_pro_state
     * 
     *
     * @access public
     *
     * @return SENDPRESS_PRO_{state} 
     */
	static function get_pro_state(){

		if ( false === ( $state = get_transient( 'sendpress_key_state' ) ) ) {
		    // It wasn't there, so regenerate the data and save the transient
		    $sendpress_key_state = SendPress_Pro_Manager::try_check_key();
			SendPress_Pro_Manager::set_pro_state($sendpress_key_state);
			//set_transient( 'sendpress_key_state', $sendpress_key_state['state'], $sendpress_key_state['transient_time'] );
			$state = $sendpress_key_state['state'];
		    
		}
		return $state;
	}

	static function set_pro_state($data){
		if( is_array($data) ){
			set_transient( 'sendpress_key_state', $data['state'], $data['transient_time'] );
		}else{
			delete_transient( 'sendpress_key_state' );
		}
	}

	static function check_api_key(){
		if( class_exists('SendPress_Option') ){
			$key = SendPress_Option::get('api_key');
			$state = SendPress_Pro_Manager::get_pro_state();
			
			if( $state !== 'valid' && !empty($key) ){
				//add_action('sendpress_notices', array('SendPress_Pro_Manager', 'key_notice'));
				//SendPress_Option::set('api_key','');
			}
		}
	}

	static function activate_key($key,$id){

		//error_log("testing........");

		$api_params = array( 
            'edd_action'=> 'activate_license', 
            'license'   => $key, 
            'item_id' => $id // the name of our product in EDD
        );

		//SendPress_Error::log(SENDPRESS_STORE_URL);

        // Call the custom API.
        $response = wp_remote_post( SENDPRESS_STORE_URL , array( 'body'=>$api_params ,'timeout' => 15, 'sslverify' => false ) );

        //SendPress_Error::log($response);

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return true; //return true, site might be down

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        //SendPress_Error::log($license_data);

        if($license_data){
        	if( $license_data->license !== 'invalid' ){
        		SendPress_Option::set('api_key',$key);
        		SendPress_Option::set('api_product', $id);
        		SendPress_Pro_Manager::set_pro_state(array('state'=>$license_data->license, 'transient_time'=>SENDPRESS_TRANSIENT_LENGTH));
        		
            	return true;
        	}
        	return false;
    	}
    	return false;
	}

	static function try_activate_key($key){

		//error_log("try to activate key");

		//$key = SendPress_Option::get('api_key');
		global $pro_names;
		$valid = false;
		foreach($pro_names as $id){
			if(SendPress_Pro_Manager::activate_key($key,$id)){
				$valid = true;
				break;
			}
		}

		return $valid;

	}

	static function deactivate_key($key, $id){

        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'deactivate_license', 
            'license'   => $key, 
            'item_id' => $id // the name of our product in EDD
        );
        // Call the custom API.
        $response = wp_remote_post( SENDPRESS_STORE_URL , array( 'body'=>$api_params ,'timeout' => 15, 'sslverify' => false ) );
        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
        // $license_data->license will be either "deactivated" or "failed"
        if( $license_data->license === SENDPRESS_PRO_DEACTIVATED || $license_data->license === SENDPRESS_PRO_FAILED ){
            //SendPress_Option::set('api_key','');
            SendPress_Pro_Manager::set_pro_state(false); //this will delete the transient
            return true;
        }
        return false;
	}

	static function try_deactivate_key(){
		$key = SendPress_Option::get('api_key');
		$id = SendPress_Option::get('api_product');
		SendPress_Pro_Manager::deactivate_key($key,$id);
	}

	static function check_key($key,$id){

		if(empty($key)){
			return false;
		}

		$failed = false; 
        
        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'check_license', 
            'license'   => $key, 
            'item_id' => $id // the name of our product in EDD
        );
        
        // Call the custom API.
        $response = wp_remote_post( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        // if response didn't come back, lets set the transient and try again in a day.
        if ( is_wp_error( $response ) )
            return true;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        if($license_data->license === 'valid'){
     	   // $license_data->license will be either "deactivated" or "failed"
     	   return array('state'=>$license_data->license, 'transient_time'=>SENDPRESS_TRANSIENT_LENGTH,  'sp_state'=>'passed');
    	}
    	return $failed;
	}

	static function try_check_key(){

		$key = SendPress_Option::get('api_key');
		global $pro_names;

		$check = array('state'=>'valid', 'transient_time'=>86400, 'sp_state'=>'failed');
		foreach($pro_names as $name){
			$check = SendPress_Pro_Manager::check_key($key,$name);
			if( $check['sp_state'] === 'passed' ){
				return $check;
			}
		}

		return $check;

	}


	static function key_notice(){
		echo '<div class="alert alert-danger">';
			echo "<b>";
			_e('Alert','sendpress');
			echo "</b>&nbsp;-&nbsp;";
			printf(__('Your API key is either invalid or in use on another site. Need help? Visit login to <a href="http://sendpress.com/your-account/">SendPress Support</a>','sendpress'), get_transient( 'sendpress_key_state' ) );
	    echo '</div>';
	}

	function deactivate_key_notice(){
		echo '<div class="alert alert-danger">';
			printf(__('There was a problem deactivating your API key.  Try again in a few minutes or visit <a href="http://sendpress.com/your-account/">SendPress Support</a>','sendpress') );
	    echo '</div>';
	}

	function activate_key_notice(){
		echo '<div class="alert alert-danger">';
			printf(__('There was a problem activating your API key.  Try again in a few minutes or visit <a href="http://sendpress.com/your-account/">SendPress Support</a>','sendpress') );
	    echo '</div>';
	}
	


	/**
	 * Calls the API and, if successfull, returns the object delivered by the API.
	 *
	 * @uses get_bloginfo()
	 * @uses wp_remote_post()
	 * @uses is_wp_error()
	 *
	 * @param string $_action The requested action.
	 * @param array $_data Parameters for the API action.
	 * @return false||object
	 */
	private function api_request( $_action, $_data ) {

		global $wp_version;

		if( $_data['slug'] != 'sendpress-pro' )
			return;
		
		$license = SendPress_Option::get('api_key');
		$product = SendPress_Option::get('api_product','SendPress Pro');
				
		$api_params = array( 
			'edd_action' 	=> 'get_version', 
			'license' 		=> $license, 
			'item_id' 			=>  $product,
			'slug' 			=> 'sendpress-pro',
			'author'		=> 'SendPress'
		);
		$request = wp_remote_post( SENDPRESS_STORE_URL , array( 'timeout' => 15, 'ssverify' => false, 'body' => $api_params ) );
		if ( !is_wp_error( $request ) ):
			$request = json_decode( wp_remote_retrieve_body( $request ) );
			if( $request )
				$request->sections = maybe_unserialize( $request->sections );
			return $request;
		else:
			return false;
		endif;
	}

	

	function get_pro_details( $res, $action, $args ){
		
		if( $action === 'plugin_information'&& is_object($args) && $args->slug === 'sendpress-pro' ){

			if( class_exists('SendPress_Option') ){
				$license = SendPress_Option::get('api_key');
				$product = SendPress_Option::get('api_product','SendPress Pro');
				$api_params = array( 
		            'edd_action'=> 'get_version', 
		            'license'   => $license, 
		            'item_id' => $product  // the name of our product in EDD
		        );
		        
		        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		        if ( is_wp_error( $response ) )
		            return false;

		        $data = json_decode( wp_remote_retrieve_body( $response ) );
		        $obj = new stdClass();
				$obj->name = 'SendPress Pro';
				$obj->slug = $args->slug;
				$obj->version = $data->version;
				$obj->download_link = $data->package;
				$obj->sections = $data->sections;
			
				$res = $obj;

				

			}
			
		}

		return $res;
	}

}

