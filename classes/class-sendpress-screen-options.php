<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Screen_Options')){  

class SendPress_Screen_Options {

	function SendPress_Screen_Options(){
		add_filter('set-screen-option', array(&$this,'set_screen_options'), 10, 3);

	}
	function set_screen_options($status, $option, $value) {
 		if ( 'sendpress_emails_per_page' == $option ) return $value;
 		if ( 'sendpress_reports_per_page' == $option ) return $value;
 		if ( 'sendpress_lists_per_page' == $option ) return $value;
 		if ( 'sendpress_subscribers_per_page' == $option ) return $value;
 		if ( 'sendpress_queue_per_page' == $option ) return $value;		
	}

}

}