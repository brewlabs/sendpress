<?php
// SendPress Required Class: SendPress_Signup_Shortcode

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Security{
	private $_adminpage = array('sp','sp-overview','sp-reports','sp-emails','sp-templates','sp-subscribers','sp-settings','sp-queue','sp-pro','sp-help');

	function page($page){
		if( in_array($page, $this->_adminpage) ){
			return $page;
		} else {
			return false;
		}
	}

	function int( $int ){
		$int = intval( $int );
		if( $int > 0 ){
			return $int;
		}
		return 0;
	}	


	function hex($colorCode) {
	    // If user accidentally passed along the # sign, strip it off
	    $colorCode = ltrim($colorCode, '#');

	    if (ctype_xdigit($colorCode) && (strlen($colorCode) == 6 || strlen($colorCode) == 3)){
	               return '#'.$colorCode;
	    }
	    return '#000000';
	}

}

