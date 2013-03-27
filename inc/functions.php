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



if( !defined('MINUTE_IN_SECONDS') ){
	//we aren't in WordPress 3.5, so lets add the constants they added so we can be cool too
	define( 'MINUTE_IN_SECONDS', 60 );
 	define( 'HOUR_IN_SECONDS',   60 * MINUTE_IN_SECONDS );
 	define( 'DAY_IN_SECONDS',    24 * HOUR_IN_SECONDS   );
	define( 'WEEK_IN_SECONDS',    7 * DAY_IN_SECONDS    );
 	define( 'YEAR_IN_SECONDS',  365 * DAY_IN_SECONDS    );
}

if( defined('DAY_IN_SECONDS') ){
	define( 'MONTH_IN_SECONDS',  28 * DAY_IN_SECONDS    );
}

define('SENDPRESS_PRO_VALID', 'valid');
define('SENDPRESS_PRO_DEACTIVATED', 'deactivated');
define('SENDPRESS_PRO_FAILED', 'failed');
