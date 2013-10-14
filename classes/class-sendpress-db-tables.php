<?php
/*
*
* SendPress Required Class: SendPress_Table_Manager
* Sort Order: 1
*/
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

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
	static function subscriber_table(){
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
	static function list_subcribers_table(){
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
	static function lists_table(){
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
	static function subscriber_status_table(){
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
	static function subscriber_event_table(){
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
	static function subscriber_open_table(){
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
	static function report_url_table(){
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
	static function queue_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "queue";
	}


    static function check_setup(){
        global $wpdb;

        echo "<b>Database Tables</b>: <br>";
        $subscriber_events_table =  SendPress_DB_Tables::subscriber_event_table();
        if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
            echo $subscriber_events_table . " Not Installed<br>";
        } else {
             echo $subscriber_events_table . " OK<br>";
        }

        $report_url_table =  SendPress_DB_Tables::report_url_table();
        if($wpdb->get_var("show tables like '$report_url_table'") != $report_url_table) {
            echo $report_url_table . " Not Installed<br>";
        } else {
             echo $report_url_table . " OK<br>";
        }

        $subscriber_status_table =  SendPress_DB_Tables::subscriber_status_table();
        if($wpdb->get_var("show tables like '$subscriber_status_table'") != $subscriber_status_table) {
            echo $subscriber_status_table . " Not Installed<br>";
        } else {
             echo $subscriber_status_table . " OK<br>";
        }

        $subscriber_table = SendPress_DB_Tables::subscriber_table();
        if($wpdb->get_var("show tables like '$subscriber_table'") != $subscriber_table) {
            echo $subscriber_table . " Not Installed<br>";
        } else {
             echo $subscriber_table . " OK<br>";
        }

        $subscriber_list_subscribers = SendPress_DB_Tables::list_subcribers_table();
        if($wpdb->get_var("show tables like '$subscriber_list_subscribers'") != $subscriber_list_subscribers) {
            echo $subscriber_list_subscribers . " Not Installed<br>";
        } else {
             echo $subscriber_list_subscribers . " OK<br>";
        }

        $subscriber_queue = SendPress_DB_Tables::queue_table();
        if($wpdb->get_var("show tables like '$subscriber_queue'") != $subscriber_queue) {
            echo $subscriber_queue . " Not Installed<br>";
        } else {
             echo $subscriber_queue . " OK<br>";
        }
        echo "<br>";
    }


    static function check_setup_support(){
        global $wpdb;

        echo "Database Tables: \n";
        $subscriber_events_table =  SendPress_DB_Tables::subscriber_event_table();
        if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
            echo $subscriber_events_table . " Not Installed\n";
        } else {
             echo $subscriber_events_table . " OK\n";
        }

        $report_url_table =  SendPress_DB_Tables::report_url_table();
        if($wpdb->get_var("show tables like '$report_url_table'") != $report_url_table) {
            echo $report_url_table . " Not Installed\n";
        } else {
             echo $report_url_table . " OK\n";
        }

        $subscriber_status_table =  SendPress_DB_Tables::subscriber_status_table();
        if($wpdb->get_var("show tables like '$subscriber_status_table'") != $subscriber_status_table) {
            echo $subscriber_status_table . " Not Installed\n";
        } else {
             echo $subscriber_status_table . " OK\n";
        }

        $subscriber_table = SendPress_DB_Tables::subscriber_table();
        if($wpdb->get_var("show tables like '$subscriber_table'") != $subscriber_table) {
            echo $subscriber_table . " Not Installed\n";
        } else {
             echo $subscriber_table . " OK\n";
        }

        $subscriber_list_subscribers = SendPress_DB_Tables::list_subcribers_table();
        if($wpdb->get_var("show tables like '$subscriber_list_subscribers'") != $subscriber_list_subscribers) {
            echo $subscriber_list_subscribers . " Not Installed\n";
        } else {
             echo $subscriber_list_subscribers . " OK\n";
        }

        $subscriber_queue = SendPress_DB_Tables::queue_table();
        if($wpdb->get_var("show tables like '$subscriber_queue'") != $subscriber_queue) {
            echo $subscriber_queue . " Not Installed\n";
        } else {
             echo $subscriber_queue . " OK\n";
        }
        echo "\n";
    }
  

    static function remove_all_data(){
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

    /**
     * 
     * SENDPRESS TABLE UPDATES
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     **/
    static function update_tables_093(){
        global $wpdb;
        $table_to_update = SendPress_DB_Tables::subscriber_table();
        if( $wpdb->get_var("SHOW COLUMNS FROM ". $table_to_update ." LIKE 'wp_user_id'") == false) {
            $wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN `wp_user_id` bigint(20) DEFAULT NULL");
            $wpdb->query("ALTER TABLE ". $table_to_update ." ADD UNIQUE KEY `wp_user_id` (`wp_user_id`)");
        }
    }


    static function update_tables_0947(){

        global $wpdb;

        $table_to_update = SendPress_DB_Tables::subscriber_table();
        $wpdb->query("ALTER TABLE ". $table_to_update ." MODIFY registered  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
        $wpdb->query("ALTER TABLE ". $table_to_update ." MODIFY join_date  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");

        $subscriber_queue = SendPress_DB_Tables::queue_table();

        $wpdb->query("ALTER TABLE ". $subscriber_queue ." MODIFY date_published  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
        $wpdb->query("ALTER TABLE ". $subscriber_queue ." MODIFY last_attempt  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
        $wpdb->query("ALTER TABLE ". $subscriber_queue ." MODIFY date_sent  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
        
        $ls = SendPress_DB_Tables::list_subcribers_table();
        $wpdb->query("ALTER TABLE ". $ls ." MODIFY updated  datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
    }

    static function update_tables_0952(){
         global $wpdb;
         $ls = SendPress_DB_Tables::list_subcribers_table();
         $wpdb->query("ALTER IGNORE TABLE `". $ls ."` ADD UNIQUE INDEX `listsub` (`subscriberID`,`listID`)");

         $subscriber_queue = SendPress_DB_Tables::queue_table();
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `to_email` (`to_email`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `subscriberID` (`subscriberID`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `listID` (`listID`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `inprocess` (`inprocess`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `success` (`success`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `max_attempts` (`max_attempts`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `attempts` (`attempts`)");
         $wpdb->query("ALTER IGNORE TABLE `". $subscriber_queue ."` ADD KEY `last_attempt` (`last_attempt`)");

        }



}

