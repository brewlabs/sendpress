<?php
// SendPress Required Class: SendPress_Option
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(class_exists('SendPress_Option')){ return; }


/**
* SendPress_Options
*
* @uses     
*
* 
* @package  SendPRess
* @author   Josh Lyford
* @license  See SENPRESS
* @since 	0.8.7     
*/
class SendPress_Option extends SendPress_Base {
	private static $key = 'sendpress_options';

    /**
     * get
     * 
     * @param mixed $name    Item to get out of SendPress option array.
     * @param mixed $default return value if option is not set.
     *
     * @access public
     *
     * @return mixed Value default or option value.
     */
	static function get( $name, $default = false ) {
		$options = get_option( self::$key );
		if ( is_array( $options ) && isset( $options[$name] ) ) {
			return is_array( $options[$name] )  ? $options[$name] : stripslashes( $options[$name] );
		}
		return $default;
	}	
	
	static function get_encrypted( $name , $default = false ) {
		$options = get_option( self::$key );
		if ( is_array( $options ) && isset( $options[$name] ) ) {
			return  self::_decrypt( $options[$name] ) ;
		}
		return $default;
	}

    /**
     * set
     * 
     * @param mixed $option String or Array of options to set.
     * @param mixed $value  if String name is passed us this to pass value to save.
     *
     * @access public
     *
     * @return bool Value success or failure of option save.
     */
	static function set($option, $value= null){
		$options = get_option( self::$key );
		
		//Set options with an array of values.
		if(is_array($option)){
			return update_option( self::$key, array_merge( $options, $option ) );
		}

		if ( !is_array( $options ) ) {
			$options = array();
		}
		$options[$option] = $value;
		return update_option( self::$key , $options );
	}




	static function set_encrypted($option, $value = null){
		$options = get_option( self::$key );

		
		$options[$option] = self::_encrypt( $value );

		return update_option(self::$key , $options);
	}	





	 /**
     * is_double_optin
     * 
     *
     * @access public
     *
     * @return bool 
     */
	static function is_double_optin(){
		if( SendPress_Option::get('send_optin_email') == 'yes'){
			return true;			
		}
		return false;
	}

	/**
     * use_theme_style
     * 
     *
     * @access public
     *
     * @return bool 
     */
    static function use_theme_style(){
		if( SendPress_Option::get('try-theme') == 'yes'){
			return true;			
		}
		return false;
	}

	
}
