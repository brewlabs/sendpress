<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Autoresponders extends SendPress_Module{
	
	function html(){
		$hide = false;
		$plugin_path = '';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/sendpress-autoresponders.php';
		}
	?>
		<h4>Autoresponders</h4>
		<form method="post" id="post">
			<div class="description">
				Allows you to send a follow-up email after an event. Like send a welcome email a few days after someone subscribes to a list.
			</div>
			<div class="inline-buttons"><a class="btn disabled btn-activate" href="#">Coming Soon</a></div>
			<?php //$this->buttons($plugin_path);?>
			<input type="hidden" name="plugin_path" value="<?php echo $plugin_path; ?>" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>

	<?php
	}

}