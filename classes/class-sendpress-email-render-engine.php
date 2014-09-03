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

	function render_example_by_id( $post_id ){
		return self::render_example( get_post( $post_id ) );
	}

	function render_template_example( $post ){
		$return = spnl_do_content_tags( $post->post_content, $post->ID, $post->ID, 0, true );
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

	function render_template( $template_id, $email_id ){
			$temp = get_post( $template_id );
			
			return spnl_do_content_tags(  $temp->post_content, $template_id, $email_id, 0, false );
	}

	function render_old_template( $post_id ){
		

		return '';
	}	

}

