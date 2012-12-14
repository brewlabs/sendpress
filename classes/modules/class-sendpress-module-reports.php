<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Module_Reports extends SendPress_Module{
	
	function html($sp){
		$hide = false;
		$plugin_path = 'sendpress-advanced-reports/sendpress-advanced-reports.php';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/class-sendpress-advanced-reports.php';
		}
	?>
		<h4>Advanced Reports</h4>
		<form method="post" id="post">
			<div class="description">
				Add more details to your reports. See who clicked what and when.
			</div>
			<?php $this->buttons($plugin_path);?>
			<input type="hidden" name="plugin_path" value="<?php echo $plugin_path; ?>" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

	<?php
	}

}