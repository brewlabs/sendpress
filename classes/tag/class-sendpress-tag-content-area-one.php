<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Content_Area_One extends SendPress_Tag_Base  {

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external( $template_id ,$email_id, $subscriber_id , $example );
		if( $return != '' ){
			return self::table_start() . $return . self::table_end();
		}
        return '';
	}
	
	static function external( $template_id ,  $email_id , $subscriber_id, $example ){
		if( $example == false ){
			$content_post = get_post($email_id);
			$content = $content_post->post_content;
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);


		} else {
			$content = self::lipsum_format();
		}
		$stat = get_post_status($template_id);
		$content = spnl_do_email_tags($content ,$template_id , $email_id , $subscriber_id, $example  );
	
		if($stat == 'sp-standard'){
			$link = get_post_meta( $template_id ,'_content_link_color',true );
			if($link == false ){
				$link = '#2469a0';
			}
			$content = SendPress_Template::link_style($link, $content);
		}
		return $content;
	}

	static function copy(){
		$return =  '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">';
        $return .= '{canspam}';
        $return .='</td></tr></table>';
        return $return;
	}

}