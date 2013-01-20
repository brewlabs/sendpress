<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Pro extends SendPress_View{

	function module_save_api_key(){
        $license = $_POST['api_key'];
        if( false === SendPress_Pro_Manager::activate_key($license) ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'activate_key_notice'));
        }
    }

    function module_deactivate_api_key(){
        if( false === SendPress_Pro_Manager::deactivate_key() ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'deactivate_key_notice'));
        }
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