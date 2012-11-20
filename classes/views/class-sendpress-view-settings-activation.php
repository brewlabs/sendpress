<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if( !class_exists('SendPress_View_Settings_Activation') ){

class SendPress_View_Settings_Activation extends SendPress_View_Settings {

	function save() {
		SendPress_Option::set('send_optin_email', $_POST['optin']);

		SendPress_Option::set('optin_subject', $_POST['subject']);

		SendPress_Option::set('optin_body', $_POST['body']);
		//print_r();
		//echo self::link();
		//print_r(get_class( $this ));
		//self::n();
		//echo "asdf";
		self::redirect();
	}
	
	function html($sp) {
		?>
		<form method="post" id="post">

		<div style="float:right;" >
			<a href=" " class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
		</div>
		<br class="clear">
		<br class="clear">
		
		
<div class="boxer form-box">
	<div style="float: right; width: 45%;">
		<p>Subject</p>
		<input type="text" name="subject" class="regular-text" style="width: 100%;" value="<?php echo SendPress_Option::get('optin_subject'); ?>"/>
		<p>Body</p>
		<textarea style="width:100%;" rows="15" name="body"><?php echo SendPress_Option::get('optin_body'); ?></textarea>

	</div>	
	<div style="width: 45%; margin-right: 10%">
		<p><b>Send Double Opt-in Email:&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('send_optin_email')=='yes'){ echo "checked='checked'"; } ?> name="optin"> Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('send_optin_email')=='no'){ echo "checked='checked'"; } ?> name="optin"> No</b>
			<br>Keep the spammers, robots and other riff-raff off your list. <br>Read more about why to use double opt-in on out support site.</p>
		<!--
		<p><input type="text" class="regular-text" style="width: 100%;"/></p>
		-->
		<br class="clear">
		<br class="clear">

	</div>
</div>




<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
		<?php
	}

}

} //End Class Check

