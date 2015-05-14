<?php 

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

abstract class SendPress_Enum_Tracker_Type extends SendPress_Enum_Base {
    const Newsletter = 0;
    const Confirm = 1;
    const Manage = 2;
    const Automation = 3;
}