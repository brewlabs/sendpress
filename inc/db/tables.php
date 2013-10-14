<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
global $wpdb;

// Create Stats Table
$subscriber_table = SendPress_DB_Tables::subscriber_table();

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
$wpdb->flush();
if($wpdb->get_var("show tables like '$subscriber_table'") != $subscriber_table) {
	$sql2 = "CREATE TABLE ".$subscriber_table." (
		  subscriberID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  email varchar(100) NOT NULL DEFAULT '',
		  join_date datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
		  status int(1) NOT NULL DEFAULT '1',
		  registered datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
		  registered_ip varchar(20) NOT NULL DEFAULT '',
		  identity_key varchar(60) NOT NULL DEFAULT '',
		  bounced int(1) NOT NULL DEFAULT '0',
		  firstname varchar(250) NOT NULL DEFAULT '',
		  lastname varchar(250) NOT NULL DEFAULT '',
		  wp_user_id bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`subscriberID`),
		  UNIQUE KEY  (`email`) ,
		  UNIQUE KEY (`identity_key`),
		  UNIQUE KEY `wp_user_id` (`wp_user_id`)
		)"; 
	

	dbDelta($sql2); 	
}

$subscriber_list_subscribers = SendPress_DB_Tables::list_subcribers_table();
$wpdb->flush();
if($wpdb->get_var("show tables like '$subscriber_list_subscribers'") != $subscriber_list_subscribers) {



	$sql3 = "CREATE TABLE ".$subscriber_list_subscribers." (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `listID` int(11) DEFAULT NULL,
		  `subscriberID` int(11) DEFAULT NULL,
		  `status` int(1) DEFAULT NULL,
		  `updated` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
		  PRIMARY KEY (`id`),
		  KEY (`listID`) ,
		  KEY (`subscriberID`) ,
		  KEY (`status`) ,
		  UNIQUE KEY `listsub` (`subscriberID`,`listID`)
		)";

	dbDelta($sql3);
}

$subscriber_queue = SendPress_DB_Tables::queue_table();
$wpdb->flush();
if($wpdb->get_var("show tables like '$subscriber_queue'") != $subscriber_queue) {
	$sql5 = "CREATE TABLE ".$subscriber_queue." (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `subscriberID` int(11) DEFAULT NULL,
	  `listID` int(11) DEFAULT NULL,
	  `from_name` varchar(64) DEFAULT NULL,
	  `from_email` varchar(128) NOT NULL,
	  `to_email` varchar(128) NOT NULL,
	  `subject` varchar(255) NOT NULL,
	  `messageID` varchar(400) NOT NULL,
	  `emailID` int(11) NOT NULL,
	  `max_attempts` int(11) NOT NULL DEFAULT '3',
	  `attempts` int(11) NOT NULL DEFAULT '0',
	  `success` tinyint(1) NOT NULL DEFAULT '0',
	  `date_published` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `inprocess` int(1) DEFAULT '0',
	  `last_attempt` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
	  `date_sent` datetime  NOT NULL DEFAULT '0000-00-00 00:00:00',
	  PRIMARY KEY (`id`),
	  KEY `to_email` (`to_email`),
	  KEY `subscriberID` (`subscriberID`),
	  KEY `listID` (`listID`),
	  KEY `inprocess` (`inprocess`),
	  KEY `success` (`success`),
	  KEY `max_attempts` (`max_attempts`),
	  KEY `attempts` (`attempts`),
	  KEY `last_attempt` (`last_attempt`)
	)";

	dbDelta($sql5);
}

add_option("sendpress_db_version", SendPress_DB_Tables::$db_version);