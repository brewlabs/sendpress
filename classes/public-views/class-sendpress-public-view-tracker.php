<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Tracker extends SendPress_Public_View {
	
	function page_start(){  }

	function page_end(){}

	function html() {

		//$ip = $_SERVER['REMOTE_ADDR'];

		$info = $this->data();
		print_r( $info );
		echo "<br>";
		$hash = wp_hash( $info->url , 'sendpress' );
		$url_in_db = SendPress_Data::get_url_by_hash( $hash );
		print_r( $url_in_db );
		echo "<br>";
		if ( $url_in_db == null ) {
			$id = SendPress_Data::insert_url( $info->url , $hash );
		} else {
			$id = $url_in_db->urlID;
		}

		echo "<br>";
		echo $id;
		$dk = SendPress_Data::devicetypes( $this->_device_type );
		//print_r( );
		print_r($this->_device);

		//$link = SendPress_Data::get_url_by_id( $info->urlID );
		//SendPress_Data::track_click( $info->id , $info->report, $info->urlID , $ip , $this->_device_type, $this->_device );
		//header("Location: " . $link->url);
		//wp_redirect( $link->url ); 
		
		//exit;
	}

}