<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Pro extends SendPress_View{

	
	 function module_save_api_key(){
        $license = $_POST['api_key'];

        $api_params = array( 
            'edd_action'=> 'activate_license', 
            'license'   => $license, 
            'item_name' => urlencode( SENDPRESS_PRO_NAME ) // the name of our product in EDD
        );

        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        SendPress_Option::set('api_key',$license);
        if($license_data){
        	//SendPress_Option::set('api_key_state', $license_data->license);
            set_transient( 'sendpress_key_state', $license_data->license, SENDPRESS_TRANSIENT_LENGTH );
    	}
    }

    function module_deactivate_api_key(){

        $license = SendPress_Option::get('api_key');
        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'deactivate_license', 
            'license'   => $license, 
            'item_name' => urlencode( SENDPRESS_PRO_NAME ) // the name of our product in EDD
        );
        
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
        if( $license_data->license === 'deactivated' || $license_data->license === 'failed' ){
            SendPress_Option::set('api_key','');
            //SendPress_Option::set('api_key_state',$license_data->license);
            delete_transient( 'sendpress_key_state' );
        }

    }

    function module_check_api_key(){

        $license = SendPress_Option::get('api_key');
        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'get_version', 
            'license'   => $license, 
            'name' => urlencode( SENDPRESS_PRO_NAME ) // the name of our product in EDD
        );
        
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SENDPRESS_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

        // $license_data->license will be either "deactivated" or "failed"
         echo '<pre>';
        print_r($license_data);
        echo '</pre>';

    }


    function module_activate_sendpress_pro(){
        $path = $_POST['plugin_path'];
        $pro_options = SendPress_Option::get('pro_plugins');

        if( !preg_match('/sendpress-pro.php/i',$path) ){
            if( preg_match('/sendpress-pro/i',$path) ){
                //make sure the plugin loads from sendpress pro
                $pro_options[$path] = true;
                SendPress_Option::set('pro_plugins',$pro_options); 
            }
        }else{
            activate_plugin($path);
        }

    }

    function module_deactivate_sendpress_pro(){
        $path = $_POST['plugin_path'];
        $pro_options = SendPress_Option::get('pro_plugins');

        if( !preg_match('/sendpress-pro.php/i',$path) ){
            if( preg_match('/sendpress-pro/i',$path) ){
                //make sure the plugin loads from sendpress pro
                $pro_options[$path] = false;

                SendPress_Option::set('pro_plugins',$pro_options); 
                
            }
        }else{
            deactivate_plugins($path);
        }

    }

	
	function html($sp){
		//SendPress_Option::set('pro_plugins','');
		$modules = array('pro','reports', 'spam_test', 'empty');
		echo '<div class="sendpress-addons">';
		foreach ($modules as $mod) {
			$mod_class = $this->get_module_class($mod);
			if($mod_class){
				$mod_class = NEW $mod_class;
				$mod_class->render( $this );
			}
			
		}
		echo '</div>';

	}

	function get_module_class( $module = false ){
		if($module !== false){
			$module = str_replace('-',' ',$module);
			$module  = ucwords( $module );
			$module = str_replace(' ','_',$module);
			$class = "SendPress_Module_{$module}";

			if ( class_exists( $class ) )
				return $class;
		}
		return false;
	}

}
SendPress_View_Pro::cap('sendpress_addons');