<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}	

if(!class_exists('PHPMailer')){
	require( ABSPATH . WPINC . '/class-phpmailer.php');
}

class SendPress_PHPMailer extends PHPMailer {

}