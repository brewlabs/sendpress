<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender_Gmail')){  

class SendPress_Sender_Gmail extends SendPress_Sender {
	function label(){
		return __('Gmail','sendpress');
	}

	
	function settings(){ ?>
	<h1>Gmail sending has been disabled. Please update to continue sending.</h1>
	<br>
	With Google's recent changes to SMTP security, direct sending of email no longer works without allowing "less secure apps". Your can read more about that here: <a href="https://support.google.com/accounts/answer/6010255?hl=en">https://support.google.com/accounts/answer/6010255?hl=en</a>
<br><br>
For the best security we recommend <a href="https://wordpress.org/plugins/postman-smtp/">Postman SMTP</a>. Just configure <strong>Postman SMTP</strong> and set <strong>SendPress</strong> to send via your <strong>Website</strong>. <strong>Postman SMTP</strong> uses Googles new security setup to securely connect and send emails.
	<br><br>
	<!--
	You can still use the old sending option for now but we do not recommend enabling less secure apps in Google.
	<br><br>
<hr>
<br><br>
	 <p><?php _e( 'Gmail is limited to 500 emails a day. We recommend that you open a dedicated Gmail account for this purpose', 'sendpress' ); ?>.</p>
  <?php _e( 'Username' , 'sendpress'); ?>
  <p><input name="gmailuser" type="text" value="<?php echo SendPress_Option::get( 'gmailuser' ); ?>" style="width:100%;" /></p>
  <?php _e( 'Password' , 'sendpress'); ?>
  <p><input name="gmailpass" type="password" value="<?php echo SendPress_Option::get( 'gmailpass' ); ?>" style="width:100%;" /></p>
-->
	<?php

	}


	function send_email($to, $subject, $html, $text, $istest = false ,$sid , $list_id, $report_id ){
		SPNL()->log->add( 'Gmail Sending' ,'Sending via gmail is disabled. Failed send to: '  . $to , 0 , 'sending' );
		return false;

	}



}


}