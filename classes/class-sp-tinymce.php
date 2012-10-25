<?php
// SendPress Required Class: SendPress_TinyMCE

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_TinyMCE {

  	public function __construct(){
  		add_filter( 'tiny_mce_version', array(&$this, 'my_refresh_mce') );
		//add_action('init', array(&$this, 'add_button') );
  		$this->add_button();
  	}
   
	function add_button() {
	   // Don't bother doing this stuff if the current user lacks permissions
	   	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
	     	return;
	 
	   // Add only in Rich Editor mode
		if ( get_user_option('rich_editing') == 'true') {
	     	add_filter("mce_external_plugins", array(&$this, "add_tinymce_plugin") );
	     	add_filter('mce_buttons', array(&$this,'register_button') );
	   	}
	}

	function register_button($buttons) {
	   	array_push($buttons, "|", "mailmerge");
	   	return $buttons;
	}
	 
	// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
	function add_tinymce_plugin($plugin_array) {
	  	$plugin_array['mailmerge'] = SENDPRESS_URL.'js/mailmerge_plugin.js';
	   	return $plugin_array;
	}

	function my_refresh_mce($ver) {
	  	$ver += 3;
	  	return $ver;
	}

}


