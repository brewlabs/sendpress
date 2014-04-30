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
* @since 	0.9.5.2     
*/
class SendPress_Notifications_Manager {

	static function &init() {
		static $instance = false;

		if ( !$instance ) {
			$instance = new SendPress_Notifications_Manager;
			$instance->_init();
		}

		return $instance;
	}

	static function maybe_send_notification($type = 'daily', $data = false){
		$options = SendPress_Option::get('notification_options');
		$subscribed = SendPress_Notifications_Manager::build_subscribed_notification($data);
		$unsubscribed = SendPress_Notifications_Manager::build_unsibscribed_notification($data);
		
		if( strlen($subscribed) === 0 && strlen($unsubscribed) === 0 ){
			return;
		}

		$body = $subscribed . $unsubscribed;
		$text = $subscribed . $unsubscribed;
		SendPress_Notifications_Manager::send_notification($body, $text);
	}

	static function build_subscribed_notification($data){
		$subscribe_body = '';
		$options = SendPress_Option::get('notification_options');
		//subscribed check
		switch($options['subscribed']){
			case 0:
				if( $data && $data['type'] === 'subscribed' ){
					$list = SendPress_Data::get_list_details($data['listID']);
					$sub = SendPress_Data::get_subscriber($data['subscriberID']);			

					$subscribe_body = $sub->email.' has subscribed to your list "'.$list->post_title.'".';
				}
				
				break;
			case 1:
				$count = SendPress_Data::get_subscriber_event_count_day(date('Y-m-d'),'subscribed');
				$subscribe_body = 'You had '.$count.' new subscribers today.';

				break;
			case 2:
				if ( false === ( $sendpress_weekly_check = get_transient( 'sendpress_weekly_subscribed_check' ) ) ) {
					// It wasn't there, so regenerate the data and save the transient
					if( date('w') === get_option('start_of_week', 0) ){
						
						$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week -1 day")),date('Y-m-d', strtotime(date('Y-m-d')." +1 day")),'subscribed');
						$subscribe_body = 'You had '.$count.' new subscribers last week.';
					
						set_transient( 'sendpress_weekly_subscribed_check', true, WEEK_IN_SECONDS );
					}
					
				}
				break;
			case 3:
				if ( false === ( $sendpress_monthly_check = get_transient( 'sendpress_monthly_subscribed_check' ) ) ) {
					if( intval(date('j')) === 1 ){
						
						//get subscribers for for the last month
						$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'subscribed');
						$subscribe_body = 'You had '.$count.' new subscribers last month.<br><br>';
					
						set_transient( 'sendpress_monthly_subscribed_check', true, MONTH_IN_SECONDS );
					}
				}
				break;
		}

		return $subscribe_body;
	}

	static function build_unsibscribed_notification($data){
		$unsubscribe_body = '';

		$options = SendPress_Option::get('notification_options');

		switch($options['unsubscribed']){
			case 0:
				if( $data && $data['type'] === 'unsubscribed' ){
					
					$list = SendPress_Data::get_list_details($data['listID']);
					$sub = SendPress_Data::get_subscriber($data['subscriberID']);			

					$unsubscribe_body = $sub->email.' has unsubscribed from your list "'.$list->post_title.'".';
				}
				break;
			case 1:
				$count = SendPress_Data::get_subscriber_event_count_day(date('Y-m-d'),'unsubscribed');
				$unsubscribe_body = 'You had '.$count.' users unsubscribe today.';
				break;
			case 2:
				if ( false === ( $sendpress_weekly_check = get_transient( 'sendpress_weekly_unsubscribed_check' ) ) ) {
					// It wasn't there, so regenerate the data and save the transient
					if( date('w') === get_option('start_of_week', 0) ){
					
						$count = SendPress_Data::get_subscriber_event_count_week(date('Y-m-d', strtotime(date('Y-m-d')." -1 week")),date('Y-m-d'),'unsubscribed');
						$unsubscribe_body = 'You had '.$count.' people unsubscribe last week.';
					
						set_transient( 'sendpress_weekly_unsubscribed_check', true, WEEK_IN_SECONDS );
					}
				}
				break;
			case 3:
				if ( false === ( $sendpress_monthly_check = get_transient( 'sendpress_monthly_unsubscribed_check' ) ) ) {
					if( intval(date('j')) === 1 ){
						//get subscribers for for the last month
						$count = SendPress_Data::get_subscriber_event_count_month(date('j', strtotime(date('j')." -1 month")),'unsubscribed');
						$unsubscribe_body = 'You had '.$count.' people unsubscribe last month.<br><br>';
					
						set_transient( 'sendpress_monthly_unsubscribed_check', true, MONTH_IN_SECONDS );
					}
				}
				break;
		}

		return $unsubscribe_body;
	}

	static function send_instant_notification($data){

		$options = SendPress_Option::get('notification_options');

		if( $options['notifications-enable'] ){
			$subscribed = '';
			$unsubscribed = '';

			if( intval($options['subscribed']) === 0 ){
				//build instant subscribed
				$subscribed = SendPress_Notifications_Manager::build_subscribed_notification($data);
			}

			if( intval($options['unsubscribed']) === 0 ){
				//build instant subscribed
				$unsubscribed = SendPress_Notifications_Manager::build_unsibscribed_notification($data);
			}

			if( strlen($subscribed) === 0 && strlen($unsubscribed) === 0 ){
				return;
			}
			$body = $subscribed.$unsubscribed;
			$text = $subscribed.$unsubscribed;
		
			SendPress_Notifications_Manager::send_notification($body,$text);
		}
		
	}

	static function send_notification($body = "Possible Error With Notifications",$text= "Possible Error With Notifications"){
		$options = SendPress_Option::get('notification_options');

		if( strlen($options['email']) > 0 ){
			$senders[] = $options['email'];
		}

		if( $options['send-to-admins'] ){
			$admins = new WP_User_Query( array( 'role' => 'Administrator' ) );
			foreach ( $admins->results as $user ) {
				if( $user->user_email !== $options['email'] ){
					$senders[] = $user->user_email;
				}
			}
		}

		if( $options['notifications-enable'] ){
			if(is_array($senders) && !empty($senders)) {
				foreach($senders as $to){
					SendPress_Manager::send($to, 'SendPress Notification', $body, $text);
				}
			}
		}
		
		//hipchat
		if( $options['enable-hipchat'] && strlen($options['hipchat-api']) > 0 ){
			global $hc;
			$hc = new SendPress_HipChat($options['hipchat-api'], 'https://api.hipchat.com');

			try{
				foreach ($hc->get_rooms() as $room) {
					if( $options['hipchat-rooms'][$room->room_id] ){
						$hc->message_room($room->name, 'SendPress', $text, true, "purple", "text");
					}
				}
			}catch(Exception $e){
				$hc->message_room($options['hipchat-room'], 'SendPress', $text, true, "purple", "text");
			}
			
		}
	}

	function _init(){

		$options = SendPress_Option::get('notification_options');

		if( is_array($options) ){
			if ( ! wp_next_scheduled( 'sendpress_notification_daily' ) && ($options['subscribed'] > 0 || $options['unsubscribed'] > 0) ) {
				wp_schedule_event( time(), 'daily', 'sendpress_notification_daily' );
			}
		}
		
		add_action( 'sendpress_notification_daily', array( 'SendPress_Notifications_Manager', 'maybe_send_notification' ) );
	}

}
