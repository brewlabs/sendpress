<?php
/*
*
* SendPress Required Class: SendPress_Table_Manager
* Sort Order: 1
*/
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
* @package  SendPress
* @author   Josh Lyford
* @since 	0.8.6
*/
class SendPress_DB_Tables {

    static $db_version = "1";
	static $prefix = 'sendpress_';			


    
    /**
     * subscriber_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function subscriber_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers";
	}

    
    /**
     * list_subcribers_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function list_subcribers_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix  . "list_subscribers";
	}

    /**
     * lists_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function lists_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "lists";
	}

    /**
     * subscriber_status_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function subscriber_status_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers_status";
	}

    /**
     * subscriber_event_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function subscriber_event_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers_event";
	}

    /**
     * subscriber_click_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function subscriber_click_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers_click";
	}

    /**
     * subscriber_open_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function subscriber_open_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers_open";
	}

    /**
     * report_url_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function report_url_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "report_url";
	}

    /**
     * queue_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function queue_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "queue";
	}


    function remove_all_data(){
        if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
            return;
        }
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // drop Stats Table
        $st = self::subscriber_table();
        $qt = self::queue_table();
        $rt = self::report_url_table();
        $so = self::subscriber_open_table();
        $se = self::subscriber_event_table();
        $sc = self::subscriber_click_table();
        $ss = self::subscriber_status_table();
        $lt = self::lists_table();
        $ls = self::list_subcribers_table();



        $drop_tables = "DROP TABLE IF EXISTS $st,$qt,$rt,$so,$se,$sc,$ss,$lt,$ls;";
        $e = $wpdb->query($drop_tables);
    }


}

