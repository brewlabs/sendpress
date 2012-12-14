<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listedit extends SendPress_View_Subscribers {
	
	function html($sp) {
		
	$list ='';
	if(isset($_GET['listID'])){
		//$listinfo = $this->getDetail( $this->lists_table(),'listID', $_GET['listID'] );	
		$listinfo = get_post($_GET['listID']);
		$list = '&listID='.$_REQUEST['listID'];
		$listname = 'for '. $listinfo->post_title;
	}
	?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Edit List','sendpress'); ?></h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="list-edit" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="edit-list" />
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <p><input type="text" name="name" value="<?php echo $listinfo->post_title; ?>" /></p>
	    <p><input type="checkbox" class="edit-list-checkbox" name="public" value="<?php echo get_post_meta($listinfo->ID,'public',true); ?>" <?php if( get_post_meta($listinfo->ID,'public',true) == 1 ){ echo 'checked'; } ?> /><label for="public"><?php _e('Allow user to sign up to this list','sendpress'); ?></label></p>
	    <!-- Now we can render the completed list table -->
	   	<input type="submit" value="<?php _e('save','sendpress'); ?>" class="button-primary"/>
	   	<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<?php
	}

}