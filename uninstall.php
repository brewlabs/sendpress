<?php
/**
 * SendPress Uninstall Scripts
 *
 *
 * @package  SendPress
 * @author   Josh Lyford
 * @since  0.8.6
 */

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

define( 'SENDPRESS_VERSION', '0.8.8.1' );
//Remove settings
delete_option( 'sendpress_options' );
delete_option( 'sendpress_db_version' );

//Drop All DB tables
//This could use an updated for Multisite
require_once plugin_dir_path( __FILE__ )  . 'classes/class-sendpress-db-tables.php';
SendPress_DB_Tables::remove_all_data();
