<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails_Tempedit') ){


	class SendPress_View_Emails_Tempedit extends SendPress_View_Emails{

		function admin_init(){
			add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
/*
wp_register_script('sendpress_codemirror', SENDPRESS_URL .'codemirror/lib/codemirror.js' ,'',SENDPRESS_VERSION);
wp_enqueue_script('sendpress_codemirror');
wp_register_script('sendpress_codemirror_mode', SENDPRESS_URL .'codemirror/mode/htmlmixed/htmlmixed.js' ,'',SENDPRESS_VERSION);
wp_enqueue_script('sendpress_codemirror_mode');
wp_register_style( 'sendpress_codemirror_css', SENDPRESS_URL . 'codemirror/lib/codemirror.css', '', SENDPRESS_VERSION );
wp_enqueue_style( 'sendpress_codemirror_css' );
*/
}

function save(){

	$template = get_post($_POST['post_ID']);
	$template->post_content = stripcslashes($_POST['template-content'] );
	$template->post_title = $_POST['post_subject'];
	wp_update_post( $template );

	SendPress_Admin::redirect('Emails_Tempedit', array('templateID'=>$_GET['templateID'] ));
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
	SendPress_Tracking::event('Emails Tab');

	
	
}

}



SendPress_Admin::add_cap('Emails_Tempedit','sendpress_email');

}