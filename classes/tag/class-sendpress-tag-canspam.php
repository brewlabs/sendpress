<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Canspam extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external( $template_id , $email_id, $subscriber_id , $example );
		if( $return != '' ){
			return $return;
		}
        return '';
	}
	
	

	static function external( $template_id , $email_id, $subscriber_id , $example ) {
		$canspam = SendPress_Option::get('canspam');
		if( $canspam != '' && $canspam  != false ){
			return  nl2br($canspam);
		}
		return '';
	}

	static function copy(){
		//$return =  '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">';
        $return = '{canspam}';
       
        return $return;
	}

}