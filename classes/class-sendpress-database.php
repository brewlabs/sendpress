<?php
// SendPress Required Class: SendPress_Database

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Database extends SendPress_Base {
	
	function __construct(){

	}

	function nonce(){
		return 'sendpress-is-awesome';
	}

	function nonce_field(){
		wp_nonce_field( SendPress_Data::nonce() );
	}

	function email_post_type(){
		return 'sp_newsletters';
	}
	
	function template_post_type(){
		return 'sptemplates';
	}

	function report_post_type(){
		return 'sp_report';
	}

	function gmdate(){
		return gmdate('Y-m-d H:i:s');
	}


}