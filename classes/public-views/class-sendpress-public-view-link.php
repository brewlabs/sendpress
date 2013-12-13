<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Link extends SendPress_Public_View {
	
	function page_start(){  }

	function page_end(){}

	function html() {

		$ip = $_SERVER['REMOTE_ADDR'];

		$info = $this->data();
		$link = SendPress_Data::get_url_by_id( $info->urlID );
		SendPress_Data::track_click( $info->id , $info->report, $info->urlID , $ip , $this->_device_type, $this->_device );
		header("Location: " . $link->url);
		//wp_redirect( $link->url ); 
		
		exit;
	}

}