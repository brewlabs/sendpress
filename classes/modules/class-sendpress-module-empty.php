<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Module_Empty extends SendPress_Module{
	
	function html($sp){
	?>
		<h4>More Coming Soon!</h4>
		
			<div class="description">
				Cool stuff is on the horizon, check back soon!
			</div>
			<?php 

			$btn = $this->build_button(
				array(	'class' => 'btn module-empty', 
						'href' => 'http://sendpress.uservoice.com', 
						'target' => '_blank', 
						'text' => 'Request a Feature'
					)
			);

			echo '<div class="inline-buttons">'.$btn.'</div>';
			?>
			<?php wp_nonce_field($sp->_nonce_value); ?>
		

	<?php
	}

}