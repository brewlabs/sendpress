<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Send extends SendPress_Public_View {
	
	function page_start(){}

	function page_end(){}

	function html() {
		SendPress_Queue::send_mail();
		$count= SendPress_Data::emails_in_queue();
		echo json_encode(array( "queue"=>$count ));
	}

}