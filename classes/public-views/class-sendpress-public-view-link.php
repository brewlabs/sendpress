<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Link extends SendPress_Public_View{
	
	function page_start(){}

	function page_end(){}

	function html($sp) {

		$ip = $_SERVER['REMOTE_ADDR'];

		$info = $this->data();
		
		$link = $sp->get_url_by_id($info->urlID);

		$sp->track_click( $info->id , $info->report, $info->urlID , $ip , $this->_device_type, $this->_device );
		
		wp_redirect( $link->url ); 
		
		exit;
	
		/*

		$sp->track_click( $info->id , $info->report, $info->urlID , $ip  );

		$link = get_query_var('spurl');

		if( get_query_var('fxti') &&  get_query_var('spreport') ){


		$this->register_click(get_query_var('fxti'), get_query_var('spreport'), $link);

		}

		



		header( 'Location: '.$link ) ;
		*/
	}

}