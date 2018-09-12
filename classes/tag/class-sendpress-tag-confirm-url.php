<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Confirm_Url extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
			$code = array(
					"id"=> $subscriber_id,
					"report"=> $email_id,
					"view"=>"confirm"
			);
			$code = SendPress_Data::encrypt( $code );

			if( SendPress_Option::get('old_permalink') || !get_option('permalink_structure') ){
				$link = home_url() ."?sendpress=".$code;
			} else {
				$link = home_url() ."/sendpress/".$code;
			}
			
			return $link;
	}

	static function copy(){
		$return = '{sp-confirm-url}';
        return $return;
	}

}