<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Settings_Feedback extends SendPress_View_Settings {
	
	function html($sp) {
		?>
<form method="post" id="post">

<br class="clear">
<div style="float:right;" >
	<a href="?page=sp-settings&view=feedback" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<input type="hidden" name="action" value="feedback-setup" />
<br class="clear">
<div class="boxer form-box">
	<h2>Hi,
	<p>We are using presstrends.io to track items like adoption from month-to-month & other trends. We only collect anonymous data and nothing is sent unless you activate the option below.</p>
	
	Thanks for helping,<br>
	<b>The SendPress Team</b>
	</h2>
	<br><br>
	<h2><?php _e('Feeback Opt-in','sendpress'); ?></h2>
	<p><input name="feedback" type="radio"  <?php if($sp->get_option('feedback') == 'yes' ) { ?>checked="checked"<?php } ?>   id="feedback" value="yes" > <?php _e('I would like to help out','sendpress'); ?>.</p>
	<p><input name="feedback" type="radio"  <?php if($sp->get_option('feedback') == 'no' ) { ?>checked="checked"<?php } ?>   id="feedback" value="no" > <?php _e('No Thanks','sendpress'); ?>!</p>
	<br><br>
	<h2><?php _e('Support','sendpress'); ?></h2>
	<?php _e('If you are looking for support or would like to provide written feedback please go to <a href="http://sendpress.zendesk.com"> our support site</a> and submit a ticket.','sendpress'); ?>

</div>

<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<?php
	}

}
