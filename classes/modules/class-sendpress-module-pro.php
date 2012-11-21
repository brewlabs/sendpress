<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Module_Pro extends SendPress_Module{
	
	function html($sp){
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
			<input name="api_key" type="text" id="api_key" value="<?php echo SendPress_Option::get('api_key'); ?>" class="regular-text">
			<a href="#" class="save-api-key btn-primary btn-small btn">Save</a>
			<div class="description">
				Enter your API key to enable premium support and Pro updates. get your API key <a href="#">here</a>.
			</div>
			<input class="action" type="hidden" name="action" value="module-save-api-key" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

	<?php
	}

	function module_start(){
		echo '<div class="sendpress-module pro-module">';
		echo '<div class="inner-module">';
	}

}
