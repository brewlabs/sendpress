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
For the best security we recommend <a href="https://wordpress.org/plugins/post-smtp/">Postman SMTP</a>. Just configure <strong>Postman SMTP</strong> and set <strong>SendPress</strong> to send via your <strong>Website</strong>. <strong>Postman SMTP</strong> uses Googles new security setup to securely connect and send emails.
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



	function send_email($to, $subject, $html, $text, $istest = false ,$sid , $list_id, $report_id, $fromname, $fromemail){
		
		$phpmailer = new SendPress_PHPMailer;
		/*
		 * Make sure the mailer thingy is clean before we start,  should not
		 * be necessary, but who knows what others are doing to our mailer
		 */
		// If we don't have a charset from the input headers
		

		$phpmailer->ClearAddresses();
		$phpmailer->ClearAllRecipients();
		$phpmailer->ClearAttachments();
		$phpmailer->ClearBCCs();
		$phpmailer->ClearCCs();
		$phpmailer->ClearCustomHeaders();
		$phpmailer->ClearReplyTos();
		//return $email;
		
		$charset = SendPress_Option::get('email-charset','UTF-8');
		$encoding = SendPress_Option::get('email-encoding','8bit');
		
		$phpmailer->CharSet = $charset;
		$phpmailer->Encoding = $encoding;


		if($charset != 'UTF-8'){
             $html = $this->change($html,'UTF-8',$charset);
             $text = $this->change($text,'UTF-8',$charset);
             $subject = $this->change($subject,'UTF-8',$charset);
                    
            }

        //$from_email = SendPress_Option::get('fromemail');
		$phpmailer->From = $fromemail;
		$phpmailer->FromName = $fromname;//SendPress_Option::get('fromname');
		//$phpmailer->Sender = 'bounce@sendpress.us';
		//$phpmailer->Sender = SendPress_Option::get('fromemail');
		$sending_method  = SendPress_Option::get('sendmethod');


        //$subject = str_replace(array('â€™','â€œ','â€�','â€“'),array("'",'"','"','-'),$subject);
        //$html = str_replace(chr(194),chr(32),$html);
		//$text = str_replace(chr(194),chr(32),$text);
		
		
		$phpmailer->AddAddress( trim( $to ) );
		$phpmailer->AltBody= $text;
		$phpmailer->Subject = $subject;
		$phpmailer->MsgHTML( $html );
		$content_type = 'text/html';
		$phpmailer->ContentType = $content_type;
		// Set whether it's plaintext, depending on $content_type
		//if ( 'text/html' == $content_type )
		$phpmailer->IsHTML( true );
		
		$rpath = SendPress_Option::get('bounce_email');
		if( $rpath != false ){
			$phpmailer->ReturnPath = $rpath;
		}

		/**
		* We'll let php init mess with the message body and headers.  But then
		* we stomp all over it.  Sorry, my plug-inis more important than yours :)
		*/
		do_action_ref_array( 'phpmailer_init', array( &$phpmailer ) );
		
		
		$phpmailer->Mailer = 'smtp';
		// We are sending SMTP mail
		$phpmailer->IsSMTP();
		// Set the other options
		$phpmailer->Host = 'smtp.gmail.com';
		$phpmailer->SMTPAuth = true;  // authentication enabled
		$phpmailer->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail

		$phpmailer->Port = 587;
		// If we're using smtp auth, set the username & password
		$phpmailer->SMTPAuth = TRUE;
		$phpmailer->Username = SendPress_Option::get('gmailuser');
		$phpmailer->Password = SendPress_Option::get('gmailpass');
		
		
		$hdr = new SendPress_SendGrid_SMTP_API();
		$hdr->addFilterSetting('dkim', 'domain', SendPress_Manager::get_domain_from_email($from_email) );
		$phpmailer->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', $hdr->asJSON() ) );
		$phpmailer->AddCustomHeader('X-SP-METHOD: Gmail');
		$phpmailer->AddCustomHeader('X-SP-LIST: ' . $list_id );
		$phpmailer->AddCustomHeader('X-SP-REPORT: ' . $report_id );
		$phpmailer->AddCustomHeader('X-SP-SUBSCRIBER: '. $sid );
		$phpmailer->AddCustomHeader('List-Unsubscribe: <mailto:'.$from_email.'>');
		
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

		if (  $result != true ){
			$log_message = 'Gmail <br>';
			$log_message .= $to . "<br>";
			
			if( $istest == true  ){
				$log_message .= "<br><br>";
				$log_message .= $smtp_debug;
			}
			//$phpmailer->ErrorInfo
			SPNL()->log->add(  $phpmailer->ErrorInfo , $log_message , 0 , 'sending' );
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