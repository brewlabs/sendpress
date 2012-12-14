<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Add extends SendPress_View_Subscribers {
	
	function html($sp) { ?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Add Subscriber','sendpress'); ?></h2>
	</div>
<div class="boxer">
	<div class="boxer-inner">
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="subscriber-create" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="create-subscriber" />
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <?php _e('Email','sendpress') ?>: <input type="text" name="email" value="" /><br>
	    <?php _e('Firstname','sendpress'); ?>: <input type="text" name="firstname" value="" /><br>
	    <?php _e('Lastname','sendpress'); ?>: <input type="text" name="lastname" value="" /><br>
	    <?php _e('Status','sendpress'); ?>: <select name="status">
	    			<?php 
	    				$results = $sp->getData($sp->subscriber_status_table());
	    				foreach($results as $status){
	    					$selected = '';
	    					if($status->status == 'Active'){
	    						$selected = 'selected';
	    					}
	    					echo "<option value='$status->statusid' $selected>$status->status</option>";

	    				}


	    			?>

	    		</select>
	    		<br>
	  <button type="submit" class="btn btn-primary"><?php _e('Submit','sendpress'); ?></button>
	   <?php wp_nonce_field($sp->_nonce_value); ?>

	</form>
	</div>
</div>
	
	<h2><?php _e('Add Subscribers','sendpress'); ?></h2>
<div class="boxer">
	<div class="boxer-inner">	
<div style="width: 300px; margin-right: 25%; padding: 15px;" class="rounded box float-right">
<?php _e('Emails shoud be written in separate lines. A line could also include a name, which is separated from the email by a comma','sendpress'); ?>.<br><br>
<strong><?php _e('Correct formats','sendpress'); ?>:</strong><br>
john@gmail.com<br>
john@gmail.com, John<br>
john@gmail.com, John, Doe<br>
</div>

<form id="subscribers-create" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="create-subscribers" />
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	   	<textarea name="csv-add" cols='50' rows='25'></textarea>
	   	<button type="submit" class="btn btn-primary"><?php _e('Submit','sendpress'); ?></button>
	   	<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
</div>
</div>
<?php
	
	}

}