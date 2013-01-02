<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender')){  

class SendPress_Sender {

	function form(){

	}

	function save(){

	}

	function name(){
		return "Default Sender";
	}

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



/**
 * Singleton that registers and instantiates WP_Widget classes.
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 2.8
 */
class SendPress_Sender_Factory {
	var $senders = array();

	function SendPress_Sender_Factory() {
		add_action( 'sendpress_sender_init', array( $this, '_register_senders' ), 100 );
	}

	function register($sender_class) {
		$this->senders[$sender_class] = new $sender_class();
	}

	function unregister($sender_class) {
		if ( isset($this->senders[$sender_class]) )
			unset($this->senders[$sender_class]);
	}

	function _register_senders() {
		global $wp_registered_senders;
		$keys = array_keys($this->senders);
		$registered = array_keys($wp_registered_senders);
		$registered = array_map('_get_widget_id_base', $registered);

		foreach ( $keys as $key ) {
			// don't register new widget if old widget with the same id is already registered
			if ( in_array($this->senders[$key]->id_base, $registered, true) ) {
				unset($this->senders[$key]);
				continue;
			}

			$this->senders[$key]->_register();
		}
	}
}



function _get_widget_id_base($id) {
	return preg_replace( '/-[0-9]+$/', '', $id );
}


