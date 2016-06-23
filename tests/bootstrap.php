<?php

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( "sendpress/sendpress.php" ),
);

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';
require_once $_tests_dir . '/includes/functions.php';
function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../sendpress.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';


$result = activate_plugin( 'sendpress/sendpress.php' );
if ( is_wp_error( $result ) ) {
	// Process Error
	print_r($result);
}

echo "Installing SendPress...\n";

// Install SendPress
SendPress::plugin_activation();

global $current_user;

$current_user = new WP_User(1);
$current_user->set_role('administrator');
wp_update_user( array( 'ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User' ) );
