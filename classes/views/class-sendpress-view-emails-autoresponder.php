<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}



class SendPress_View_Emails_Autoresponder extends SendPress_View_Emails{

	function html(){
		echo 'buy pro for autoresponders goes here';
	}

}

