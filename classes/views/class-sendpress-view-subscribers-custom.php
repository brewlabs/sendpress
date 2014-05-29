<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Custom extends SendPress_View_Subscribers {
	
	function html($sp) {
			$this->panel_start('Upgrade to SendPress Pro');
			if(defined('SENDPRESS_PRO_VERSION')){
				?>
				<p>You have SendPress Pro Version <?php echo SENDPRESS_PRO_VERSION; ?> this version does not support custom fields. You will need to update to the latest version.</p>
				<?php 
			} else {
				?>
				<p>Custom fields requires <a href="https://sendpress.com" target="_blank">SendPress Pro</a>. Please upgrade or install Pro to start using this feature.</p>
				<?php 
			}
			$this->panel_end();
	}

}