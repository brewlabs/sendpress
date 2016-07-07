<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Email extends SendPress_Public_View{
	function prerender(){
	
		
	}


	function html(){
		$email_id = get_query_var( 'spemail' );
		global $post;

		if( is_object($this->data()) &&  $this->data()->id){
			$email_id = $this->data()->id;
		} 
		$email_id_encoded = SPNL()->validate->_string('eid');
		if( !empty($email_id_encoded) ){
			$email_id = SPNL()->validate->int( base64_decode( $email_id_encoded ) );
		}
		// If there's a subscriber ID in the URL, we need to get the subscriber object from it to use for the str_replace below.
		$s_id_encoded = SPNL()->validate->_string('sid');
		if(!empty($s_id_encoded)) {
			$subscriber_id =  SPNL()->validate->int( base64_decode( $s_id_encoded ) );
		} else {
			$subscriber_id = 0;
		}
		//$post = get_post($email_id);
		$inline = false;
		if(SPNL()->validate->_bool('inline') ){
			$inline = true;
		}
		SendPress_Email_Cache::build_cache_for_email( $email_id );
					
		$message = new SendPress_Email();
	   	$message->id( $email_id );
	   	$message->subscriber_id( $subscriber_id );
	   	$message->list_id( 0 );
	   	$body = $message->html();
		
	   	//print_r( $body );
	   	unset( $message );

		echo $body; 
		//echo SendPress_Template::get_instance()->render_html(false, true, $inline );
	}

	function page_start(){

	}
	
	function page_end(){

	}

}