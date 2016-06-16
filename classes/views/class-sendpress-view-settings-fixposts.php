<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Fixposts extends SendPress_View_Settings {
	
	function view_buttons(){
		//$this->security_check();
	}

	function posts_repair(){
		//$this->security_check();
		SendPress_Data::delete_extra_posts();
		SendPress_Admin::redirect('Settings_Fixposts');
	}

	function html() {
		$count =  SendPress_Data::get_bad_post_count();
		echo __("We see","sendpress")." ". $count." ". __("bad posts.","sendpress");
		$link = SendPress_Admin::link('Settings_Fixposts',array('action'=>'posts-repair'));
		echo "<br><br><a href='$link' class='btn btn-primary' >".__('Attempt to Delete These','sendpress')."</a>";


		/*
		echo "<h2>Attempting to install or repair missing data</h2><br>";

		SendPress_Data::install();

		echo "<pre>";
		echo SendPress_DB_Tables::check_setup_support();
		echo "</pre>";
		*/
	}

}