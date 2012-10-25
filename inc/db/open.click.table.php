<?php
global $wpdb;


// Create Stats Table
//$subscriber_open_table =  SendPress_Table_Manager::subscriber_open_table();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');


/*
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

if($wpdb->get_var("show tables like '$subscriber_open_table'") != $subscriber_open_table) {
	$sqltable = "CREATE TABLE ".$subscriber_open_table." (
  `openID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscriberID` int(11) DEFAULT NULL,
  `reportID` int(11) DEFAULT NULL,
  `messageID` varchar(400) NOT NULL,
  `sendat` datetime DEFAULT `0000-00-00 00:00:00`,
  `date` datetime DEFAULT `0000-00-00 00:00:00`,
  `count` int(11) DEFAULT NULL,
  `ip`  varchar(400) DEFAULT NULL,
  `devicetype`  varchar(50) DEFAULT NULL,
  `device`  varchar(50) DEFAULT NULL,
  PRIMARY KEY (`openID`),
  KEY `subscriberID` (`subscriberID`),
  KEY `reportID` (`reportID`),
  KEY `date` (`date`)
)"; 
	

	dbDelta($sqltable); 	

}

$subscriber_click_table =  SendPress_Table_Manager::subscriber_click_table();




if($wpdb->get_var("show tables like '$subscriber_click_table'") != $subscriber_click_table) {
	$sqltable = "CREATE TABLE ".$subscriber_click_table." (
  `clickID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscriberID` int(11) unsigned NOT NULL,
  `reportID` int(11) unsigned NOT NULL,
  `urlID` int(11) unsigned NOT NULL,
  `date` datetime DEFAULT `0000-00-00 00:00:00`,
  `count` int(11) unsigned NOT NULL DEFAULT '0',
  `ip`  varchar(400) DEFAULT NULL,
  `devicetype`  varchar(50) DEFAULT NULL,
  `device`  varchar(50) DEFAULT NULL,
  PRIMARY KEY (`clickID`),
  KEY `subscriberID` (`subscriberID`),
  KEY `reportID` (`reportID`),
  KEY `urlID` (`urlID`),
  KEY `date` (`date`)
)"; 
	

	dbDelta($sqltable); 	

}
*/


$subscriber_events_table =  SendPress_DB_Tables::subscriber_event_table();
if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
    $sqltable = "CREATE TABLE ".$subscriber_events_table." (
    `eventID` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `subscriberID` int(11) unsigned NOT NULL,
    `reportID` int(11) unsigned DEFAULT NULL,
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







$report_url_table =  SendPress_DB_Tables::report_url_table();


if($wpdb->get_var("show tables like '$report_url_table'") != $report_url_table) {
  $sqltable = "CREATE TABLE ".$report_url_table." (
  `urlID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(2000) DEFAULT '',
  `reportID` int(11) DEFAULT NULL,
  PRIMARY KEY (`urlID`),
  KEY `url` (`url`),
  KEY `reportID` (`reportID`)
)"; 
  

  dbDelta($sqltable);   

}