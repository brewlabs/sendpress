<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Public_View_Confirm extends SendPress_Public_View{
	

	function html($sp){
		$ip = $_SERVER['REMOTE_ADDR'];
		//print_r($info);
		$info = $this->data();
		if(isset($info->listids)){
			$lists = explode(',',$info->listids);
			foreach ($lists as $list_id) {
				$status = SendPress_Data::get_subscriber_list_status( $list_id , $info->id );
				if( !isset($status) || $status->status != '2' ) {				
					SendPress_Data::update_subscriber_status($list_id, $info->id, '2');
					SendPress_Data::add_subscriber_event( $info->id, $rid=NULL, $list_id, $ip , $this->_device_type, $this->_device );
				}
			}
		}

		if(SendPress_Option::get('confirm-page') == 'custom' ){
			$page = SendPress_Option::get('confirm-page-id');
			if($page != false){
				$plink = get_permalink($page);
				if($plink != ""){
					//echo $plink;
					wp_redirect($plink);
				}

			}


		}

		?>
			<div class="span12">
				<div class='area'>
					<h1><?php _e('Thank you for signing up!','sendpress'); ?></h1>
					<p><?php _e("You're all set, and should start receiving emails soon.","sendpress"); ?></p>
				</div>
			</div>
			<?php 
	}

}