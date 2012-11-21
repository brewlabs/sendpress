<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if( !class_exists('SendPress_View_Settings_Activation') ){

class SendPress_View_Settings_Activation extends SendPress_View_Settings {

	function save() {
		SendPress_Option::set('send_optin_email', $_POST['optin']);

		SendPress_Option::set('optin_subject', $_POST['subject']);
		$optin = SendPress_Data::get_template_id_by_slug('double-optin');
		$dpost = get_post($optin);
		$dpost->post_content = $_POST['body'];
		wp_update_post($dpost);

		//SendPress_Option::set('optin_body', $_POST['body']);
		//print_r();
		//echo self::link();
		//print_r(get_class( $this ));
		//self::n();
		//echo "asdf";
		self::redirect();
	}
	
	function html($sp) {

		$optin = SendPress_Data::get_template_id_by_slug('double-optin');
		$dpost = get_post($optin);
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
		<input type="text" name="subject" class="regular-text" style="width: 100%;" value="<?php echo  stripcslashes($dpost->post_title); ?>"/>
		<p>Body</p>
		<textarea style="width:100%;" rows="15" name="body"><?php 
		remove_filter( 'the_content', 'wpautop' );
		$content = apply_filters('the_content', $dpost->post_content);
									$content = str_replace(']]>', ']]&gt;', $content);
									echo stripcslashes($content); ?></textarea>

	</div>	
	<div style="width: 45%; margin-right: 10%">
		<p><b>Send Double Opt-in Email:&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('send_optin_email')=='yes'){ echo "checked='checked'"; } ?> name="optin"> Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('send_optin_email')=='no'){ echo "checked='checked'"; } ?> name="optin"> No</b>
			<br>Keep the spammers, robots and other riff-raff off your list. <br>Read more about why to use double opt-in on out support site.</p>
			<br>
			<b>Quick Tags</b>: work in both subject and body.
			<div class='well'>
				<b>*|SP:CONFIRMLINK|*</b> This is required to be in the body of the email.
				<hr>
				*|SITE:TITLE|* - Add Website title to the email.
				<hr>
				*|EMAIL|* - Add the email the user signed up with.
			</div>
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

