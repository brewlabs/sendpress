<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Footer_Content extends SendPress_Tag_Base  {

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external($template_id , $email_id, $subscriber_id , $example );
		if( $return != '' ){
			return self::table_start( $template_id  ) . $return . self::table_end( $template_id );
		}
        return '';
	}
	
	static function content(){
		return '{sp-social-links}';
	}

	static function external(  $template_id , $email_id , $subscriber_id, $example ){
		//if( $example == false ){
		$link = get_post_meta( $template_id ,'_footer_link_color',true );
			if($link == false ){
				$link = '#2469a0';
			}
			
			$content = get_post_meta( $email_id , '_footer_content' , true); 
			if(!$content){

				if( self::template_post_exists($template_id) ){
					$content = get_post_meta( $template_id , '_footer_content' , true); 
				} else {
					$content = self::content();
				}
			}
			//$content = $content_post->post_content;
			//remove_filter('the_content','wpautop');
			add_filter( 'bj_lazy_load_run_filter', '__return_false' );
			$content = apply_filters('the_content', $content);
			//add_filter('the_content','wpautop');
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = spnl_do_email_tags($content ,$template_id , $email_id , $subscriber_id, $example  );
			$content = SendPress_Template::link_style($link, $content);
			
		/*
		} else {
			$content = self::lipsum_format();
		}
		*/
		 if($content != ''){
		 	return self::table_start( $template_id ) . $content . self::table_end( $template_id );
		 }
		 return '';
	}

	static function copy(){
		$return =  '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">';
        $return .= '{footer-content}';
        $return .='</td></tr></table>';
        return $return;
	}

	
	
	
	static function table_start( $template_id ){
		$cl  = '';
		$htext = get_post_meta( $template_id ,'_footer_text_color',true );
		if($htext == false ){
			$htext = '#333';
		}
		$bgtext = get_post_meta( $template_id ,'_footer_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#e2e2e2';
        }

        $padding = get_post_meta( $template_id ,'_footer_padding',true );
        $pd = '';
        if( $padding == 'pad-footer'  ){
        	 $pd = ' padding-left: 30px; padding-right: 30px; padding-top: 15px;';
        	 $cl = ' container-padding ';
    	}
    	$return ='';
		$return .='<!-- 600px container Header - SendPress_Tag_Header_Content-->';
	    $return .='<table border="0" width="600" cellpadding="0" cellspacing="0" class="container sp-style-f-bg" bgcolor="'.$bgtext.'">';
	    $return .='<tr>';
	    $return .='<td class="' . $cl . ' sp-style-f-bg" bgcolor="'.$bgtext.'" style="background-color: '.$bgtext.'; '.$pd.' font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: '.$htext.';" align="left">';
	    return $return;
	}
	static function table_end($template_id){
		$return ='';
		$return .='</td>';
	    $return .='</tr>';
	    $return .='</table>';
	    return $return;
	}

}