<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails_Templates') ){


class SendPress_View_Emails_Templates extends SendPress_View_Emails{

	function admin_init(){
		add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
	}

	function screen_options(){

		$screen = get_current_screen();
	 	
		$args = array(
			'label' => __('Emails per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_emails_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

 	

	function prerender($sp= false){
	
	

	}
	
	function html($sp){
	
	
	}

}



SendPress_Admin::add_cap('Emails_Templates','sendpress_email');

}