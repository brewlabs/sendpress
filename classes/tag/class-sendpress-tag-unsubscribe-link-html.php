<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Unsubscribe_Link_Html extends SendPress_Tag_Base{

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
		
		return "<a href='{sp-unsubscribe-url}'>" .__('Unsubscribe from this list.','sendpress') ."</a>";
	}

	static function copy(){
		$return = '{sp-unsubscribe-link-html}';
        return $return;
	}

}