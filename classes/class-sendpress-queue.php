<?php
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


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
class SendPress_Queue extends SendPress_Base {

function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

static function send_mail(){
		global $wpdb;
		$attempted_count = SendPress_Option::get('autocron-per-call',25);
		
		if( SendPress_Manager::limit_reached( $attempted_count )  ){
			return;
		}

		$count = SendPress_Data::emails_in_queue();
		$email_count = 0;
		$attempts = 0;
		
		SendPress_Email_Cache::build_cache();

		if($count > 0 ){
		for ($i=0; $i < $attempted_count; $i++) { 
				$email = SendPress_Data::get_single_email_from_queue();
				SendPress_Queue::send_the_queue($email);
				
		}		
		
		}

		
	}


	static function send_the_queue($email){
			if( $email != null ) {

				$tracker_disable =  SendPress_Option::get( 'tracker_off', false);
				$user_tracker = true;
				if( $tracker_disable == "true" ){
					$user_tracker = false;
				}
			
				global $wpdb;
						if( is_email( trim($email->to_email) ) && ( isset($email->is_confirm) || SendPress_Data::is_subscriber_active_or_unconfirmed($email->subscriberID ) )  ){
							SendPress_Data::queue_email_process( $email->id );
							$result = SendPress_Manager::send_email_from_queue( $email , $user_tracker);
						
							if ($result) {
								if($result == true){
									$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
									SPNL()->load("Subscribers_Tracker")->add( array('subscriber_id' => intval( $email->subscriberID ), 'email_id' => intval( $email->emailID) ) );
								} else {
									$wpdb->update( SendPress_Data::queue_table() , array('success'=>2,'inprocess'=>3 ) , array('id'=> $email->id ));
									$tfd = print_r($result, true);
									SPNL()->log->add(  'Odd Return from send_email_from_queue ' . $email->to_email , $tfd  , 0 , 'error' );
									if($result != false) {
										SendPress_Data::bounce_subscriber_by_id( $email->subscriberID );
									}
								}
							} else {
								$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
							}
						} else {
							SPNL()->log->add(  'Email not on list ' . $email->to_email , '', 0 , 'error' );
							$wpdb->update( SendPress_Data::queue_table() , array('success'=>2,'inprocess'=>3 ) , array('id'=> $email->id ));
							SendPress_Data::bounce_subscriber_by_id( $email->subscriberID );
						}
					} 
	}




	static function send_mail_cron(){
		//@ini_set('max_execution_time',0);
		global $wpdb;
		$count = SendPress_Option::get('emails-per-hour');
		$count = SendPress_Option::get('wpcron-per-call',25);
		$user_tracker = true;
		$tracker_disable =  SendPress_Option::get( 'tracker_off', false);
		
		if( $tracker_disable =="true" ){
			$user_tracker = false;
		}
		$email_count = 0;
		$attempts = 0;

		if( SendPress_Manager::limit_reached( $count )  ){
			return;
		}
		SendPress_Email_Cache::build_cache();


		for ($i=0; $i < $count ; $i++) { 
				$email = SendPress_Data::get_single_email_from_queue();
				if($email != null){

					
					$attempts++;
				
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email , $user_tracker );
					$email_count++;
					if ($result) {
						if($result === true){
							$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
							//( $sid, $rid, $lid=null, $uid=null, $ip=null, $device_type=null, $device=null, $type='confirm' )
							//$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
							//$wpdb->insert(SendPress_Data::subscriber_tracker_table() , array('subscriberID'=>$email->subscriberID,'emailID'=>$email->emailID,'sent_at' => get_gmt_from_date( date('Y-m-d H:i:s') )) );
							SPNL()->load("Subscribers_Tracker")->add( array('subscriber_id' => intval( $email->subscriberID ), 'email_id' => intval( $email->emailID) ) );
							SendPress_Data::add_subscriber_event($email->subscriberID, $email->emailID, $email->listID,null,null,null,null, 'send');
						} else {
							$wpdb->update( SendPress_Data::queue_table() , array('success'=>2,'inprocess'=>3 ) , array('id'=> $email->id ));
							SendPress_Data::add_subscriber_event($email->subscriberID, $email->emailID, $email->listID,null,null,null,null, 'bounce');
							SendPress_Data::bounce_subscriber_by_id( $email->subscriberID );
						}

						//$wpdb->insert( $this->subscriber_open_table(),  $senddata);
						$count++;
						//SendPress_Data::update_report_sent_count( $email->emailID );
					} else {
						$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
					}
				} else{//We ran out of emails to process.
					break;
				}
				set_time_limit(30);
		}


		return;


		
		

		
	}

}