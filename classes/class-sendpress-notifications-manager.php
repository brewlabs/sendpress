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
		
		if( isset($options['notifications-'.$event_data['type'].'-instant']) &&  $options['notifications-'.$event_data['type'].'-instant'] ){
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
				$subscribe_body = '';
				$unsubscribe_body = '';
				if( $options['notifications-subscribed-monthly'] ){
					//get subscribers for for the last month
					$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'subscribed');
					$subscribe_body = 'You had '.$count.' new subscribers last month.<br><br>';
					
				}
				if( $options['notifications-unsubscribed-monthly'] ){
					$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'unsubscribed');
					$unsubscribe_body = 'You had '.$count.' people unsubscribe last month.<br><br>';
				}

				if( isset($subscribe_body) || isset($unsubscribe_body) ){
					$body = $text = $subscribe_body.$unsubscribe_body;
					SendPress_Manager::send($options['email'], 'SendPress Monthly Notification', $body, $text);
					set_transient( 'sendpress_monthly_check', true, MONTH_IN_SECONDS );
				}
				
			}
			
		}

		if ( false === ( $sendpress_weekly_check = get_transient( 'sendpress_weekly_check' ) ) ) {
			// It wasn't there, so regenerate the data and save the transient
			if( date('w') === get_option('start_of_week', 0) ){
				$subscribe_body = '';
				$unsubscribe_body = '';
				if( $options['notifications-subscribed-weekly'] ){
					$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week -1 day")),date('Y-m-d', strtotime(date('Y-m-d')." +1 day")),'subscribed');
					$subscribe_body = 'You had '.$count.' new subscribers last week.';
				}
				if( $options['notifications-unsubscribed-weekly'] ){
					$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week")),date('Y-m-d'),'unsubscribed');
					$unsubscribe_body = 'You had '.$count.' people unsubscribe last week.';
				}

				if( isset($subscribe_body) || isset($unsubscribe_body) ){
					$body = $text = $subscribe_body.$unsubscribe_body;
					SendPress_Manager::send($options['email'], 'SendPress Weekly Notification', $body, $text);
					set_transient( 'sendpress_weekly_check', true, WEEK_IN_SECONDS );
				}
			}
			
		}

		//finally send the daily
		$subscribe_body = '';
		$unsubscribe_body = '';
		if( $options['notifications-subscribed-daily'] ){
			$count = SendPress_Data::get_subscriber_event_count_day(date('Y-m-d'),'subscribed');
			$subscribe_body = 'You had '.$count.' new subscribers today.';
		}
		if( $options['notifications-unsubscribed-daily'] ){
			$count = SendPress_Data::get_subscriber_event_count_day(date('Y-m-d'),'unsubscribed');
			$subscribe_body = 'You had '.$count.' users unsubscribe today.';
		}

		if( isset($subscribe_body) || isset($unsubscribe_body) ){
			$body = $text = $subscribe_body.$unsubscribe_body;
			SendPress_Manager::send($options['email'], 'SendPress Daily Notification', $body, $text);
		}

	}


	function _init(){

		$options = SendPress_Option::get('notification_options');

		if ( ! wp_next_scheduled( 'sendpress_notification_daily' ) && ($options['notifications-subscribed-daily'] || $options['notifications-unsubscribed-daily']) ) {
			wp_schedule_event( time(), 'daily', 'sendpress_notification_daily' );
		}

		add_action( 'sendpress_notification_daily', array( $this, 'daily_notification_check' ) );
	}

}

