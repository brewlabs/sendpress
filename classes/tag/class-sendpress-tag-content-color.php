<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Content_Color extends SendPress_Tag_Base  {

	static function internal( $template_id , $email_id, $subscriber_id , $example ,$x = false) {
		$return = self::external( $template_id ,$email_id, $subscriber_id , $example , $x);
		return $return;
	}
	
	static function external(  $template_id , $email_id , $subscriber_id, $example ,$x = false){
		//if( $example == false ){
			$color = get_post_meta( $template_id , '_content_color' , true); // get_post_meta($email_id);
			//$content = $content_post->post_content;
		    if( $color == false  ){
	        	 $color = '#ffffff';
	    	}
			return $color;
		
	}

}