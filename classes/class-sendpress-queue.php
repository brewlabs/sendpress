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
		//@ini_set('max_execution_time',0);
		global $wpdb;
		$count = SendPress_Option::get('emails-per-hour');
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		$count = $count/12;

		if($emails_per_hour != 0){
			$rate = 3600 / $emails_per_hour;
		}
		if($rate > 8){
			$rate = 8;
		}
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;
		$attempts = 0;
		if( SendPress_Manager::send_limit_reached()  ){
			return;
		}


		for ($i=0; $i < $count ; $i++) { 
				$email = $wpdb->get_row("SELECT * FROM ". SendPress_Data::queue_table() ." WHERE success = 0 AND max_attempts != attempts AND inprocess = 0 ORDER BY id LIMIT 1");
				if($email != null){

					if( SendPress_Manager::send_limit_reached() ){
						break;
					}
					SendPress_Manager::increase_email_count( 1 );
					$attempts++;
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email );
					$email_count++;
					if ($result) {
						$table = SendPress_Data::queue_table();
						$wpdb->query( 
							$wpdb->prepare( 
								"DELETE FROM $table WHERE id = %d",
							    $email->id  
						    )
						);
						$senddata = array(
							'sendat' => date('Y-m-d H:i:s'),
							'reportID' => $email->emailID,
							'subscriberID' => $email->subscriberID
						);

						//$wpdb->insert( $this->subscriber_open_table(),  $senddata);
						SendPress_Data::update_report_sent_count( $email->emailID );
					} else {
						$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
					}
				

				} 
				 set_time_limit(30);
			}


		
		

		
	}


}