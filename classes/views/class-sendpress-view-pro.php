<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Pro extends SendPress_View{

	function module_save_api_key(){
        //$this->security_check();
        
        //error_log('save page api key');

        $license = $_POST['api_key'];
        if( false === SendPress_Pro_Manager::try_activate_key($license) ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'activate_key_notice'));
        }
    }

    function module_deactivate_api_key(){
        //$this->security_check();
        if( false === SendPress_Pro_Manager::try_deactivate_key() ){
            add_action('sendpress_notices', array('SendPress_Pro_Manager', 'deactivate_key_notice'));
        }
    }

    function remove_key(){
        //$this->security_check();
        SendPress_Option::set('api_key','');
    }

    function module_activate_sendpress_pro(){
        //$this->security_check();
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

    function wp_mail(){
        //$this->security_check();
        $e = SPNL()->validate->_int('enable');
       
        if($e == 1){
            update_option( 'sendpress_wp_mail' , 'true' ); 
        } else {
            update_option( 'sendpress_wp_mail' , 'false' ); 
        }
    }

    function module_deactivate_sendpress_pro(){
        //$this->security_check();
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

	
	function html(){
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
            <?php wp_nonce_field($this->_nonce_value); ?>
        </form>
    <h1 style="font-size:35px;">SendPress Pro
    <?php if(defined('SENDPRESS_PRO_VERSION')){ echo "<small>v" . SENDPRESS_PRO_VERSION."</small>"; } ?>
    </h1>
    <?php if( is_plugin_active('sendpress-pro/sendpress-pro.php') ){ ?>
        <p class="lead">Thanks for using <b>SendPress Pro</b>.</p>
    <?php } else { ?> 
        <p class="lead">Take your emails to the next level. <b>SendPress Pro</b> allows you to build an email marketing system tailored to your needs. All within WordPress with no need for an external system.</p>
    <?php } ?>
    </div>
                <?php


		$modules = apply_filters('sendpress_pro_modules', array('sendgrid', 'mailjet','customsmtp','amazonses','mandrill','elastic','mailgun','empty','empty') );
		echo '<div class="sendpress-addons">';
        $p = NEW SendPress_Module_Pro();
        $p->index(0);
        $p->render( $this );
        $i = 1;
       
        echo "<h2 class='clear' style='padding-left: 18px; padding-top: 30px; padding-bottom: 10px;'>Sending Options</h2>";
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

        ?>
        <!--
        <h2 class="clear" style="padding-left: 18px; padding-top: 30px; padding-bottom: 10px;">Addons</h2>
        <div class="sendpress-module  mod-first"><div class="inner-module">
        <h4>WordPress Emails</h4>
            <form method="post" id="post">
            <div class="description">
                Wrap WordPress system emails in a SendPress template and send them via your sending settings.
                <br><br>
                
            </div>
            <?php 
                $d = get_option( 'sendpress_wp_mail' , false ); 
                $sp = is_plugin_active('sendpress-pro/sendpress-pro.php');
            
                
            ?>


            <div class="inline-buttons">
            <?php 
            if($sp){ 
                if($d === false ) { ?>
                    <a class=" btn-success btn-activate btn" href="<?php echo SendPress_Admin::link('Pro',array('action'=>'wp-mail','enable'=>true) ); ?>">Activate</a>
                <?php } else { ?>
                    <a class="btn btn-default " href="<?php echo SendPress_Admin::link('Pro',array('action'=>'wp-mail','enable'=>false) ); ?>">Deactivate</a>&nbsp;<a class="btn btn-default module-deactivate-plugin" href="#">Settings</a>
                <?php } ?>
            <?php  } else { ?>
                <a class="btn disabled btn-default btn-activate" href="#">Activate</a>
            <?php } ?>
            </div>         
            
            </div>
        </div>
        -->
          <br class="clear">
        <br><br>

        <?php


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