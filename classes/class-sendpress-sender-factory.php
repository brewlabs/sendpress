<?php
/**
 * Singleton that registers and instantiates SendPress_Sender classes.
 *
 * @package SendPress
 * @subpackage Senders
 * @since 0.8.8.5
 */

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Sender_Factory {
	var $senders = array();

	function __construct() {
		//add_action( 'senders_init', array( $this, '_register_senders' ), 100 );
	}

	function register($widget_class) {
		$this->senders[$widget_class] = new $widget_class();
	}

	function unregister($widget_class) {
		if ( isset($this->senders[$widget_class]) )
			unset($this->senders[$widget_class]);
	}

	function get_all_senders(){
		return $this->senders;
	}

	function get_sender( $sender ){
		if ( isset($this->senders[$sender]) ){
			return $this->senders[$sender];
		}
		return false;

	}

	/*
	function _register_senders() {
		global $sendpress_registered_senders;
		$keys = array_keys($this->senders);
		$registered = array_keys($sendpress_registered_senders);
		$registered = array_map('_get_widget_id_base', $registered);

		foreach ( $keys as $key ) {
			// don't register new widget if old widget with the same id is already registered
			if ( in_array($this->senders[$key]->id_base, $registered, true) ) {
				unset($this->senders[$key]);
				continue;
			}

			$this->senders[$key]->_register();
		}
	}
	*/
}