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
		return spnl_do_template_tags( $post->post_content, $post->ID, 0, true );
	}	

}

