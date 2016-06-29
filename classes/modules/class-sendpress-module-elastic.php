<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Elastic extends SendPress_Module{
	

	function html(){
		$hide = false;
		$plugin_path = '';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/sendpress-sender-elastic.php';
		}
	?>
		<h4>Elastic Email</h4>
		<form method="post" id="post">
			<div class="description">
				Connects to <a href="http://www.elasticemail.com/" target="_blank">Elastic Email</a> to send your SendPress emails. A <a href="http://www.elasticemail.com/" target="_blank">Elastic Email</a> account is required to use this option.
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