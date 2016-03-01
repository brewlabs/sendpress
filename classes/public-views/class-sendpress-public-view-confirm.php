<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Confirm extends SendPress_Public_View{
	function prerender(){
		$ip = $_SERVER['REMOTE_ADDR'];
		//print_r($info);
		$info = $this->data();
		if(isset($info->id)){

			$lists = SendPress_Data::get_list_ids_for_subscriber($info->id);
			
			//$lists = explode(',',$info->listids);
			foreach ($lists as $list) {
				$status = SendPress_Data::get_subscriber_list_status( $list->listID, $info->id );
				if( $status->statusid == 1 ) {				
					SendPress_Data::update_subscriber_status($list->listID, $info->id, '2');

					$event_data = array(
						'eventdate'=>SendPress_Data::gmdate(),
						'subscriberID' => $info->id,
						'listID'=>$list->listID,
						'type'=>'subscribed'
					);
					SendPress_Notifications_Manager::send_instant_notification($event_data);

				}
			}
			SPNL()->load("Subscribers_Tracker")->open( $info->report , $info->id , 4);
		}
		

		if(SendPress_Option::get('confirm-page') == 'custom' ){
			$page = SendPress_Option::get('confirm-page-id');
			if($page != false){
				$plink = get_permalink($page);
				if($plink != ""){
					wp_safe_redirect( esc_url_raw( $plink) );
					exit();
				}

			}


		}
		
	}


	function html(){
		

		?>
			
					<h1><?php _e('Thank you for signing up!','sendpress'); ?></h1>
					<p><?php _e("You're all set, and should start receiving emails soon.","sendpress"); ?></p>
				
			<?php 
	}

}