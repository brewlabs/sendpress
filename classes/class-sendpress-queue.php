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


static function send_mail(){
		global $wpdb;
		$day_count = 25;

		if( !SendPress_Manager::limit_reached()  ){
		$count= SendPress_Data::emails_in_queue();
		$email_count = 0;
		$attempts = 0;
		if($count > 0 ){
		for ($i=0; $i < $day_count; $i++) { 
				$email = SendPress_Data::get_single_email_from_queue();
				if($email != null){
					if( SendPress_Manager::limit_reached()  ){
						break;
					}
					$attempts++;
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email );
					$email_count++;
					if ($result) {
						$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
						$senddata = array(
							'sendat' => date('Y-m-d H:i:s'),
							'reportID' => $email->emailID,
							'subscriberID' => $email->subscriberID
						);

						SendPress_Data::update_report_sent_count( $email->emailID );
					} else {
						$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
					}
				} 
				
				
		}
		}


		
		}


		
		

		
	}


	static function send_mail_cron(){
		//@ini_set('max_execution_time',0);
		global $wpdb;
		$count = SendPress_Option::get('emails-per-hour');
		$count = $count;



		if( SendPress_Manager::limit_reached()  ){
			return;
		}


		for ($i=0; $i < $count ; $i++) { 
				$email = SendPress_Data::get_single_email_from_queue();
				if($email != null){

					if( SendPress_Manager::limit_reached()  ){
						break;
					}
					$attempts++;
				
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email );
					$email_count++;
					if ($result) {
						$wpdb->update( SendPress_Data::queue_table() , array('success'=>1,'inprocess'=>3 ) , array('id'=> $email->id ));
						$senddata = array(
							'sendat' => date('Y-m-d H:i:s'),
							'reportID' => $email->emailID,
							'subscriberID' => $email->subscriberID
						);

						//$wpdb->insert( $this->subscriber_open_table(),  $senddata);
						$count++;
						SendPress_Data::update_report_sent_count( $email->emailID );
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