<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Empty extends SendPress_Module{
	
	function html($sp){
	?>
		<h4><?php _e('More Coming Soon!','sendpress');?></h4>
		
			<div class="description">
				<?php _e('Cool stuff is on the horizon, check back soon!','sendpress');?>
			</div>
			<?php 

			$btn = $this->build_button(
				array(	'class' => 'btn module-empty', 
						'href' => 'http://sendpress.uservoice.com', 
						'target' => '_blank', 
						'text' => __('Request a Feature','sendpress')
					)
			);

			echo '<div class="inline-buttons">'.$btn.'</div>';
			?>
			<?php wp_nonce_field($sp->_nonce_value); ?>
		

	<?php
	}

}