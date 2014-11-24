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
		if(isset($_GET['eid'])){
			$email_id = base64_decode( $_GET['eid'] );
		}
		// If there's a subscriber ID in the URL, we need to get the subscriber object from it to use for the str_replace below.
		if(isset($_GET['sid'])) {
			$subscriber_id = base64_decode( $_GET['sid'] );
		} else {
			$subscriber_id = 0;
		}
		//$post = get_post($email_id);
		$inline = false;
		if(isset($_GET['inline']) ){
			$inline = true;
		}
		SendPress_Email_Cache::build_cache_for_email( $email_id );
					
		$message = new SendPress_Email();
	   	$message->id( $email_id );
	   	$message->subscriber_id( $subscriber_id );
	   	$message->list_id( 0 );
	   	$body = $message->html();
		
	   	//print_r( $body );
	   	unset($message);

		echo $body; 
		//echo SendPress_Template::get_instance()->render_html(false, true, $inline );
	}

	function page_start(){

	}
	
	function page_end(){

	}

}