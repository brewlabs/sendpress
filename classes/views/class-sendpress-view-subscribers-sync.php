<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Sync extends SendPress_View_Subscribers {

	function save(){
		
	}
	
	function html($sp) {
		if(isset($_GET['listID'])){
			//$listinfo = $this->getDetail( $this->lists_table(),'listID', $_GET['listID'] );	
			$listinfo = get_post($_GET['listID']);
			$list = '&listID='.$_REQUEST['listID'];
			$listname = 'for '. $listinfo->post_title;
		}
		$role = get_post_meta($_GET['listID'],'sync_role',true);
		$blogusers = get_users( 'role=' . $role );
		$email_list = array();
		echo "<h2>Sync WordPress users ". $listname."</h2>";
		foreach ($blogusers as $user) {
			SendPress_Data::update_subscriber_by_email( $user->user_email , array('wp_user_id'=>$user->ID,'firstname'=>$user->first_name,'lastname'=>$user->last_name) );
        	$email_list[] = $user->user_email;
       	}
       	echo "<p>Synced ". count($blogusers) . " users.</p>";
       	echo "<p>All users not in role <b>".$role."</b> where removed from this list.";
       	echo "<p>Your list is now up to date</p>";
       	echo "<a class='btn' href='".SendPress_Admin::link('Subscribers')."'>Back to Lists</a> <a href='".SendPress_Admin::link('Subscribers_Subscribers',array('listID'=>$_GET['listID'])) ."' class='btn'>View Subscribers</a>";
    	SendPress_Data::sync_emails_to_list( $_GET['listID'] , $email_list );

	}
 
}