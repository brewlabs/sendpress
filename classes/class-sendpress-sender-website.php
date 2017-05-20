<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender_Website')){  

	class SendPress_Sender_Website extends SendPress_Sender {

		var $emailText = '';
		var $sid = '' ;
		var $list_id = '';
		var $report_id = '';

		function label(){
			return __('Your Website','sendpress');
		}

		function save(){
			if(SPNL()->validate->_isset('hosting-provider')){
				SendPress_Option::set('website-hosting-provider', SPNL()->validate->_string('hosting-provider'));
			} else{
				SendPress_Option::set('website-hosting-provider', false);
			}

		}

		function settings(){ ?>
			This option uses your host's local mail server to send emails.<br>Use this option for plugins like Postman SMTP.

			<?php 

				$hosting = SendPress_Option::get('website-hosting-provider');



			?>
			<br><br>
			<input type="checkbox" value="godaddy" name="hosting-provider" <?php if($hosting=="godaddy"){  echo "checked='checked'"; }  ?> /> GoDaddy Hosting<br>
			This sets the smtp host to <b>relay-hosting.secureserver.net</b> for GoDaddy users.<br>GoDaddy limits emails to 1000 per day.
			<!--Send a max of <input type="text" name="emails-per-day" value="" class="sptext"  > Emails per day.-->


		<?php

	}	

	function wpmail_init( $phpmailer ){
		/*
		$phpmailer->ClearCustomHeaders();
		$phpmailer->Body = $this->AltBody;
		$phpmailer->AltBody = $this->AltBody;
		$phpmailer->Subject = $this->Subject;
		$phpmailer->From = $this->From;
		$phpmailer->FromName = $this->FromName;
		$phpmailer->Sender = $this->Sender;
		$phpmailer->MessageID = $this->MessageID;

		$phpmailer->AddAddress( $this->to[0][0], $this->to[0][1] );
		$phpmailer->AddReplyTo( $this->ReplyTo[0][0], $this->ReplyTo[0][1] );
		*/
		$phpmailer->ClearCustomHeaders();
		$from_email = SendPress_Option::get('fromemail');
		$phpmailer->From = $from_email;
		$phpmailer->FromName = SendPress_Option::get('fromname');
		
		$phpmailer->AddCustomHeader('X-SP-METHOD: website wp_mail');
		$charset = SendPress_Option::get('email-charset','UTF-8');
		$encoding = SendPress_Option::get('email-encoding','8bit');
		
		$phpmailer->CharSet = $charset;
		$phpmailer->Encoding = $encoding;
		$phpmailer->ContentType = 'text/html';

		$phpmailer->AddCustomHeader('X-SP-LIST: ' . $this->list_id );
		$phpmailer->AddCustomHeader('X-SP-REPORT: ' . $this->report_id );
		$phpmailer->AddCustomHeader('X-SP-SUBSCRIBER: '. $this->sid );

		$phpmailer->AltBody = $this->emailText;
		$phpmailer->IsHTML( true );
		
		//$phpmailer->WordWrap = $this->WordWrap;

		return $phpmailer;
	}

	function send_email($to, $subject, $html, $text, $istest = false, $sid , $list_id, $report_id,$fromname, $fromemail  ){
		
		$this->emailText = $text;
		$this->sid = $sid;
		$this->list_id = $list_id;
		$this->report_id = $report_id;

		//add_filter( 'phpmailer_init' , array( $this , 'wpmail_init' ) , 90 );
		$link2 = array(
								"id"=>$sid,
								"report"=> $list_id,
								"view"=>"tracker",
								"url" => "{sp-unsubscribe-url}"
							);



							$code2 = SendPress_Data::encrypt( $link2 );
							$link2 = SendPress_Manager::public_url($code2);

			$rpath = SendPress_Option::get('bounce_email');
			if( $rpath != false ){
				$rpath = SendPress_Option::get('fromname');
			}

			$headers = array(
				'Content-Type: text/html; charset=' . SendPress_Option::get('email-charset','UTF-8'), 
				'X-SP-LIST: ' . $this->list_id . ';',
				'X-SP-REPORT: ' . $this->report_id . ';',
				'X-SP-SUBSCRIBER: '. $this->sid . ';',
				'X-SP-METHOD: website wp_mail',
				'From: '. $fromname .' <'.$fromemail.'>',
				'List-Unsubscribe: <'.$link2.'>',
				'Return-Path: '. $rpath
			 );
		
		$r = wp_mail($to, $subject, $html, $headers);
		
		if (!$r) {
			global $phpmailer;
			if (isset($phpmailer)) {
				SPNL()->log->add( 'Website Sending' ,$phpmailer->ErrorInfo , 0 , 'sending' );
			}
		}
		//remove_filter( 'phpmailer_init' , array( $this , 'wpmail_init' ) , 90 );

		return $r;
	}


}


}