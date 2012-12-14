<?php
// SendPress Required Class: SendPress_Unsubscribe_Shortcode

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Unsubscribe_Shortcode{

	function load_form( $attr, $content = null ) {

	    ob_start();

	    extract(shortcode_atts(array(
			'firstname_label' => 'First Name'
		), $attr));

	    ?>
	    
	    <div class="unsubscribe">
	    	unsubscribe goes here...
	    </div>
	
	    <?php

	    $output = ob_get_contents();
	    ob_end_clean();
	    return $output;
	}

}

add_shortcode('sendpress-unsubscribe', array('SendPress_Unsubscribe_Shortcode','load_form'));