<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Debug{

	/*
    hard coding this so I can run through these pages 
    and look at the language support.  for some reason 
    when pig latin is active, get_current_screen doesn't 
    return the per_page array
    */
	function piglatin_per_page_fix($per_page){
		if( is_plugin_active('piglatin/piglatin.php') ){
           return 10;
        }
        return $per_page;
	}

}