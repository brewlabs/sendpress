<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Settings_Advanced extends SendPress_View_Settings {
	
	function save($post, $sp){

	if(isset( $post['allow_tracking'] )){
		SendPress_Option::set('allow_tracking', 'yes' );
		SendPress_Option::set('feedback', 'yes' );
	} else {
		SendPress_Option::set('allow_tracking', 'no' );
		SendPress_Option::set('feedback', 'no' );
	}

	}

	function html($sp) {
		?><form method="post" id="post">
<div style="float:right;" >
	<a href="<?php echo self::link(); ?>" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<br class="clear">
		<br class="clear">
<div class="boxer form-box">
	<div style="float: right; width: 45%;"><br>
	<br class="clear">
	</div>	
	<div style="width: 45%; margin-right: 10%">
		<h2>Tracking</h2>
		<?php $ctype = SendPress_Option::get('allow_tracking'); ?>
	<input type="checkbox" name="allow_tracking" value="yes" <?php if($ctype=='yes'){echo "checked='checked'"; } ?> /> Allow tracking of this WordPress installs anonymous data.
		<p>	
	To maintain a plugin as big as SendPress, we need to know what we're dealing: what kinds of other plugins our users are using, what themes, etc. Please allow us to track that data from your install. It will not track any user details, so your security and privacy are safe with us.</p>
	<br class="clear">
	</div>

</div>
<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<?php
	}

}