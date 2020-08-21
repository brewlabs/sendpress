<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}	




global $wp_version;
if ( version_compare( $wp_version, '5.5', '>=' ) ) {
    if(!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require(ABSPATH . WPINC .'/PHPMailer/PHPMailer.php');
        require(ABSPATH . WPINC . '/PHPMailer/SMTP.php');
        require(ABSPATH . WPINC . '/PHPMailer/Exception.php');
    }

    class SendPress_PHPMailer extends PHPMailer\PHPMailer\PHPMailer {

    }
} else {
    if(!class_exists('PHPMailer')) {
        require(ABSPATH . WPINC . '/class-phpmailer.php');
    }
    class SendPress_PHPMailer extends PHPMailer {

    }
}


