<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Browser_Link_Html extends SendPress_Tag_Base{

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
		
		return __('Is this email not displaying correctly?','sendpress') ."
 <a href='{sp-browser-url}'>" .__('View it in your browser','sendpress') ."</a>";
	}

	static function copy(){
		$return = '{sp-browser-link-html}';
        return $return;
	}

}