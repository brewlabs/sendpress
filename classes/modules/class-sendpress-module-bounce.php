<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Bounce extends SendPress_Module{
	
	function html($sp){
		$hide = false;
		$plugin_path = '';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/class-sendpress-bounce.php';
		}
	?>
		<h4>Bounce Handling</h4>
		<form method="post" id="post">
			<div class="description">
				Currently supported for Mandrill API sending with nothing to configure.
			</div>
			 <!--<div class="inline-buttons"><a class="btn disabled btn-activate" href="#">Coming Soon</a></div>-->
			<!--<?php $this->buttons($plugin_path);?> -->
			<input type="hidden" name="plugin_path" value="<?php echo $plugin_path; ?>" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

	<?php
	}

}