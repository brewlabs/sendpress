<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Manager {


	function send_test(){
		$text= __('This is text only alternative body.','sendpress');
		$subject = __('A Test Email from SendPress.','sendpress');
		$body= __( 'SendPress test email :).','sendpres' );
		$testemails = explode(',' , SendPress_Option::get('testemail') );
		foreach ($testemails as $emailadd) {
			 SendPress_Manager::send($emailadd, $subject, $body, $text, true );
		}
	}	
	


	function send_optin($subscriberID, $listids, $lists){
			$subscriber = SendPress_Data::get_subscriber( $subscriberID );
			$l = '';
			foreach($lists as $list){
				if( in_array($list->ID, $listids) ){
					$l .= $list->post_title ." <br>";
				}
			}
			//	add_filter( 'the_content', array( $this, 'the_content') );	
			$optin = SendPress_Data::get_template_id_by_slug('double-optin');
			$user = SendPress_Data::get_template_id_by_slug('user-style');
			SendPress_Posts::copy_meta_info($optin,$user);

			

			$message = new SendPress_Email();
			$message->id($optin);
			$message->remove_links(true);
			$message->purge(true);
			$message->subscriber_id($subscriberID);
			
			$code = array(
					"id"=>$subscriberID,
					"listids"=> implode(',',$listids),
					"view"=>"confirm"
				);
			$code = SendPress_Data::encrypt( $code );

			$href = site_url() ."?sendpress=".$code;

			$html = $message->html();
			$html = str_replace("*|SP:CONFIRMLINK|*", $href , $html );

			$sub =  $message->subject();
			SendPress_Manager::send( $subscriber->email, $sub , $html, '', false );
	}


	/**
	* Used to add Overwrite send info for testing. 
	*
	* @return boolean true if mail sent successfully, false if an error
	*/
    function send_email_from_queue( $email ) {

	   	$message = new SendPress_Email();
	   	$message->id( $email->emailID );
	   	$message->subscriber_id( $email->subscriberID );
	   	$message->list_id( $email->listID );
	   	$body = $message->html();
	   	$subject = $message->subject();
	   	$to = $email->to_email;
	   	$text = $message->text();
	   	return SendPress_Manager::send($to , $subject, $body, $text);
	   
	}

	function send($to , $subject, $body, $text, $test = false){

		global $sendpress_sender_factory;
	   	$senders = $sendpress_sender_factory->get_all_senders();
   		$method = SendPress_Option::get( 'sendmethod' );

   		if( isset($senders[ $method ]) ){
   			error_log($method);
   			return $senders[$method]->send_email($to, $subject, $body, $text, $test );
   		}

	   	return  SendPress_Manager::old_send_email($to, $subject, $body, $text, $test );

	}

	function old_send_email($to, $subject, $html, $text, $istest = false ){
		global $phpmailer, $wpdb;
		error_log('Old Method');
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
		
		$phpmailer = apply_filters('sendpress_sending_method_'. $sending_method, $phpmailer );

		
		
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

	function get_domain_from_email($email){
		$domain = substr(strrchr($email, "@"), 1);
		return $domain;
	}




}