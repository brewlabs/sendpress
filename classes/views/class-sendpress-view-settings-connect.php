<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Settings_Connect') ){

class SendPress_View_Settings_Connect extends SendPress_View_Settings {

	function save(){
		
	}

	function html() {
		?>
		<form method="post" id="post">
			Connect your site to AutoCron V2.....
			<input type="submit" class="btn btn-primary" value="Connect" />
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		<?php
	}

}

} //End Class Check

SendPress_Admin::add_cap('Settings_Access','sendpress_settings_access');
