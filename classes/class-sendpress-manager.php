<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Manager {

	static function limit_reached($count = 1){
		global $wpdb;
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$pause_sending = SendPress_Option::get('pause-sending','no');

		//Stop Sending for now
		if($pause_sending == 'yes'){
			return true;
		}

		$email_count_day = SendPress_Data::emails_sent_in_queue("day");
		// Check our daily send limit
		if($emails_per_day != false && $emails_per_day != 0 ){
			if( intval($email_count_day) >= intval($emails_per_day)  ){
				//We hit the daily limit
				return true;
			}

			if( ( intval($email_count_day ) + $count ) > intval($emails_per_day) ){
				return true;
			}

		}
		$email_count_hour = SendPress_Data::emails_sent_in_queue("hour");
		// Check our hourly send limit
		if($emails_per_hour != false && $emails_per_hour != 0 ){
			if( intval($email_count_hour) >= intval($emails_per_hour)  ){
				//We hit the hourly limit
				return true;
			}

			if( ( intval($email_count_hour ) + $count ) > intval($emails_per_hour) ){
				return true;
			}
		}

		return false;
	}



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
		$emails_this_hour = SendPress_Data::emails_sent_in_queue("hour");
		$emails_today = SendPress_Data::emails_sent_in_queue("day");
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

	static function public_url($code){
		$permalinks = get_option('permalink_structure');
		$pos = strpos($permalinks, "index.php");
		$indexer ="";
		if ($pos > 0) { // note: three equal signs
			    $indexer = "index.php";
		}
		if( is_ssl() ){
			$h = 'https';
		} else {
			$h = 'http';
		}

		if( SendPress_Option::get('old_permalink') || !get_option('permalink_structure') ){
				$link = home_url("?sendpress=".$code , $h);
			} else {
				$link = home_url("{$indexer}/sendpress/".$code."/", $h);
				
			}
		return $link;
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
	

	static function send_single_from_queue(){
		
		global $wpdb;
		//$emails = $this->wpdbQuery("SELECT * FROM ".$this->queue_table()." WHERE success = 0 AND max_attempts != attempts LIMIT ".$limit,"get_results");
		$count = 0;
		$attempts = 0;
		$queue_table = SendPress_Data::queue_table();
		if( SendPress_Manager::limit_reached()  ){
			return array('attempted'=> $attempts,'sent'=>$count);
		}
		$tracker_disable =  SendPress_Option::get( 'tracker_off', false);
		
		if( $tracker_disable == true ){
			$user_tracker = false;
		}
		$user_tracker = false;
		SendPress_Email_Cache::build_cache();
		$email = SendPress_Data::get_single_email_from_queue( true );
		if( is_object($email) ){
			//$email = $email[0];
			

			if( SendPress_Manager::limit_reached() ){
				return array('attempted'=> $attempts,'sent'=>$count);
			}
			$attempts++;
			SendPress_Data::queue_email_process( $email->id );
			$result = SendPress_Manager::send_email_from_queue( $email, $user_tracker );
			
			if ($result) {
				if($result === true){
					$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
					//( $sid, $rid, $lid=null, $uid=null, $ip=null, $device_type=null, $device=null, $type='confirm' )
					$wpdb->insert(SendPress_Data::subscriber_tracker_table() , array('subscriberID'=>$email->subscriberID,'emailID'=>$email->emailID,'sent_at' => get_gmt_from_date( date('Y-m-d H:i:s') ) ) );
							
					SendPress_Data::add_subscriber_event($email->subscriberID, $email->emailID, $email->listID,null,null,null,null, 'send');
				} else {
					$wpdb->update( SendPress_Data::queue_table() , array('success'=>2,'inprocess'=>3 ) , array('id'=> $email->id ));
					SendPress_Data::add_subscriber_event($email->subscriberID, $email->emailID, $email->listID,null,null,null,null, 'bounce');
					SendPress_Data::bounce_subscriber_by_id( $email->subscriberID );
				}
				$count++;
			
			} else {
				$wpdb->update($queue_table , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
			}
		} else{//We ran out of emails to process.
			return array('attempted'=> $attempts,'sent'=>$count);
		}

		//SendPress_Manager::increase_email_count( $attempts );
		return array('attempted'=> $attempts,'sent'=>$count);
	}



	static function send_optin($subscriberID, $listids, $lists){
			$subscriber = SendPress_Data::get_subscriber( $subscriberID );
			$l = '';
			$optin_id = 0;
			foreach($lists as $list){
				if( in_array($list->ID, $listids) ){
					$l .= $list->post_title ." <br>";

					if($optin_id === 0){
						$o = get_post_meta($list->ID, 'opt-in-id', true);

						if($o === ""){
							$o = 0;
						}

						if($o > 0){
							$optin_id = $o;
						}
					}	
				}
			}
			//	add_filter( 'the_content', array( $this, 'the_content') );	
			$optin = ($optin_id > 0) ? $optin_id : SendPress_Data::get_template_id_by_slug('double-optin');
			$user = SendPress_Data::get_template_id_by_slug('user-style');
			SendPress_Posts::copy_meta_info($optin,$user);
			SendPress_Email_Cache::build_cache_for_system_email($optin);

			 $go = array(
                'from_name' => 'queue',
                'from_email' => 'queue',
                'to_email' => $subscriber->email,
                'emailID'=> intval($optin),
                'subscriberID'=> intval( $subscriberID ),
                //'to_name' => $email->fistname .' '. $email->lastname,
                'subject' => '',
                //'date_sent' => $time,
                'listID'=> 0
                );
           
            $id = SendPress_Data::add_email_to_queue($go);	
			SPNL()->load("Subscribers_Tracker")->add( array('subscriber_id' => intval( $subscriberID ), 'email_id' => intval( $optin), 'tracker_type' => SendPress_Enum_Tracker_Type::Confirm ) );
			
			$confirm_email = SendPress_Data::get_single_email_from_queue_by_id( $id );
			SendPress_Email_Cache::build_cache_for_system_email($confirm_email->id);
			$confirm_email->is_confirm = true;
			SendPress_Queue::send_the_queue($confirm_email);

			/*
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
				$link = home_url() ."?sendpress=".$code;
			} else {
				$link = home_url() ."/sendpress/".$code;
			}
			
			$href = $link;
			$html_href = "<a href='". $link  ."'>". $link  ."</a>";
			
			
			$html = str_replace("*|SP:CONFIRMLINK|*", $html_href , $html );
			$text = str_replace("*|SP:CONFIRMLINK|*", $href , $text );
			$text = nl2br($text);
			$sub =  $message->subject();
			SPNL()->load("Subscribers_Tracker")->add( array('subscriber_id' => intval( $subscriberID ), 'email_id' => intval( $optin), 'tracker_type' => SendPress_Enum_Tracker_Type::Confirm ) );
			//SendPress_Data::register_event( 'confirm_sent', $subscriberID );			
			SendPress_Manager::send( $subscriber->email, $sub , $html, $text, false );
			*/
	}

	static function send_manage_subscription($subscriberID, $listids, $lists){
		$subscriber = SendPress_Data::get_subscriber( $subscriberID );

		
		
		SendPress_Email_Cache::build_cache_for_system_email($optin);






		
	}


	/**
	* Used to add Overwrite send info for testing. 
	*
	* @return boolean true if mail sent successfully, false if an error
	*/
    static function send_email_from_queue( $email , $tracker = true ) {

	   	$message = new SendPress_Email();
	   	$message->id( $email->emailID );
	   	$message->subscriber_id( $email->subscriberID );
	   	$message->tracker( $tracker );
	   	$message->list_id( $email->listID );
	   	$body = $message->html();
	   	$subject = $message->subject();
	   	$to = $email->to_email;
	   	$text = $message->text();

	   	if(empty($text) || $text == '' || empty($body) || $body== '' || $body == " "){
	   		SPNL()->log->add(  'Email Skiped' , 'Email id #'.$email->emailID . ' to '.$to.' did not have any content. Was the email or template deleted?', 0 , 'sending' );
	   		return false;
	   	}
	   	
	   	unset($message);
	   	return SendPress_Manager::send($to , $subject, $body, $text, false, $email->subscriberID ,$email->listID, $email->emailID );
	   
	}

	/**
	* Used to add Overwrite send info for testing. 
	*
	* @return boolean true if mail sent successfully, false if an error
	*/
    static function send_test_email( $email ) {
    	SendPress_Email_Cache::build_cache_for_email($email->emailID);
	   	$message = new SendPress_Email();
	   	$message->id( $email->emailID );
	   	$message->purge( true );
	   	$message->subscriber_id( $email->subscriberID );
	   	$message->list_id( $email->listID );

	   	$fromname = '';
	   	if(isset($email->from_name)){
	   		$fromname = $email->from_name;
	   	}
	   	
	   	$fromemail = '';
	   	if(isset($email->from_email)){
	   		$fromemail = $$email->from_email;
	   	}


	   	$body = $message->html();
	   	$subject = $message->subject();
	   	$to = $email->to_email;
	   	$text = $message->text();
	   	if(empty($text) || $text == '' || empty($body) || $body == '' || $body == " "){
	   		SPNL()->log->add(  'Email Skiped' , 'Email id #'.$email->emailID . ' to '.$to.' did not have any content. Was the email or template deleted?', 0 , 'sending' );
	   		return false;
	   	}

	   	return SendPress_Manager::send($to , $subject, $body, $text, true, $email->subscriberID ,$email->listID, $email->emailID, $fromname, $fromemail );
	   
	}

	static function send($to , $subject, $body, $text, $test = false, $sid=0 ,$list_id = 0, $report_id = 0, $fromname='', $fromemail=''){

		//SendPress_Error::log('Send me an email!');
		
		global $sendpress_sender_factory;
	   //	$senders = $sendpress_sender_factory->get_all_senders();
   		$method = SendPress_Option::get( 'sendmethod' );

		//SendPress_Error::log(array($to, $subject,$method));

   		if(empty($fromname) || $fromname == ''){
   			$fromname = SendPress_Option::get('fromname');
   		}
   		if(empty($fromemail) || $fromemail==''){
   				$fromemail = SendPress_Option::get('fromemail');
		}


   		$sender = $sendpress_sender_factory->get_sender($method);
   		if( $sender != false ){
   			if( empty($text) || $text == '' || empty($body) || $body== '' || $body == " "){
   				SPNL()->log->add(  'Email Skiped' , 'Email to '.$to.' did not have any Text.', 0 , 'sending' );
   				return false;
	   		}
	   		return $sender->send_email( $to, $subject, $body, $text, $test, $sid , $list_id, $report_id, $fromname, $fromemail);
   		}
   		return false;
   		/*
   		$website = new SendPress_Sender_Website();
   		return  $website->send_email( $to, $subject, $body, $text, $test, $sid , $list_id, $report_id );
   		*/

	}

	static function old_send_email($to, $subject, $html, $text, $istest = false , $sid ,$list_id, $report_id ){
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
		$encoding = SendPress_Option::get('email-encoding','8bit');
		
		$phpmailer->CharSet = $charset;
		$phpmailer->Encoding = $encoding;

		
		if($charset != 'UTF-8'){
			$sender = new SendPress_Sender;
            $html = $sender->change($html,'UTF-8',$charset);
            $text = $sender->change($text,'UTF-8',$charset);
            $subject = $sender->change($subject,'UTF-8',$charset);
                    
        }
		
            

        $subject = str_replace(array('â€™','â€œ','â€�','â€“'),array("'",'"','"','-'),$subject);
        $html = str_replace(chr(194),chr(32),$html);
		$text = str_replace(chr(194),chr(32),$text);
		
		
		$phpmailer->AddAddress( trim( $to ) );
		$phpmailer->AltBody= $text;
		$phpmailer->Subject = $subject;
		$phpmailer->MsgHTML( $html );
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