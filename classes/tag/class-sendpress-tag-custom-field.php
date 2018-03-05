<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Custom_Field extends SendPress_Tag_Base {

	
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){

		//get_subscriber_meta($subscriber_id =false, $meta_key =false, $list_id = false, $multi = false)
		$tag = str_replace("spcf-","", $e);

		return Sendpress_Data::get_subscriber_meta($subscriber_id, $tag, false, false );
	}
	
}