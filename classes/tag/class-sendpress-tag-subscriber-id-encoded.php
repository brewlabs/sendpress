<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Subscriber_Id_Encoded extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		return str_replace( '=', '', base64_encode( $subscriber_id ) );
	}

	static function copy(){
		$return = '{sp-subscriber-id-encoded}';
        return $return;
	}

}