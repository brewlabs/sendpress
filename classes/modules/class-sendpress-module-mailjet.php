<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Mailjet extends SendPress_Module{
	

	function html(){
		$hide = false;
		$plugin_path = '';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/sendpress-sender-mailjet.php';
		}
	?>
		<h4>MailJet</h4>
		<form method="post" id="post">
			<div class="description">
				Connects to <a href="http://www.mailjet.com/" target="_blank">MailJet</a> to send your SendPress emails. A <a href="http://www.mailjet.com/" target="_blank">MailJet</a> account is required to use this option.
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