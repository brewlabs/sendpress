<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender')){  

	class SendPress_Sender {

		function label(){
			return __('Sender Missing Label','sendpress');
		}
		 
		function settings(){

		}

		function save(){

		}

		function name(){
			return "Default Sender";
		}

		static function init(){
			add_filter('sendpress_sending_method_gmail',array('SendPress_Sender','gmail'),10,1);
			add_filter('sendpress_sending_method_sendpress',array('SendPress_Sender','sendpress'),10,1);
			//add_filter('sendpress_sending_method_spnl',array('SendPress_Sender_SPNL','sendpress'),10,1);
		}

		//Legacy Sending
		function gmail($phpmailer){
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = 'smtp';
			// We are sending SMTP mail
			$phpmailer->IsSMTP();
			// Set the other options
			$phpmailer->Host = 'smtp.gmail.com';
			$phpmailer->SMTPAuth = true;  // authentication enabled
			$phpmailer->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail

			$phpmailer->Port = 465;
			// If we're using smtp auth, set the username & password
			$phpmailer->SMTPAuth = TRUE;
			$phpmailer->Username = SendPress_Option::get('gmailuser');
			$phpmailer->Password = SendPress_Option::get('gmailpass');
			return $phpmailer;
		}
		//Legacy Sending
		function sendpress($phpmailer){
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = 'smtp';
			// We are sending SMTP mail
			$phpmailer->IsSMTP();

			// Set the other options
			$phpmailer->Host = 'smtp.sendgrid.net';
			$phpmailer->Port = 25;

			// If we're using smtp auth, set the username & password
			$phpmailer->SMTPAuth = TRUE;
			$phpmailer->Username = SendPress_Option::get('sp_user');
			$phpmailer->Password = SendPress_Option::get('sp_pass');
			return $phpmailer;
		}

		function get_domain_from_email($email){
			$domain = substr(strrchr($email, "@"), 1);
			return $domain;
		}

		function send_email_old($to, $subject, $html, $text, $istest = false, $sid, $list_id, $report_id ){

			
			return false;
		}

		function send_email_new($to, $subject, $html, $text, $istest = false, $sid, $list_id, $report_id, $fromname, $fromemail ){
			return false;
		}


		function change($data,$input,$output){
			$input = strtoupper(trim($input));
			$output = strtoupper(trim($output));
			if($input == $output) return $data;
			if ($input == 'UTF-8' && $output == 'ISO-8859-1'){
				$data = str_replace(array('€','„','“'),array('EUR','"','"'),$data);
			}
			if ( function_exists('iconv') ){
				set_error_handler('sendpress_encoding_error_handler');
				$encodedData = iconv($input, $output."//TRANSLIT", $data);
				restore_error_handler();
				if(!sendpress_encoding_error_handler('result')){
					return $encodedData;
				}
			}
			if (function_exists('mb_convert_encoding')){
				return mb_convert_encoding($data, $output, $input);
			}
			if ($input == 'UTF-8' && $output == 'ISO-8859-1'){
				return utf8_decode($data);
			}
			if ($input == 'ISO-8859-1' && $output == 'UTF-8'){
				return utf8_encode($data);
			}
			return $data;
		}

		public function __call($method, $arguments) {
		    if($method == 'send_email') {
		        if(count($arguments) == 8) {
		        	return call_user_func_array(array($this,'send_email_old'), $arguments);
		        }
		        else if(count($arguments) == 10) {
		            return call_user_func_array(array($this,'send_email_new'), $arguments);
		        }
		    }
		}    

	}

}

 function sendpress_encoding_error_handler($errno,$errstr=''){
      static $error = false;
      if(is_string($errno) && $errno=='result'){
          $currentError = $error;
          $error = false;
          return $currentError;
      }
      $error = true;
      return true;
 }
