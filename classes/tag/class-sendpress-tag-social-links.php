<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Social_Links extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external( $template_id , $email_id , $subscriber_id , $example);
		if( $return != '' ){
			return $return;
		}
        return '';
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		$links = SendPress_Data::build_social(  );
		if( $links != '' && $links  != false ){
			return  $links;
		}
		return '';
	}

	static function copy(){
		$return = '{sp-social-links}';
        return $return;
	}

}