<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Install extends SendPress_View_Settings {
	
	function events_repair(){
		//$this->security_check();
		SendPress_DB_Tables::repair_events_table();
		SendPress_Admin::redirect('Settings_Install');
	}	

	function html() {
		echo "<h2>". __('Attempting to install or repair missing data','sendpress') . "</h2><br>";

		SendPress_Data::install();
		@SPNL()->load("Subscribers_Tracker")->create_table();
		@SPNL()->load("Url")->create_table();
		@SPNL()->load("Subscribers_Url")->create_table();
		echo "<pre>";
		echo SendPress_DB_Tables::check_setup_support();
		echo "</pre>";

		}

}