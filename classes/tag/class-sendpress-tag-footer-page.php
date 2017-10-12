<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Footer_Page extends SendPress_Tag_Base  {

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external( $template_id ,$email_id, $subscriber_id , $example );
		if( $return != '' ){
			return self::table_start() . $return . self::table_end( $template_id );
		}
        return '';
	}
	
	static function external(  $template_id , $email_id , $subscriber_id, $example ){
		//if( $example == false ){
		
			if( self::template_post_exists($template_id) ){
				$content = get_post_meta( $template_id , '_footer_page' , true); 
			} else {
				$content = '';//self::content();
			}
			//$content = $content_post->post_content;
			$link = get_post_meta( $template_id ,'_header_page_link_color',true );
			if($link == false ){
				$link = '#2469a0';
			}

			//$content = SendPress_Template::link_style($link, $content);
			add_filter( 'bj_lazy_load_run_filter', '__return_false' );
			remove_filter('the_content','wpautop');
			$content = apply_filters('the_content', $content);
			add_filter('the_content','wpautop');
			$content = nl2br(str_replace(']]>', ']]&gt;', $content));
			
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

	static function content( $system = false ){

		if( $system == true ){
			return "<br><!--page footer -->";
		}
		$display_correct = __("Is this email not displaying correctly?","sendpress");
		$view = __("View it in your browser","sendpress");
		$unsubscribe =  __("unsubscribe from this list","sendpress");
		return '<br>' . $display_correct . ' <a href="{sp-browser-url}">'.$view.'</a><br><br><a href="{sp-unsubscribe-url}">'. $unsubscribe . '</a><br>';
	}
	

	static function copy(){
		$return =  '<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td align="left">';
        $return .= '{footer-page}';
        $return .='</td></tr></table>';
        return $return;
	}

	static function table_start( $template_id ){
		$htext = get_post_meta( $template_id ,'_header_page_text_color',true );
		if($htext == false ){
			$htext = '#333';
		}

		
		
		$return ='';
        $padding = get_post_meta( $template_id ,'_header_padding',true );
        $pd = '';
        if( $padding == 'pad-page'  ){
        	 $pd = ' padding-left: 30px; padding-right: 30px; ';
    	}
    	$return .='<table border="0" width="100%" class="sp-body-bg" cellpadding="0" cellspacing="0">';
    	$return .='<tr>';
      	$return .='<td align="center" valign="top">';
		$return .='<!-- 600px container Header - SendPress_Tag_Footer_Page-->';
	    $return .='<table border="0" width="600" cellpadding="0" cellspacing="0" class="container " >';
	    $return .='<tr>';
	    $return .='<td class="container-padding page-text-color" style="'.$pd.' font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: '.$htext.';" align="left">';
	   
	    return $return;
	}
	static function table_end( $template_id ){
		$return ='';
		$return .='</td>';
	    $return .='</tr>';
	    $return .='</table>';
		$return .='</td>';
	    $return .='</tr>';
	    $return .='</table>';
	    return $return;
	}
}