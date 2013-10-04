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
        static function stop(){
        $upload_dir = wp_upload_dir();
        $filename = $upload_dir['basedir'].'/sendpress.pause';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }

    static function start(){
        $upload_dir = wp_upload_dir();
        $filename = $upload_dir['basedir'].'/sendpress.pause';
        if (file_exists($filename)) {
            unlink($filename);
        } 
    }

    static function iron_url($url){
        return  parse_url($url);
        
    }


    static function use_iron_cron(){
        
        $url =  SendPress_Manager::public_url('send');
        $info = self::iron_url( $url );
        $domain = base64_encode($info['host']);
        $xpath = $info['path'];
       
        if(isset($info['query']) ){
             $xpath .= "?".$info['query'];
        }
         $path = base64_encode(  $xpath );
        //echo $url;
        error_log('http://sendpress.com/iron/cron/add/'. $domain .'/'. $path);
        $body = wp_remote_retrieve_body( wp_remote_get( 'http://sendpress.com/iron/cron/add/'. $domain .'/'.$path ) );
        wp_clear_scheduled_hook( 'sendpress_cron_action' );
    }

    function remove_http($url) {
   $disallowed = array('http://', 'https://');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}

    function disable_iron_cron(){

    }
}







