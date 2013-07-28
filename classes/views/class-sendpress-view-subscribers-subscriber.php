<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Subscriber extends SendPress_View_Subscribers {

	
	function html($sp) {
		?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Edit Subscriber','sendpress'); ?></h2>
	</div>
<?php
	$sub = SendPress_Data::get_subscriber($_GET['subscriberID'],$_GET['listID']);
	
	?>
	<form id="subscriber-edit" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="edit-subscriber" />
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <input type="hidden" name="subscriberID" value="<?php echo $_GET['subscriberID']; ?>" />
	    <span class="sublabel"><?php _e('Email','sendpress'); ?>:</span> <input type="text" name="email" class="regular-text sp-text" value="<?php echo $sub->email; ?>" /><br>
	    <span class="sublabel"><?php _e('Firstname','sendpress'); ?>:</span> <input type="text" name="firstname" value="<?php echo $sub->firstname; ?>" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Lastname','sendpress'); ?>:</span> <input type="text" name="lastname" value="<?php echo $sub->lastname; ?>" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Status','sendpress'); ?>:</span> <select name="status">
			<?php 
				$results = $sp->getData($sp->subscriber_status_table());
				foreach($results as $status){
					$selected = '';
					if($status->status == $sub->status){
						$selected = 'selected';
					}
					echo "<option value='$status->statusid' $selected>$status->status</option>";

				}


			?>

	    </select>
	    <br>
	   <input type="submit" class="btn btn-primary" value="<?php _e('submit','sendpress'); ?>"/>
	   <?php wp_nonce_field($sp->_nonce_value); ?>

	</form>
	<?php	
	}

}