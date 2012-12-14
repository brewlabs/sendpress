<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

global $wpdb;

// Create Stats Table
$table_to_create =  SendPress_DB_Tables::subscriber_status_table();

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($wpdb->get_var("show tables like '$subscriber_status_table'") != $table_to_create) {
	$sqltable = "CREATE TABLE ".$table_to_create." (
			  `statusid` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `status` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`statusid`)
			)"; 
	

	dbDelta($sqltable); 	


		$insert = "INSERT INTO ".$table_to_create." (`statusid`, `status`)
VALUES
	(1,'Unconfirmed'),
	(2,'Active'),
	(3,'Unsubscribed'),
	(4,'Bounced')";
	 
	$results = $wpdb->query( $insert );

}