<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Manage_Subscriptions extends SendPress_Tag_Base{

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		return self::external( $template_id , $email_id , $subscriber_id , $example);
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id , $e ){
		//maybe saved link?
		$link_data = array(
			"id"=>$subscriber_id,
			"view"=>'manage'
		);

		//SendPress_Error::log($link_data);

		$code = SendPress_Data::encrypt( $link_data );
		$link = SendPress_Manager::public_url($code);



		if(SendPress_Option::get('manage-page') == 'custom' ){
			$page = SendPress_Option::get('manage-page-id');
			if($page != false){
				$plink = get_permalink($page);
				if($plink != ""){
					$link = $plink . '?spms='. $code;
				}
			}
		}

		return $link;
	}

	static function copy(){
		$return = '{sp-manage-subscription-url}';
        return $return;
	}

}