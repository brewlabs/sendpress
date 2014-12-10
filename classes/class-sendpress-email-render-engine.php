<?php
// SendPress Required Class: SendPress_Template

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_Render_Engine
*
* @uses     
*
* 
* @package  SendPress
* @author   Josh Lyford
* @license  See SENPRESS
* @since 	0.9.9.9.3     
*/
class SendPress_Email_Render_Engine {

	static function render_example_by_id( $post_id ){
		return self::render_example( get_post( $post_id ) );
	}

	static function render_template_example( $post ){
		$html = self::render_html_base_by_post( $post );
		$return = spnl_do_content_tags( $html, $post->ID, $post->ID, 0, true );
		$return = spnl_do_email_tags( $return, $post->ID, $post->ID, 0, true );
		$return = spnl_do_subscriber_tags( $return, $post->ID, $post->ID, 0, true );
		
		//$body_html = preg_replace( $pattern , site_url() ."?sendpress=link&fxti=".$subscriber_key."&spreport=". $this->id ."&spurl=$0", $body_html );
			if(class_exists("DomDocument")){
				$dom = new DomDocument();
				$dom->strictErrorChecking = false;
				@$dom->loadHtml($return);
				
				$pTags = $dom->getElementsByTagName('p');
				foreach ($pTags as $pElement) {
					$px = $pElement->getAttribute('style');
					$pElement->setAttribute('style', $px .' margin-top:0;margin-bottom:10px;');
				}
				$return =  $dom->saveHtml();
			}

		return $return;
	}

	static function render_template( $template_id, $email_id ){
		
			$html = self::render_html_base_by_id($template_id);

			return spnl_do_content_tags(  $html , $template_id, $email_id, 0, false );
	}

	static function render_html_base_by_id( $id ){
			$temp_post = get_post( $id );
			if($temp_post !== null){
				if($temp_post->post_status == 'sp-custom'){
					return $temp_post->post_content;
				}


				$temp = json_decode( $temp_post->post_content );
			 

				if( $temp === false || $temp === null){
					$path = SENDPRESS_PATH.'templates/v1-0/master.html';
				} else {
					$path = $temp->path;
				}
				$html = file_get_contents($path);
				return $html;
			}
			$path = SENDPRESS_PATH.'templates/v1-0/master.html';
			$html = file_get_contents($path);
			return $html;

	}

	static function render_html_base_by_post( $post ){
			
			$temp = json_decode( $post->post_content );
			if( is_object($temp) ){
				$path = $temp->path;
			} else {
				$path = SENDPRESS_PATH.'templates/v1-0/master.html';
				
			}
			$html = file_get_contents($path);
			return $html;
	}

	function render_old_template( $post_id ){
		

		return '';
	}	

}

