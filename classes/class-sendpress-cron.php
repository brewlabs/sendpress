<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_Cron
*
* @uses     
*
* @package  SendPress
* @author   Josh Lyford
* @license  See SENPRESS
* @since 	0.8.8.5     
*/
class SendPress_Cron {
	private static $instance;
	
	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}

	function __construct(){
		  /* some processing for cron management */
        add_filter( 'cron_schedules', array( $this , 'cron_schedules' ) );
 
	}
	
    function cron_schedules( $param ) {
        $frequencies=array(
            'one_min' => array(
                'interval' => 60,
                'display' => __( 'Once every minutes', 'sendpress')
                ),
            'two_min' => array(
                'interval' => 120,
                'display' => __( 'Once every two minutes','sendpress')
                ),
            'five_min' => array(
                'interval' => 300,
                'display' => __( 'Once every five minutes','sendpress')
                ),
            'ten_min' => array(
                'interval' => 600,
                'display' => __( 'Once every ten minutes','sendpress')
                ),
            'fifteen_min' => array(
                'interval' => 900,
                'display' => __( 'Once every fifteen minutes','sendpress')
                ),
            'thirty_min' => array(
                'interval' => 1800,
                'display' => __( 'Once every thirty minutes','sendpress')
                ),
            'two_hours' => array(
                'interval' => 7200,
                'display' => __( 'Once every two hours','sendpress')
                ),
            'eachweek' => array(
                'interval' => 2419200,
                'display' => __( 'Once a week','sendpress')
                ),
            'each28days' => array(
                'interval' => 604800,
                'display' => __( 'Once every 28 days','sendpress')
                ),
            'monthly' => array(
                'interval' => 2419200,
                'display' => __( 'Once Monthly' )
                )
            );

        return array_merge($param, $frequencies);
    }
}







