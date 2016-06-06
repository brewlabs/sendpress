<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Reports extends SendPress_Module{
	
	function html(){
		$hide = false;
		$plugin_path = 'sendpress-advanced-reports/sendpress-advanced-reports.php';
		if( $this->is_pro_active() ){
			$plugin_path = 'sendpress-pro/extensions/class-sendpress-advanced-reports.php';
		}
	?>
		<h4><?php _e('Advanced Reports','sendpress');?></h4>
		<form method="post" id="post">
			<div class="description">
				<?php _e('Add more details to your reports. See who clicked what and when. What device they used and where they were.','sendpress');?>
			</div>
			<?php $this->buttons($plugin_path);?>
			<input type="hidden" name="plugin_path" value="<?php echo $plugin_path; ?>" />
			<input class="action" type="hidden" name="action" value="module-activate-sendpress-pro" />
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>

	<?php
	}

}