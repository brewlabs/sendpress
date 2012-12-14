<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

global $wpdb;
$table_to_update = SendPress_DB_Tables::list_subcribers_table();
if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'updated'") == false) {
	$wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `updated` datetime DEFAULT NULL");
}