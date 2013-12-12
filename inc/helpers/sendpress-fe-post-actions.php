<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}
/**
*
*   SENDPRESS FRONT END ACTIONS 
*   
*   see @sendpress class line 101
*   Handles saving data and other user actions.
*
**/

global $sendpress_show_thanks, $sendpress_signup_error;

switch ( $this->_current_action ) {

	case 'signup-user':

		$sendpress_show_thanks = false;
		$sendpress_signup_error = "There was an error signing up, please try again.";

        $s = NEW SendPress;

        $first = isset($_POST['first']) ? $_POST['first'] : '';
        $last = isset($_POST['last']) ? $_POST['last'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $listid = isset($_POST['list']) ? $_POST['list'] : '';

        $custom = apply_filters('sendpress_subscribe_to_list_custom_fields',array(), $_POST);

        $success = SendPress_Data::subscribe_user($listid, $email, $first, $last, 2, $custom);

        //need to do stuff with the form on the page
        //var_dump($success);

        if( (bool)$success ){
        	$sendpress_signup_error = "";
        	$sendpress_show_thanks = true;
        }

    break;

}