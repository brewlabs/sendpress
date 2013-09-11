<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Queue extends SendPress_Public_View {
	
	function page_start(){}

	function page_end(){}

	function html() {
		$x = SendPress_Data::fetch_queue_for_iron();

		echo json_encode($x);
	}

}