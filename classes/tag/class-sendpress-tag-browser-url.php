<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Browser_Url extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		/*
		$open_info = array(
			"id"=>$email_id,
			"view"=>"email"
		);
		$code = SendPress_Data::encrypt( $open_info );
		$xlink = SendPress_Manager::public_url($code);
		*/
		$link = SendPress_Manager::public_url('email');
		
		$email_key = str_replace( '=', '', base64_encode( $email_id ) );

		//Uses a known internal url - 4/20
		return add_query_arg(array('sid'=> '{sp-subscriber-id-encoded}', 'eid'=> $email_key), $link );

		return $xlink;
	}

	static function copy(){
		$return = '{sp-browser-url}';
        return $return;
	}

}