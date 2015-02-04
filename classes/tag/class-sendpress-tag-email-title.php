<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Email_Title extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		$email_subject =  get_post_meta($email_id, '_sendpress_subject' ,true );
		if( $email_subject !== '' ){
			return $email_subject;
		}
		return get_the_title($email_id);
	}

	static function copy(){
		$return = '{sp-email-title}';
        return $return;
	}

}