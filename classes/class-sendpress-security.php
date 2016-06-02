<?php
// SendPress Required Class: SendPress_Signup_Shortcode

/**
*
*	This should be the only place to get user inpur data $_GET and $_POST
*
*/

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Security{
	private $_adminpage = array('sp','sp-overview','sp-reports','sp-emails','sp-templates','sp-subscribers','sp-settings','sp-queue','sp-pro','sp-help');
	private $_orderby = array('firstname','lastname','email','join_date','lastsend','subject');
	private $_allowed_tags = array(
		'a' => array(
			'class' => array(),
			'href'  => array(),
			'rel'   => array(),
			'title' => array(),
		),
		'abbr' => array(
			'title' => array(),
		),
		'b' => array(),
		'blockquote' => array(
			'cite'  => array(),
		),
		'br' => array(),
		'cite' => array(
			'title' => array(),
		),
		'code' => array(),
		'del' => array(
			'datetime' => array(),
			'title' => array(),
		),
		'dd' => array(),
		'div' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'dl' => array(),
		'dt' => array(),
		'em' => array(),
		'h1' => array(),
		'h2' => array(),
		'h3' => array(),
		'h4' => array(),
		'h5' => array(),
		'h6' => array(),
		'i' => array(),
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
		'li' => array(
			'class' => array(),
		),
		'ol' => array(
			'class' => array(),
		),
		'p' => array(
			'class' => array(),
		),
		'q' => array(
			'cite' => array(),
			'title' => array(),
		),
		'span' => array(
			'class' => array(),
			'title' => array(),
			'style' => array(),
		),
		'strike' => array(),
		'strong' => array(),
		'ul' => array(
			'class' => array(),
		),
	);
	
	

	function page($page = false){
		if($page == false){
			$page = $this->_string('page');
		}
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

	function orderby($item){
		if(in_array($item, $this->_orderby)){
			return $item;
		}
		return '';
	}

	//Basic wrapper to move checks out of main code.
	function _isset( $field ){
		if( isset($_GET[$field]) || isset($_POST[$field]) ){
			return true;
		}
		return false;
	}



	function _int($field){
		return $this->int($this->secure($field,'int'));
	}
	function _string($field){
		return $this->secure($field,'string');
	}
	function _email($field){
		$e = $this->secure($field,'email');
		if(is_email($e)){
			return $e;
		}
		return false;
	}

	function _html($field){
		$html = $this->secure($field,'html');
		return wp_kses($html, $this->_allowed_tags);
	}

	function _bool($field){
		$data = $this->secure($field,'string');
		if(function_exists('boolval')){
			return boolval($data);
		}
		return (bool) $data;
	}
	
	function secure($field,$type)
	{
		$action = 'none';
		if(isset($_GET[$field])){
			$action = 'get_';
		}
		if(isset($_POST[$field])){
			$action = 'post_';
		}
		$action .= $type;
		$output = null;



		switch($action)
		{
			case 'get_string':
				$output = filter_input(INPUT_GET, $field, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
			break;
			case 'get_int':
				$output = $this->int( filter_input(INPUT_GET, $field, FILTER_SANITIZE_NUMBER_INT) );
			break;
			case 'get_html':
				$output = filter_input(INPUT_GET, $field, FILTER_SANITIZE_MAGIC_QUOTES);
			break;
			case 'get_email':
				$output = $this->int( filter_input(INPUT_GET, $field, FILTER_SANITIZE_EMAIL) );
			break;

			case 'post_string':
				$output = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES); 
			break;
			case 'post_int':
				$output = filter_input(INPUT_POST, $field, FILTER_SANITIZE_NUMBER_INT); 
			break;
			case 'post_email':
				$output = filter_input(INPUT_POST, $field, FILTER_SANITIZE_EMAIL); 
			break;
			case 'post_html':
				$output = filter_input(INPUT_POST, $field, FILTER_SANITIZE_MAGIC_QUOTES);
			break;

			default:
			break;
		}
		return $output;
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

