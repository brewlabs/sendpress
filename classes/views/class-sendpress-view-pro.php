<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Pro extends SendPress_View{

	function module_save_api_key(){
        $license = $_POST['api_key'];
        if( false === SendPress_Pro_Manager::try_activate_key($license) ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'activate_key_notice'));
        }
    }

    function module_deactivate_api_key(){
        if( false === SendPress_Pro_Manager::try_deactivate_key() ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'deactivate_key_notice'));
        }
    }

    function remove_key(){
        SendPress_Option::set('api_key','');
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
        SendPress_Tracking::event('Pro Tab');
        ?>
        <div class="pro-header" >
            <form method="post" id="post" style="float:right;">
            <?php 
            $sppro = new SendPress_Module_Pro();
            $sppro->buttons('sendpress-pro/sendpress-pro.php'); ?> 
            <input type="hidden" name="plugin_path" value="sendpress-pro/sendpress-pro.php" />
            <input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
            <?php wp_nonce_field($sp->_nonce_value); ?>
        </form>
    <h1 style="font-size:35px;">SendPress Pro</h1>
    <?php if( is_plugin_active('sendpress-pro/sendpress-pro.php') ){ ?>
        <p class="lead">Thanks for using <b>SendPress Pro</b>.</p>
    <?php } else { ?> 
        <p class="lead">Take your emails to the next level. <b>SendPress Pro</b> allows you to build an email marketing system tailored to your needs. All within WordPress with no need for an external system.</p>
    <?php } ?>
    </div>
                <?php


		$modules = apply_filters('sendpress_pro_modules', array('pro','reports', 'spam_test', 'sendgrid', 'mailjet','customsmtp','amazonses','bounce','autoresponders','mandrill') );
		echo '<div class="sendpress-addons">';
        $i = 0;
		foreach ($modules as $mod) {
			$mod_class = $this->get_module_class($mod);
			if($mod_class){
				$mod_class = NEW $mod_class;
                $mod_class->index($i);
				$mod_class->render( $this );
                $i++;
			}
			
		}

		echo '<br class="clear"><br></div>';

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
SendPress_Admin::add_cap('Pro','sendpress_addons');