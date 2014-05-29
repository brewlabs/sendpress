<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
/**
 * Shortcode Base file
 *
 * 
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Base {

	/**
	*
	*	Overide to set the title
	*
	*/
	public static function title() {
		return false;
	}

	/**
	*
	*	Overide to set the title
	*
	*/
	public static function html() {
		return false;
	}

	/**
	*
	*	Overide to set the docs
	*
	*/
	public static function docs() {
		return false;
	}
	
	/**
	*
	*	Overide to set default options
	*
	*/
	public static function options() {
		return 	array();
	}

	/**
	*
	* Overide to set default output
	*
	* @param array $atts
	*/
	public static function output( $atts ) {
		return "";
	}

	/**
	*
	*	Overide for form post
	*
	*/
	public static function form_post(){
		return false;
	}

}
