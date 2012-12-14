<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listcreate extends SendPress_View_Subscribers {
	
	function html($sp) {
		?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Create List','sendpress'); ?></h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="list-create" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="create-list" />
	    <p><input type="text" name="name" value="" /></p>
	    <p><input type="checkbox" class="edit-list-checkbox" name="public" value="1" checked /><label for="public"><?php _e('Allow user to sign up to this list','sendpress'); ?></label></p>
	    <!-- Now we can render the completed list table -->
	   	<input type="submit" value="save" class="button-primary"/>
	   	<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<?php
	}

}