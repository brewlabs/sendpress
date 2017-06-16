<?php
/**
 * SendPress: Simple Single Column
 * Regions: Main
 * Description: A killer default email template.
 *
 */
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

echo SendPress_Email_Render_Engine::render_customizer_base();
/*
$email_id = SPNL()->validate->_int(  'spemail'  );
		global $post;
		$post = get_post($email_id);
		$inline = false;
	
		if(isset($post) &&  $post->post_type == 'sp_template' ){
			//Render New Template Preview
			
			echo SendPress_Email_Render_Engine::render_template_example( $post );		
		} else {
			if(SPNL()->validate->_bool('inline')){
				$inline = true;
			}
			echo $post->post_content;
		}

*/