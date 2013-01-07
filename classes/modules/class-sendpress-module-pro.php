<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Pro extends SendPress_Module{
	
	function html($sp){
		//for testing only
		//SendPress_Option::set('api_key','');
        //SendPress_Option::set('api_key_state','');

		$key_active = false;
		if( SendPress_Option::get('api_key_state') === 'valid' ){
			$key_active = true;
		}
		SendPress_Helper::log('API Key = '.SendPress_Option::get('api_key'));
		SendPress_Helper::log('API State = '.SendPress_Option::get('api_key_state'));
		//$key_active = true;
		
	?>
		<h4>SendPress Pro</h4>
		<form method="post" id="post">
			<div class="description">
				Get SendPress Pro for premium support, advanced reports, and much much more!
			</div>
			<?php $this->buttons('sendpress-pro/sendpress-pro.php');?>
			<input type="hidden" name="plugin_path" value="sendpress-pro/sendpress-pro.php" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

		<!-- <form method="post" id="post">
			
			<h5>Check API</h5>
			
				<a href="#" class="save-api-key btn-primary btn-small btn">Check API</a>
			
			
			<input class="action" type="hidden" name="action" value="module-check-api-key" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form> -->

		<form method="post" id="post">
			
			<h5>Enable Updates and Support</h5>
			<label for="api_key"><?php _e('API Key','sendpress'); ?></label>
			<?php if($key_active){
				echo '<span class="icon-ok-sign"></span>';
			}?>
			<input <?php if($key_active){ echo 'disabled'; } ?> name="api_key" type="text" id="api_key" value="<?php echo SendPress_Option::get('api_key'); ?>" class="regular-text">
			<?php if( !$key_active ): ?>
				<a href="#" class="save-api-key btn-primary btn-small btn">Register Key</a>
			<?php else: ?>
				<a href="#" class="save-api-key btn-small btn">Remove Key</a>
			<?php endif; ?>
			<div class="description">
				Enter your API key to enable premium support and automatic updates. Get your API key by logging into <a href="http://sednpress.com">SendPress.com</a>.
			</div>
			<input class="action" type="hidden" name="action" value="<?php if($key_active){ echo 'module-deactivate-api-key'; }else{ echo 'module-save-api-key'; }?>" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>
		
	<?php
	}

	function buttons($plugin_path){
		
		switch( $this->pro_plugin_state() ){
			case 'installable':
				$button = array(
					'class' => 'btn btn-success btn-activate', 
					'href' => wp_nonce_url(admin_url('update.php?action=install-plugin&plugin=sendpress-pro'), 'install-plugin_sendpress-pro'), 
					'target' => '', 
					'text' => 'Install Pro'
				);
				break;
			case 'not-installed':
				$button = array(
					'class' => 'btn-primary btn-buy btn', 
					'href' => 'http://sendpress.com', 
					'target' => '_blank', 
					'text' => 'Buy Now'
				);
				break;
			case 'activated':
				$button = array(
					'class' => 'btn module-deactivate-plugin', 
					'href' => '#', 
					'target' => '', 
					'text' => 'Deactivate'
				);
				break;
			case 'installed':
				$button = array(
					'class' => 'module-activate-plugin btn-success btn-activate btn', 
					'href' => '#',
					'target' => '', 'text' => 'Activate'
				);
				break;
		}

			
		$btn = $this->build_button($button);
		
		
		echo '<div class="inline-buttons">'.$btn.'</div>';
	}

	function module_start(){
		echo '<div class="sendpress-module pro-module">';
		echo '<div class="inner-module">';
	}

}
