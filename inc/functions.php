<?php

/**
 * 	Function used by or with SendPress
 */
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

global $sendpress_sender_factory;
$sendpress_sender_factory = new SendPress_Sender_Factory();

function sendpress_register_sender( $classname ){
	global $sendpress_sender_factory;
	$sendpress_sender_factory->register($classname);
}

function sendpress_unregister_sender( $classname ){
	global $sendpress_sender_factory;
	$sendpress_sender_factory->unregister($classname);
}



/**
 * 
 * 
 *  array('path'=>SENDPRESS_PATH . '/templates/original.html' , 'name' => 'SP Original'  )
 */
global $sendpress_html_templates;
$sendpress_html_templates = array();
function sendpress_register_template($html_template = array()){
	global $sendpress_html_templates;
	$id = SendPress_Data::get_html_template_id_by_slug( sanitize_title_for_query( $html_template['name'] ) );
	//$id = 0;
	echo $id;
	$content = file_get_contents($html_template['path']);
	$my_post = array(
		'ID'           	=> $id,
		'post_content' 	=> $content,
		'post_title' 	=> $html_template['name'],
		'post_status' 	=> 'draft'
	);

	//print_r( $my_post );
	// Update the post into the database
	wp_update_post( $my_post );

	//$html_template['ID'] = $id;
	//$sendpress_html_templates[$id] = $html_template;
}
















/**
 * Private
 */
function _get_sendpress_id_base($id) {
	return preg_replace( '/-[0-9]+$/', '', $id );
}




if(!function_exists('sp_sort')) {
function sp_sort($a,$b){
	return strlen($a)-strlen($b);
}
}

if(!function_exists('sendpress_sort')) {
function sendpress_sort($a,$b){
    return strlen($a[0])-strlen($b[0]);
}
}


if(!function_exists('sp_glob')) {

function sp_glob($dir){
	$files = array();
	if(is_dir($dir)){
	    if($dh=opendir($dir)){
	    while(($file = readdir($dh)) !== false){
	    	if( end(explode('.',basename($file))) == 'php' ){
	        	$files[]=$dir.$file;
	    	}
	    }}
	}
	return $files;
}

}



/**
 * Check if the web server is running on Apache
 * @return bool
 */
function spnl_is_apache() {
	if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stristr( $_SERVER['SERVER_SOFTWARE'], 'apache' ) !== false ) {
		return true;
	}
	return false;
}

/**
 * Check if the web service is running on Nginx
 *
 * @return bool
 */
function spnl_is_nginx() {
	if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && stristr( $_SERVER['SERVER_SOFTWARE'], 'nginx' ) !== false ) {
		return true;
	}
	return false;
}



function _spnl_die_handler() {
	if ( defined( 'SPNL_UNIT_TESTS' ) )
		return '_spnl_die_handler';
	else
		die();
}
/**
 * Wrapper function for wp_die(). This function adds filters for wp_die() which
 * kills execution of the script using wp_die(). This allows us to then to work
 * with functions using spnl_die() in the unit tests.
 *
 */
function spnl_die( $message = '', $title = '', $status = 400 ) {
	add_filter( 'wp_die_ajax_handler', '_spnl_die_handler', 10, 3 );
	add_filter( 'wp_die_handler', '_spnl_die_handler', 10, 3 );
	wp_die( $message, $title, array( 'response' => $status ));
}


define('SENDPRESS_PRO_VALID', 'valid');
define('SENDPRESS_PRO_DEACTIVATED', 'deactivated');
define('SENDPRESS_PRO_FAILED', 'failed');
