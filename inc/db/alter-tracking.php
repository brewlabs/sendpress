<?php
global $wpdb;
/*
$table_to_update = SendPress_Table_Manager::subscriber_click_table();
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'ip'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `ip`  varchar(400) DEFAULT NULL");
}

if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'devicetype'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `devicetype`  varchar(50) DEFAULT NULL");
}
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'device'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `device`  varchar(50) DEFAULT NULL");
}
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'clickedat'") == true) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." change clickedat date DATETIME");
}


$table_to_update = SendPress_Table_Manager::subscriber_open_table();
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'ip'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `ip`  varchar(400) DEFAULT NULL");
}
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'devicetype'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `devicetype`  varchar(50) DEFAULT NULL");
}
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'device'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `device`  varchar(50) DEFAULT NULL");
}
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'openat'") == true) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." change openat date DATETIME");
}
*/
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$subscriber_events_table =  SendPress_DB_Tables::subscriber_event_table();
if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
	  $sqltable = "CREATE TABLE ".$subscriber_events_table." (
	  `eventID` int(11) unsigned NOT NULL AUTO_INCREMENT,
	  `subscriberID` int(11) unsigned NOT NULL,
	  `reportID` int(11) unsigned NOT NULL,
	  `urlID` int(11) unsigned DEFAULT NULL,
	  `eventdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `ip`  varchar(400) DEFAULT NULL,
	  `devicetype`  varchar(50) DEFAULT NULL,
	  `device`  varchar(50) DEFAULT NULL,
	  `type` varchar(50) DEFAULT NULL,
	  PRIMARY KEY (`eventID`),
	  KEY `subscriberID` (`subscriberID`),
	  KEY `reportID` (`reportID`),
	  KEY `urlID` (`urlID`),
	  KEY `eventdate` (`eventdate`),
	  KEY `type` (`type`)
	)"; 
	dbDelta($sqltable); 
}
