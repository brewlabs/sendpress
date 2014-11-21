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
				<p><?php _e('You have SendPress Pro Version','sendpress'); ?> <?php echo SENDPRESS_PRO_VERSION; ?> <?php _e('this version does not support custom fields. You will need to update to the latest version','sendpress'); ?>.</p>
				<?php 
			} else {
				?>
				<p><?php _e('Custom fields requires','sendpress'); ?> <a href="https://sendpress.com" target="_blank"><?php _e('SendPress Pro','sendpress'); ?></a>. <?php _e('Please upgrade or install Pro to start using this feature','sendpress'); ?>.</p>
				<?php 
			}
			$this->panel_end();
	}

}