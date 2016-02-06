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

		SPNL()->load("Subscribers_Tracker")->open( $info->report , $info->id );
	
		//Get the filesize of the image for headers
		$filesize = filesize( SENDPRESS_PATH . 'img/icon.png' );

		//Now actually output the image requested (intentionally disregarding if the database was affected)
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Cache-Control: public ',false );
		header('Content-type: image/png'); 
		header( 'Content-Length: '.$filesize );
		echo file_get_contents( SENDPRESS_PATH . 'img/icon.png' );
	}

}