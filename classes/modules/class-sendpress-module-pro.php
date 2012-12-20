<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Pro extends SendPress_Module{
	
	function html($sp){
		$key_active = false;
		if( SendPress_Option::get('api_key_state') === 'active' ){
			$key_active = true;
		}
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

		<form method="post" id="post">
			
			<h5>Enable Updates and Support</h5>
			<label for="api_key"><?php _e('API Key','sendpress'); ?></label>
			<input <?php if($key_active){ echo 'disabled'; } ?> name="api_key" type="text" id="api_key" value="<?php echo SendPress_Option::get('api_key'); ?>" class="regular-text">
			<?php if( !$key_active ): ?>
				<a href="#" class="save-api-key btn-primary btn-small btn">Activate</a>
			<?php else: ?>
				<a href="#" class="save-api-key btn-small btn">Deactivate</a>
			<?php endif; ?>
			<div class="description">
				Enter your API key to enable premium support and automatic updates. Get your API key by logging into <a href="http://sednpress.com">SendPress.com</a>.
			</div>
			<input class="action" type="hidden" name="action" value="<?php if($key_active){ echo 'module-deactivate-api-key'; }else{ echo 'module-save-api-key'; }?>" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

	<?php
	}

	function module_start(){
		echo '<div class="sendpress-module pro-module">';
		echo '<div class="inner-module">';
	}

}
