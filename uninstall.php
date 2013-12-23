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
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
	exit();
}

global $wpdb, $wp_roles;

define( 'SENDPRESS_VERSION', '0.9.6.3' );
//Remove settings
/*
delete_option( 'sendpress_options' );
delete_option( 'sendpress_db_version' );


require_once plugin_dir_path( __FILE__ )  . 'classes/class-sendpress-pro-manager.php';
SendPress_Pro_Manager::try_deactivate_key();



$sp_post_types = array( 'sp_newsletters', 'sp_report', 'sptemplates', 'sendpress_list' );
foreach ( $sp_post_types as $post_type ) {

	$items = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1, 'fields' => 'ids' ) );

	if ( $items ) {
		foreach ( $items as $item ) {
			delete_transient('sendpress_report_subject_' . $item );
			delete_transient('sendpress_report_body_html_' . $item );
			wp_delete_post( $item, true);
		}
	}
}

//Drop All DB tables
//This could use an updated for Multisite
require_once plugin_dir_path( __FILE__ )  . 'classes/class-sendpress-db-tables.php';
SendPress_DB_Tables::remove_all_data();
*/