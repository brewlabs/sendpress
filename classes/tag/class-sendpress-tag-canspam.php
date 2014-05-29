<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Canspam extends SendPress_Tag_Base{

	static function internal( $email_id, $subscriber_id ) {
		$return = self::external( $email_id , $subscriber_id );
		if( $return != '' ){
			return self::table_start() . $return . self::table_end();
		}
        return '';
	}
	
	static function external(  $email_id , $subscriber_id  ){
		$canspam = SendPress_Option::get('canspam');
		if( $canspam != '' && $canspam  != false ){
			return wpautop( $canspam );
		}
		return '';
	}

	static function copy(){
		$return =  '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">';
        $return .= '{canspam}';
        $return .='</td></tr></table>';
        return $return;
	}

}