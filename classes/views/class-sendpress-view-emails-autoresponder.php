<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}



class SendPress_View_Emails_Autoresponder extends SendPress_View_Emails{

	function html($sp){
		 SendPress_Tracking::event('Emails Tab');
	//Create an instance of our package class...
	$testListTable = new SendPress_Autoresponder_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<div id="taskbar" class="lists-dashboard rounded group">

		<div id="button-area">
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=createauto"><?php _e('Create Autoresponder','sendpress'); ?></a>
		</div>

	</div>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display(); ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}

