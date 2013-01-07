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


if(!function_exists('get_called_class')) {
    /**
     * get_called_class
     * Used to make plugin work with php 5.2
     * 
     * @param mixed $bt Description.
     * @param int   $l  Description.
     *
     * @access public
     *
     * @return mixed Value.
     */
	function get_called_class($bt = false,$l = 1) {
	    if (!$bt) $bt = debug_backtrace();
	    if (!isset($bt[$l])) throw new Exception("Cannot find called class -> stack level too deep.");
	    if (!isset($bt[$l]['type'])) {
	        throw new Exception ('type not set');
	    }
	    else switch ($bt[$l]['type']) {
	        case '::':
	            $lines = file($bt[$l]['file']);
	            $i = 0;
	            $callerLine = '';
	            do {
	                $i++;
	                $callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
	            } while (stripos($callerLine,$bt[$l]['function']) === false);
	            preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
	                        $callerLine,
	                        $matches);
	            if (!isset($matches[1])) {
	                // must be an edge case.
	                throw new Exception ("Could not find caller class: originating method call is obscured.");
	            }
	            switch ($matches[1]) {
	                case 'self':
	                case 'parent':
	                    return get_called_class($bt,$l+1);
	                default:
	                    return $matches[1];
	            }
	            // won't get here.
	        case '->': switch ($bt[$l]['function']) {
	                case '__get':
	                    // edge case -> get class of calling object
	                    if (!is_object($bt[$l]['object'])) throw new Exception ("Edge case fail. __get called on non object.");
	                    return get_class($bt[$l]['object']);
	                default: return $bt[$l]['class'];
	            }

	        default: throw new Exception ("Unknown backtrace method type");
	    }
	}
}