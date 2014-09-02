<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Unsubscribe extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		$link = SendPress_Manager::public_url('unsubscribe');
		return $link .'{sp-subscriber-id}';
	}

	static function copy(){
		$return = '{sp-unsubscribe}';
        return $return;
	}

}