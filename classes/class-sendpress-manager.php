<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Manager {

	static function send_limit_reached(){

		global $wpdb;
		
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		
		if($emails_per_hour != 0){
			$rate = 3600 / $emails_per_hour;
		}
		if($rate > 8){
			$rate = 8;
		}
		
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;
		$emails_this_hour = SendPress_Option::get('emails-this-hour');
		// Check our daily send limit
		if($emails_per_day != false && $emails_per_day != 0 ){
			if( intval($email_count) >= intval($emails_per_day)  ){
				return true;
			}
		}

		$email_last_sent = SendPress_Option::get('email-last-sent');
		//Haven't sent an email in the last hour
		if( $email_last_sent == false || ( $email_last_sent + (60 * 60) ) <= time()   ){
			SendPress_Option::set('emails-this-hour', 0);
			SendPress_Option::set('time-delay', false);
			return false;
		}


		if( $emails_this_hour >= $emails_per_hour ){
			$time_delay =  SendPress_Option::get('time-delay');
			if($time_delay == false){
				$time_delay = time() + (60 * 59.5);
				SendPress_Option::set('time-delay', $time_delay);
			}	
			if($time_delay < time() ){
				//The hour is up start sending
				SendPress_Option::set('emails-this-hour', 0);
				SendPress_Option::set('time-delay', false);
				return false;
			}
			return true;
		}



		return false;
	}

	static function emails_allowed_to_send(){
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		$count = SendPress_Data::emails_in_queue();
		$emails_this_hour = SendPress_Manager::emails_this_hour();
		$emails_today = SendPress_Manager::emails_today();
		$hour = $emails_per_hour - $emails_this_hour;
		$day = $emails_per_day - $emails_today;

		if($count <= $hour && $count <= $day ){
			return $count;
		}
		if($hour <= $day){
			return $hour;
		}
		return $day;




	}


	static function increase_email_count( $add = 1 ){
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_this_hour = SendPress_Option::get('emails-this-hour');
		$emails_this_hour = $emails_this_hour != false ? $emails_this_hour : 0 ;
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;
		$email_count = $email_count + $add;
		$emails_this_hour = $emails_this_hour + $add;
		$emails_today[date("z")] = $email_count;

		SendPress_Option::set('emails-today', $emails_today );
		SendPress_Option::set('emails-this-hour', $emails_this_hour );
		SendPress_Option::set('email-last-sent', time() );
	}

	static function reset_counters(){
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_today[date("z")] = 0;

		SendPress_Option::set('emails-today', $emails_today );
		SendPress_Option::set('emails-this-hour', 0 );
		SendPress_Option::set('email-last-sent', time() - (60 * 60) );

	}

	static function emails_this_hour(){
		$email_last_sent = SendPress_Option::get('email-last-sent');
		//Haven't sent an email in the last hour
		if( $email_last_sent == false || ( $email_last_sent + (60 * 60) ) <= time()   ){
			SendPress_Option::set('emails-this-hour', 0);
			SendPress_Option::set('time-delay', false);
			return 0;
		}

		$hour = SendPress_Option::get('emails-this-hour');
		return $hour;
	}

	static function emails_today(){
		$emails_today =  SendPress_Option::get('emails-today');
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;
		return $email_count;
	}


	static function send_test(){
		$text= __('This is text only alternative body.','sendpress');
		$subject = __('A Test Email from SendPress.','sendpress');
		$body= __( 'SendPress test email :).','sendpres' );
		$testemails = explode(',' , SendPress_Option::get('testemail') );
		foreach ($testemails as $emailadd) {
			 SendPress_Manager::send($emailadd, $subject, $body, $text, true );
		}
	}	
	


	static function send_optin($subscriberID, $listids, $lists){
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
			$message->subscriber_id($subscriberID);
			$message->remove_links(true);
			$message->purge(true);
			$html = $message->html();
			$message->purge(false);
			$text = $message->text();
			
			
			$code = array(
					"id"=>$subscriberID,
					"listids"=> implode(',',$listids),
					"view"=>"confirm"
				);
			$code = SendPress_Data::encrypt( $code );

			if( SendPress_Option::get('old_permalink') || !get_option('permalink_structure') ){
				$link = site_url() ."?sendpress=".$code;
			} else {
				$link = site_url() ."/sendpress/".$code;
			}
			
			$href = $link;
			$html_href = "<a href='". $link  ."'>". $link  ."</a>";
			
			
			$html = str_replace("*|SP:CONFIRMLINK|*", $html_href , $html );
			$text = str_replace("*|SP:CONFIRMLINK|*", $href , $text );
			$text = nl2br($text);
			$sub =  $message->subject();
			SendPress_Manager::send( $subscriber->email, $sub , $html, $text, false );
	}


	/**
	* Used to add Overwrite send info for testing. 
	*
	* @return boolean true if mail sent successfully, false if an error
	*/
    static function send_email_from_queue( $email ) {

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

	static function send($to , $subject, $body, $text, $test = false){

		global $sendpress_sender_factory;
	   	$senders = $sendpress_sender_factory->get_all_senders();
   		$method = SendPress_Option::get( 'sendmethod' );

   		if( array_key_exists( $method , $senders) && is_a( $senders[$method] , 'SendPress_Sender') ){
   			return $senders[$method]->send_email($to, $subject, $body, $text, $test );
   		}

	   	return  SendPress_Manager::old_send_email($to, $subject, $body, $text, $test );

	}

	static function old_send_email($to, $subject, $html, $text, $istest = false ){
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
		//
		
		$charset = SendPress_Option::get('email-charset','UTF-8');
		
		$phpmailer->CharSet = $charset;
		$phpmailer->Encoding = '8bit';


		if($charset != 'UTF-8'){
             $html = $this->change($html,'UTF-8',$charset);
             $text = $this->change($text,'UTF-8',$charset);
             $subject = $this->change($subject,'UTF-8',$charset);
                    
            }

            

        $subject = str_replace(array('â€™','â€œ','â€�','â€“'),array("'",'"','"','-'),$subject);
        $html = str_replace(chr(194),chr(32),$html);
		$text = str_replace(chr(194),chr(32),$text);
		
		$phpmailer->MsgHTML( $html );
		$phpmailer->AddAddress( trim( $to ) );
		$phpmailer->AltBody= $text;
		$phpmailer->Subject = $subject;
		$content_type = 'text/html';
		$phpmailer->ContentType = $content_type;
		// Set whether it's plaintext, depending on $content_type
		//if ( 'text/html' == $content_type )
		$phpmailer->IsHTML( true );
		

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
		$phpmailer->AddCustomHeader('X-SP-METHOD: old');
		
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

	static function get_domain_from_email($email){
		$domain = substr(strrchr($email, "@"), 1);
		return $domain;
	}




}