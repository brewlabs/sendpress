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
		//$hash = wp_hash( $info->url , 'sendpress' );

		$db_url = SPNL()->db->url;



		
		$url_in_db = $db_url->get( $info->url );  //= SendPress_Data::get_url_by_hash( $hash );
		
		if ( $url_in_db == false ) {
			$id = $db_url->add( $info->url );
		} else {
			$id = $url_in_db;
		}

		$dk = SendPress_Data::devicetypes( $this->_device_type );
		
		$url = $info->url;
		switch($info->url){
			case '{sp-browser-url}':
				$url = SPNL()->template_tags->do_subscriber_tags( SendPress_Tag_Browser_Link::external( $info->url, $info->report, $info->report , $info->id, false ), $info->report, $info->report, $info->id, false );
			break;
			case '{sp-unsubscribe-url}':
				$url = SPNL()->template_tags->do_subscriber_tags( SendPress_Tag_Unsubscribe::external( $info->url, $info->report, $info->report , $info->id, false ), $info->report, $info->report, $info->id, false );
			break;

		}

		


		SPNL()->db->subscribers_url->add_update( array('subscriber_id'=> $info->id, 'email_id' => $info->report, 'url_id' => $id  ) );

		SPNL()->db->subscribers_tracker->open( $info->report , $info->id , 2 );

		if(strrpos( $href, "mailto" ) === false){
				header("Location: $url");
		} else {
			wp_redirect( $url );
		}
		exit;

	
		//
		//$link = SendPress_Data::get_url_by_id( $info->urlID );
		//SendPress_Data::track_click( $info->id , $info->report, $info->urlID , $ip , $this->_device_type, $this->_device );
		//header("Location: " . $link->url);
		//wp_redirect( $link->url ); 
		
		//exit;
	}

}