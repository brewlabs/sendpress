<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Open extends SendPress_Public_View{
	
	function page_start(){}

	function page_end(){}

	function html(){
		$ip = $_SERVER['REMOTE_ADDR'];
		$info = $this->data();
		$link = SendPress_Data::track_open($info->id , $info->report , $ip , $this->_device_type, $this->_device );
		header('Content-type: image/gif'); 
		//include(SENDPRESS_PATH. 'img/clear.gif'); 	
	}

}