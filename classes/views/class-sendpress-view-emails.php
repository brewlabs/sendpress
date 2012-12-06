<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
if( !class_exists('SendPress_View_Emails') ){

class SendPress_View_Emails extends SendPress_View{
	
	function html($sp){
		
	//Create an instance of our package class...
	$testListTable = new SendPress_Emails_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
	<div id="taskbar" class="lists-dashboard rounded group"> 

		<div id="button-area">  
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=create"><?php _e('Create Email','sendpress'); ?></a>
		</div>
		<h2><?php _e('Emails','sendpress'); ?></h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}
SendPress_View_Emails::cap('sendpress_email');

}