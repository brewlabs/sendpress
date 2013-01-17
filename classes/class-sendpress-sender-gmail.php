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

	function save(){
		$options =  array();
	 	$options['gmailuser'] = $_POST['gmailuser'];
        $options['gmailpass'] = $_POST['gmailpass'];
        SendPress_Option::set( $options );
	}

	function settings(){ ?>
	 <p><?php _e( 'Gmail is limited to 500 emails a day. We recommend that you open a dedicated Gmail account for this purpose', 'sendpress' ); ?>.</p>
  <?php _e( 'Username' , 'sendpress'); ?>
  <p><input name="gmailuser" type="text" value="<?php echo SendPress_Option::get( 'gmailuser' ); ?>" style="width:100%;" /></p>
  <?php _e( 'Password' , 'sendpress'); ?>
  <p><input name="gmailpass" type="password" value="<?php echo SendPress_Option::get( 'gmailpass' ); ?>" style="width:100%;" /></p>

	<?php

	}


	function send_email($to, $subject, $html, $text, $istest = false ){
		global $phpmailer, $wpdb;

		// (Re)create it, if it's gone missing
		if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			require_once ABSPATH . WPINC . '/class-smtp.php';
			$phpmailer = new PHPMailer();
		}
		
		/*
		 * Make sure the mailer thingy is clean before we start,  should not
		 * be necessary, but who knows what others are doing to our mailer
		 */
		$phpmailer->ClearAddresses();
		$phpmailer->ClearAllRecipients();
		$phpmailer->ClearAttachments();
		$phpmailer->ClearBCCs();
		$phpmailer->ClearCCs();
		$phpmailer->ClearCustomHeaders();
		$phpmailer->ClearReplyTos();
		//return $email;
		$phpmailer->MsgHTML( $html );
		$phpmailer->AddAddress( trim( $to ) );
		$phpmailer->AltBody= $text;
		$phpmailer->Subject = $subject;
		$content_type = 'text/html';
		$phpmailer->ContentType = $content_type;
		// Set whether it's plaintext, depending on $content_type
		//if ( 'text/html' == $content_type )
		$phpmailer->IsHTML( true );
		
		// If we don't have a charset from the input headers
		if ( !isset( $charset ) )
		//$charset = get_bloginfo( 'charset' );
		// Set the content-type and charset
		$phpmailer->CharSet = 'UTF-8';
		$phpmailer->Encoding = 'quoted-printable';
		/**
		* We'll let php init mess with the message body and headers.  But then
		* we stomp all over it.  Sorry, my plug-inis more important than yours :)
		*/
		do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
		
		$from_email = SendPress_Option::get('fromemail');
		$phpmailer->From = $from_email;
		$phpmailer->FromName = SendPress_Option::get('fromname');
		$phpmailer->Sender = SendPress_Option::get('fromemail');
		$sending_method  = SendPress_Option::get('sendmethod');
		
		$phpmailer->Mailer = 'smtp';
		// We are sending SMTP mail
		$phpmailer->IsSMTP();
		error_log('gmail');
		// Set the other options
		$phpmailer->Host = 'smtp.gmail.com';
		$phpmailer->SMTPAuth = true;  // authentication enabled
		$phpmailer->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail

		$phpmailer->Port = 465;
		// If we're using smtp auth, set the username & password
		$phpmailer->SMTPAuth = TRUE;
		$phpmailer->Username = SendPress_Option::get('gmailuser');
		$phpmailer->Password = SendPress_Option::get('gmailpass');
		
		
		$hdr = new SendPress_SendGrid_SMTP_API();
		$hdr->addFilterSetting('dkim', 'domain', SendPress_Manager::get_domain_from_email($from_email) );
		$phpmailer->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', $hdr->asJSON() ) );
		$phpmailer->AddCustomHeader('X-SP-ACCOUNT','TEST-FROM_DEV');
		
		// Set SMTPDebug to 2 will collect dialogue between us and the mail server
		if($istest == true){
			$phpmailer->SMTPDebug = 2;
			// Start output buffering to grab smtp output
			ob_start(); 
		}


		// Send!
		$result = true; // start with true, meaning no error
		$result = @$phpmailer->Send();

		//$phpmailer->SMTPClose();
		if($istest == true){
			// Grab the smtp debugging output
			$smtp_debug = ob_get_clean();
			SendPress_Option::set('phpmailer_error', $phpmailer->ErrorInfo);
			SendPress_Option::set('last_test_debug', $smtp_debug);
		
		}

		if (  $result != true && $istest == true  ) {
			$hostmsg = 'host: '.($phpmailer->Host).'  port: '.($phpmailer->Port).'  secure: '.($phpmailer->SMTPSecure) .'  auth: '.($phpmailer->SMTPAuth).'  user: '.($phpmailer->Username)."  pass: *******\n";
		    $msg = '';
			$msg .= __('The result was: ','sendpress').$result."\n";
		    $msg .= __('The mailer error info: ','sendpress').$phpmailer->ErrorInfo."\n";
		    $msg .= $hostmsg;
		    $msg .= __("The SMTP debugging output is shown below:\n","sendpress");
		    $msg .= $smtp_debug."\n";
		}

	
		
		return $result;

	}



}


}