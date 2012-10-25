<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Module_Test extends SendPress_Module{
	
	function html($sp){
	?>
		<h4>Test One</h4>
		<form method="post" id="post">
			<div class="description">
				Add more details to your reporst. See who clicked what and when.
			</div>
			<div class="inline-buttons">
					<a class="btn btn-primary btn-buy" title="Get SendPress Pro" href="http://sendpress.com" target="_blank">Buy Now</a>
				</div>
			<input type="hidden" name="plugin_path" value="sendpress-pro/sendpress-pro.php" />
			<input type="hidden" name="action" value="module-sendpress-pro" />
			<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>

	<?php
	}

}