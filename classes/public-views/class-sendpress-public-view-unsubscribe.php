<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Unsubscribe extends SendPress_Public_View {
	function prerender(){
		
		$r = SPNL()->validate->_string('rid');
		$s = SPNL()->validate->_string('sid');

		$r = (int) base64_decode($r);
		$s = (int) base64_decode($s);

		if(is_numeric($r)){	
			$lists =  get_post_meta($r,'_send_lists', true);
			$lists = explode(",", $lists);
			foreach ($lists as $list) {
				SendPress_Data::unsubscribe_from_list( $s , $r, $list );
			}
		}

			$link_data = array(
				"id"=>$s,
				"report"=>$r,
				"urlID"=> '0',
				"view"=>"manage",
				"listID"=>"0",
				"action"=>""
			);
			$code = SendPress_Data::encrypt( $link_data );

			if(SendPress_Option::get('unsubscribe-page') == 'custom' ){

				$page = SendPress_Option::get('unsubscribe-page-id');
				if($page != false){
					$plink = get_permalink($page);
					error_log($plink);
					if($plink != ""){
						$link = $plink . '?spms='. $code;
					}
				}

			} else {
				$link =  SendPress_Manager::public_url($code);
			}

			$this->redirect(  $link ); 
			exit;
		
	}

	function page_start(){}

	function page_end(){}

	function html(){}
		
		

}