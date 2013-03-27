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
		if( $options['notifications-subscribed-daily'] ){
			//get info

		}

	}

	function build_notification($data){

	}

	function send_instant_notification($event_data){

		$options = SendPress_Option::get('notification_options');
		
		if( $options['notifications-'.$event_data['type'].'-instant'] ){
			//build the message and send it....
			$list = SendPress_Data::get_list_details($event_data['urlID']);
			$sub = SendPress_Data::get_subscriber($event_data['subscriberID']);
			
			$verbage = 'from';
			if( $event_data['type'] === 'subscribed' ){
				$verbage = 'to';
			}

			$body = $text = $sub->email.' has '.$event_data['type'].' '.$verbage.' your list '.$list->post_title.'.';

			SendPress_Manager::send($options['email'], 'SendPress Instant Notification', $body, $text);
		}

	}

	function daily_notification_check(){
		$options = SendPress_Option::get('notification_options');

		if ( false === ( $sendpress_monthly_check = get_transient( 'sendpress_monthly_check' ) ) ) {
			
			if( intval(date('j')) === 1 ){
				//check for the option
				if( $options['notifications-subscribed-monthly'] ){
					//get subscribers for for the last month
					$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'subscribed');
					$body = $text = 'You had '.$count.' new subscribers last month.';
					SendPress_Manager::send($options['email'], 'SendPress Monthly Notification', $body, $text);
				}
				if( $options['notifications-unsubscribed-monthly'] ){
					$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'unsubscribed');
					$body = $text = 'You had '.$count.' people un-subscribe last month.';
					SendPress_Manager::send($options['email'], 'SendPress Monthly Notification', $body, $text);
				}

				set_transient( 'sendpress_monthly_check', true, MONTH_IN_SECONDS );
			}
			
		}

		if ( false === ( $sendpress_weekly_check = get_transient( 'sendpress_weekly_check' ) ) ) {
			// It wasn't there, so regenerate the data and save the transient
			if( date('w') === get_option('start_of_week', 0) ){
				if( $options['notifications-subscribed-weekly'] ){
					$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week")),date('Y-m-d'),'subscribed');
					$body = $text = 'You had '.$count.' new subscribers last week.';
					SendPress_Manager::send($options['email'], 'SendPress Weekly Notification', $body, $text);
				}
				if( $options['notifications-unsubscribed-weekly'] ){
					$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week")),date('Y-m-d'),'unsubscribed');
					$body = $text = 'You had '.$count.' people unsubscribe last week.';
					SendPress_Manager::send($options['email'], 'SendPress Weekly Notification', $body, $text);
				}

				set_transient( 'sendpress_weekly_check', true );
			}
			
		}

		if( $options['notifications-subscribed-daily'] ){

		}
		if( $options['notifications-unsubscribed-daily'] ){
			
		}

		//SendPress_Notifications_Manager::maybe_send_notification('daily');
	}
	// function sendpress_notification_weekly(){
	// 	SendPress_Notifications_Manager::maybe_send_notification('weekly');
	// }
	// function sendpress_notification_monthly(){
	// 	SendPress_Notifications_Manager::maybe_send_notification('monthly');
	// }

	function _init(){

		$options = SendPress_Option::get('notification_options');

		if ( ! wp_next_scheduled( 'sendpress_notification_daily' ) && ($options['notifications-subscribed-daily'] || $options['notifications-unsubscribed-daily']) ) {
			wp_schedule_event( time(), 'daily', 'sendpress_notification_daily' );
		}
		// if ( ! wp_next_scheduled( 'sendpress_notification_weekly' ) && ($options['notifications-subscribed-weekly'] || $options['notifications-unsubscribed-weekly']) ) {
		// 	wp_schedule_event( time(), 'weekly', 'sendpress_notification_weekly' );
		// }
		// if ( ! wp_next_scheduled( 'sendpress_notification_monthly' ) && ($options['notifications-subscribed-monthly'] || $options['notifications-unsubscribed-monthly']) ) {
		// 	wp_schedule_event( time(), 'monthly', 'sendpress_notification_monthly' );
		// }

		add_action( 'sendpress_notification_daily', array( $this, 'daily_notification_check' ) );
		// add_action( 'sendpress_notification_weekly', array( $this, 'subscribed_weekly' ) );
		// add_action( 'sendpress_notification_monthly', array( $this, 'subscribed_monthly' ) );
	}

}

