<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Install extends SendPress_View_Settings {
	
	

	function html($sp) {
		echo "<h2>Attempting to install missing data</h2><br>";

		SendPress_Data::install();

		echo "<pre>";
		echo SendPress_DB_Tables::check_setup_support();
		echo "</pre>";
	}

}