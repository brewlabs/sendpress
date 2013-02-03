<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

global $wpdb;

// Create Stats Table
$subscriber_status_table =  SendPress_DB_Tables::subscriber_status_table();

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$wpdb->flush(); 

if($wpdb->get_var("show tables like '$subscriber_status_table'") != $subscriber_status_table) {
	$sqltable = "CREATE TABLE ".$subscriber_status_table." (
			  `statusid` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `status` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`statusid`)
			)"; 
	dbDelta($sqltable); 	
}

$unconfirmed = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 1) );
if ($unconfirmed != null) {
	$wpdb->update( 
		$subscriber_status_table, 
		array( 
			'status' => 'Unconfirmed',	// string
		), 
		array( 'statusid' => 1 ), 
		array( 
			'%s',	// value1
		), 
		array( '%d' ) 
	);

} else {
	$wpdb->insert( 
		$subscriber_status_table, 
		array( 
			'statusid' => 1, 
			'status' => 'Unconfirmed' 
		), 
		array( 
			'%d', 
			'%s' 
		) 
	);
}



$active = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 2) );
if ($active != null) {
	$wpdb->update( 
		$subscriber_status_table, 
		array( 
			'status' => 'Active',	// string
		), 
		array( 'statusid' => 2 ), 
		array( 
			'%s',	// value1
		), 
		array( '%d' ) 
	);

} else {
	$wpdb->insert( 
		$subscriber_status_table, 
		array( 
			'statusid' => 2, 
			'status' => 'Active' 
		), 
		array( 
			'%d', 
			'%s' 
		) 
	);
}


$unsubscribed = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 3) );
if ($unsubscribed != null) {
	$wpdb->update( 
		$subscriber_status_table, 
		array( 
			'status' => 'Unsubscribed',	// string
		), 
		array( 'statusid' => 3 ), 
		array( 
			'%s',	// value1
		), 
		array( '%d' ) 
	);

} else {
	$wpdb->insert( 
		$subscriber_status_table, 
		array( 
			'statusid' => 3, 
			'status' => 'Unsubscribed' 
		), 
		array( 
			'%d', 
			'%s' 
		) 
	);
}


$bounced = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 4) );
if ($bounced != null) {
	$wpdb->update( 
		$subscriber_status_table, 
		array( 
			'status' => 'Bounced',	// string
		), 
		array( 'statusid' => 4 ), 
		array( 
			'%s',	// value1
		), 
		array( '%d' ) 
	);

} else {
	$wpdb->insert( 
		$subscriber_status_table, 
		array( 
			'statusid' => 4, 
			'status' => 'Bounced' 
		), 
		array( 
			'%d', 
			'%s' 
		) 
	);
}
