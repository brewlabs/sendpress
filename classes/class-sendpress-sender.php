<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender')){  

class SendPress_Sender {

	function init(){
		add_filter('sendpress_sending_method_gmail',array('SendPress_Sender','gmail'),10,1);
		add_filter('sendpress_sending_method_sendpress',array('SendPress_Sender','sendpress'),10,1);
	}

	//qjrizsmmteujfzcq
	function gmail($phpmailer){
		// Set the mailer type as per config above, this overrides the already called isMail method
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
		return $phpmailer;
	}

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

}





}