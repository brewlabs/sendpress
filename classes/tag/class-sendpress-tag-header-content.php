<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Tag_Header_Content extends SendPress_Tag_Base  {

	static function internal( $template_id , $email_id, $subscriber_id , $example ) {
		$return = self::external( $template_id ,$email_id, $subscriber_id , $example );
		if( $return != '' ){
			return self::table_start() . $return . self::table_end( $template_id );
		}
        return '';
	}
	
	static function external(  $template_id , $email_id , $subscriber_id, $example ){
		//if( $example == false ){

			$content = get_post_meta( $email_id , '_header_content' , true); 
			if(!$content){
				if( self::template_post_exists($template_id) ){
					$content = get_post_meta( $template_id , '_header_content' , true); 
				} else {
					$content = self::content();
				}
			}

			// get_post_meta($email_id);
			//$content = $content_post->post_content;
			//remove_filter('the_content','wpautop');
			add_filter( 'bj_lazy_load_run_filter', '__return_false' );
			$content = apply_filters('the_content', $content);
			add_filter('the_content','wpautop');
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = spnl_do_email_tags($content ,$template_id , $email_id , $subscriber_id, $example  );
			

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
        $return .= '{header-content}';
        $return .='</td></tr></table>';
        return $return;
	}

	static function content(){
		return "<table cellpadding='0' cellspacing='0' width='100%'><tr><td><h1 style='text-align:center;'>{sp-site-name}</h1></td></tr></table>";	
	}

	static function table_start( $template_id ){
		$htext = get_post_meta( $template_id ,'_header_text_color',true );
		if($htext == false ){
			$htext = '#333';
		}
		$bgtext = get_post_meta( $template_id ,'_header_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#d1d1d1';
        }

        $padding = get_post_meta( $template_id ,'_header_padding',true );
        $pd = '';
        $cl = '';
        if( $padding == 'pad-header'  ){
        	 $pd = ' padding-left: 30px; padding-right: 30px; padding-top: 15px; padding-bottom:15px;';
        	 $cl = ' container-padding ';
    	}
    	$return ='';
		$return .='<!-- 600px container Header - SendPress_Tag_Header_Content-->';
	    $return .='<table border="0" width="600" cellpadding="0" cellspacing="0" class="container sp-style-h-bg" bgcolor="'.$bgtext.'">';
	    $return .='<tr>';
	    $return .='<td class="' . $cl . ' sp-style-h-bg" bgcolor="'.$bgtext.'" style="background-color: '.$bgtext.'; '.$pd.' font-size: 13px; line-height: 20px; font-family: Helvetica, sans-serif; color: '.$htext.';" align="left">';
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