<?php
// SendPress Required Class: SendPress_Pro_Manager
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_Notifications_Manager
*
* @uses     
*
* @package  SendPress
* @author   Jared Harbour
* @license  See SENPRESS
* @since 	0.9.2.3     
*/
class SendPress_Notifications_Manager {

	function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Notifications_Manager;
			$instance->_init();
		}

		return $instance;
	}

	function maybe_send_notification($type){
		//based on the type, check the options and build an e-mail to notify the admin.


	}

	function sendpress_notification_daily(){
		SendPress_Notifications_Manager::maybe_send_notification('daily');
	}
	function sendpress_notification_weekly(){
		SendPress_Notifications_Manager::maybe_send_notification('weekly');
	}
	function sendpress_notification_monthly(){
		SendPress_Notifications_Manager::maybe_send_notification('monthly');
	}

	function _init(){

		$options = SendPress_Option::get('notification_options');

		if ( ! wp_next_scheduled( 'sendpress_notification_daily' ) ) {
			wp_schedule_event( time(), 'daily', 'sendpress_notification_daily' );
		}
		if ( ! wp_next_scheduled( 'sendpress_notification_weekly' ) ) {
			wp_schedule_event( time(), 'weekly', 'sendpress_notification_weekly' );
		}
		if ( ! wp_next_scheduled( 'sendpress_notification_monthly' ) ) {
			wp_schedule_event( time(), 'monthly', 'sendpress_notification_monthly' );
		}

		add_action( 'sendpress_notification_daily', array( $this, 'subscribed_daily' ) );
		add_action( 'sendpress_notification_weekly', array( $this, 'subscribed_weekly' ) );
		add_action( 'sendpress_notification_monthly', array( $this, 'subscribed_monthly' ) );

		SendPress_Notifications_Manager::maybe_send_notification('instant');

	}

}

