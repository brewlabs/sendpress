<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Mailgun extends SendPress_Module{
	var $_pro_version = '0.7.3';
	function html(){
		$hide = false;
		$plugin_path = '';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/sendpress-sender-mailgun.php';
		}
	?>
		<h4>MailGun</h4>
		<form method="post" id="post">
			<div class="description">
				Connects to <a href="http://mailgun.com" target="_blank">MailGun</a> to send your SendPress emails. A <a href="http://mailgun.com" target="_blank">MailGun</a> account is required to use this option.
				<br><br>
				
			</div>
			<?php $this->buttons($plugin_path);?>
			<input type="hidden" name="plugin_path" value="<?php echo $plugin_path; ?>" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>

	<?php
	}

}