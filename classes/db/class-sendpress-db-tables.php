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
	static function subscriber_click_table(){
		global $wpdb;
		return $wpdb->prefix . self::$prefix . "subscribers_click";
	}

    /**
     * subscriber_click_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
    static function subscriber_tracker_table(){
        global $wpdb;
        return $wpdb->prefix . self::$prefix . "subscribers_tracker";
    }

     /**
     * subscriber_click_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
    static function url_table(){
        global $wpdb;
        return $wpdb->prefix . self::$prefix . "url";
    }

    /**
     * subscriber_meta_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
    static function subscriber_meta_table(){
        global $wpdb;
        return $wpdb->prefix . self::$prefix . "subscribers_meta";
    }

    /**
     * subscriber_meta_table
     * 
     * @access public
     *
     * @return mixed Value.
     */
    static function subscriber_url_table(){
        global $wpdb;
        return $wpdb->prefix . self::$prefix . "subscribers_url";
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
        $subscriber_events_table =  new SendPress_DB_Subscribers_Tracker();
        $subscriber_events_table = $subscriber_events_table->table_name;
        if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
            echo $subscriber_events_table . " Not Installed<br>";
        } else {
             echo $subscriber_events_table . " OK<br>";
        }

        $subscriber_events_table =  new SendPress_DB_Subscribers_Url();
        $subscriber_events_table = $subscriber_events_table->table_name;
        if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
            echo $subscriber_events_table . " Not Installed<br>";
        } else {
             echo $subscriber_events_table . " OK<br>";
        }

        $subscriber_events_table =  new SendPress_DB_Url();
        $subscriber_events_table = $subscriber_events_table->table_name;
        if($wpdb->get_var("show tables like '$subscriber_events_table'") != $subscriber_events_table) {
            echo $subscriber_events_table . " Not Installed<br>";
        } else {
             echo $subscriber_events_table . " OK<br>";
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
        $tables  =  true;
       


        $subscriber_status_table =  SendPress_DB_Tables::subscriber_status_table();
        if($wpdb->get_var("show tables like '$subscriber_status_table'") != $subscriber_status_table) {
            $tables =false;
        } 
        $subscriber_table = SendPress_DB_Tables::subscriber_table();
        if($wpdb->get_var("show tables like '$subscriber_table'") != $subscriber_table) {
           $tables =false;
        } 

        $subscriber_list_subscribers = SendPress_DB_Tables::list_subcribers_table();
        if($wpdb->get_var("show tables like '$subscriber_list_subscribers'") != $subscriber_list_subscribers) {
            $tables =false;
        } 

        $subscriber_queue = SendPress_DB_Tables::queue_table();
        if($wpdb->get_var("show tables like '$subscriber_queue'") != $subscriber_queue) {
            $tables =false;
        } 
        if($tables !== false){
            return "Tables Installed";
        } else {
            return "Tables Missing";
        }
       
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
            $wpdb->query("ALTER TABLE ". $table_to_update ." ADD COLUMN wp_user_id bigint(20) DEFAULT NULL");
            $wpdb->query("ALTER TABLE ". $table_to_update ." ADD UNIQUE KEY wp_user_id (wp_user_id)");
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
        if( $wpdb->get_var("SHOW INDEX FROM ". $ls ." WHERE Key_name = 'listsub'") == false) {
            $wpdb->query("ALTER IGNORE TABLE ". $ls ." ADD UNIQUE INDEX listsub (subscriberID,listID)");
        }

         $subscriber_queue = SendPress_DB_Tables::queue_table();

         
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'subscriberID'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY subscriberID (subscriberID)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'listID'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY listID (listID)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'inprocess'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY inprocess (inprocess)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'success'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY success (success)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'max_attempts'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY max_attempts (max_attempts)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'attempts'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY attempts (attempts)");
            }
            if( $wpdb->get_var("SHOW INDEX FROM ". $subscriber_queue ." WHERE Key_name = 'last_attempt'") == false) {
                $wpdb->query("ALTER IGNORE TABLE ". $subscriber_queue ." ADD KEY last_attempt (last_attempt)");
            }

        }

        


        static function repair_tables(){
            global $wpdb;
             $subscriber_table = SendPress_DB_Tables::subscriber_table();
             $subscriber_queue = SendPress_DB_Tables::queue_table();
             
             $wpdb->query("REPAIR TABLE  $subscriber_queue, $subscriber_table");

             SPNL()->load("Subscribers_Tracker")->repair_table();
        }



    static function install(){

            global $wpdb;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
          
            $wpdb->hide_errors();

            $collate = '';

            if ( $wpdb->has_cap( 'collation' ) ) {
                if( ! empty($wpdb->charset ) ){
                      $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                }
                  
                if( ! empty($wpdb->collate ) ){
                     $collate .= " COLLATE $wpdb->collate";
                }
                   
            }

            /*
            
            THE RULES FOR DBDELTA

            You must put each field on its own line in your SQL statement.
            You must have two spaces between the words PRIMARY KEY and the definition of your primary key.
            You must use the key word KEY rather than its synonym INDEX and you must include at least one KEY.
            You must not use any apostrophes or backticks around field names.
             */

            // Create Stats Table
            $subscriber_table = SendPress_DB_Tables::subscriber_table();
            //if($wpdb->get_var("show tables like '$subscriber_table'") != $subscriber_table) {
            $command ='';
$command .= " CREATE TABLE $subscriber_table (
subscriberID bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
email varchar(100) NOT NULL DEFAULT '', 
join_date datetime  NOT NULL DEFAULT '0000-00-00 00:00:00', 
status int(1) NOT NULL DEFAULT '1', 
registered datetime  NOT NULL DEFAULT '0000-00-00 00:00:00', 
registered_ip varchar(20) NOT NULL DEFAULT '', 
identity_key varchar(60) NOT NULL DEFAULT '', 
bounced int(1) NOT NULL DEFAULT '0', 
firstname varchar(250) DEFAULT '', 
lastname varchar(250) DEFAULT '', 
wp_user_id bigint(20) DEFAULT NULL, 
phonenumber varchar(12) DEFAULT NULL, 
salutation varchar(40) DEFAULT NULL,
PRIMARY KEY  (subscriberID), 
UNIQUE KEY email (email) , 
UNIQUE KEY identity_key (identity_key), 
UNIQUE KEY wp_user_id (wp_user_id)
) $collate;\n"; 
             //dbDelta($command);  
            //}
$subscriber_list_subscribers = SendPress_DB_Tables::list_subcribers_table();
$command .= " CREATE TABLE $subscriber_list_subscribers (
id int(11) unsigned NOT NULL AUTO_INCREMENT, 
listID int(11) DEFAULT NULL, 
subscriberID int(11) DEFAULT NULL, 
status int(1) DEFAULT NULL, 
updated datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
PRIMARY KEY  (id), 
KEY listID (listID) , 
KEY subscriberID (subscriberID) , 
KEY status (status), 
UNIQUE KEY listsub (subscriberID,listID)
) $collate;\n";
            //dbDelta($command);  
          
            $subscriber_meta = SendPress_DB_Tables::subscriber_meta_table();
            
$command .= " CREATE TABLE $subscriber_meta (
smeta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
subscriberID bigint(20) unsigned NOT NULL DEFAULT '0', 
listID bigint(20) unsigned NULL DEFAULT '0', 
meta_key varchar(255) DEFAULT NULL, 
meta_value longtext, 
PRIMARY KEY  (smeta_id), 
KEY listID (listID), 
KEY subscriberID (subscriberID), 
KEY meta_key (meta_key)
) $collate;\n";
                //dbDelta($command);  
                
                 $subscriber_queue = SendPress_DB_Tables::queue_table();
           
$command .=" CREATE TABLE $subscriber_queue (
id int(11) NOT NULL AUTO_INCREMENT, 
subscriberID int(11) DEFAULT NULL, 
listID int(11) DEFAULT NULL, 
from_name varchar(64) DEFAULT NULL, 
from_email varchar(128) NOT NULL, 
to_email varchar(128) NOT NULL, 
subject varchar(255) NOT NULL, 
messageID varchar(400) NOT NULL, 
emailID int(11) NOT NULL, 
max_attempts int(11) NOT NULL DEFAULT '3', 
attempts int(11) NOT NULL DEFAULT '0', 
success tinyint(1) NOT NULL DEFAULT '0', 
date_published datetime  NOT NULL DEFAULT '0000-00-00 00:00:00', 
inprocess int(1) DEFAULT '0', 
last_attempt datetime  NOT NULL DEFAULT '0000-00-00 00:00:00', 
date_sent datetime  NOT NULL DEFAULT '0000-00-00 00:00:00', 
PRIMARY KEY  (id), 
KEY to_email (to_email), 
KEY subscriberID (subscriberID), 
KEY listID (listID), 
KEY inprocess (inprocess), 
KEY success (success), 
KEY max_attempts (max_attempts), 
KEY attempts (attempts), 
KEY last_attempt (last_attempt),
KEY date_sent (date_sent),
KEY success_date (success,last_attempt,max_attempts,attempts,inprocess,date_sent)
) $collate;\n";
// dbDelta($command); 
            
/*
$subscriber_events_table =  SendPress_DB_Tables::subscriber_event_table();
$command .= " CREATE TABLE $subscriber_events_table (
eventID int(11) unsigned NOT NULL AUTO_INCREMENT, 
subscriberID int(11) unsigned NOT NULL, 
reportID int(11) unsigned DEFAULT NULL, 
urlID int(11) unsigned DEFAULT NULL, 
listID int(11) unsigned DEFAULT NULL, 
eventdate datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
ip  varchar(400) DEFAULT NULL, 
devicetype  varchar(50) DEFAULT NULL, 
device  varchar(50) DEFAULT NULL, 
type varchar(50) DEFAULT NULL, 
PRIMARY KEY  (eventID), 
KEY subscriberID (subscriberID), 
KEY reportID (reportID), 
KEY urlID (urlID), 
KEY listID (listID), 
KEY eventdate (eventdate), 
KEY type (type)
) $collate;\n";
             //dbDelta($command);  
  /*          
$report_url_table =  SendPress_DB_Tables::report_url_table();
$command .= " CREATE TABLE $report_url_table (
urlID int(11) unsigned NOT NULL AUTO_INCREMENT,
url varchar(2000) DEFAULT NULL,
reportID int(11) DEFAULT NULL,
PRIMARY KEY  (urlID),
KEY reportID (reportID),
KEY url (url(255))
) $collate;\n"; 
            //  dbDelta($command); 
    */          

$subscriber_status_table =  SendPress_DB_Tables::subscriber_status_table();
$command .= " CREATE TABLE $subscriber_status_table (
statusid int(11) unsigned NOT NULL AUTO_INCREMENT, 
status varchar(255) DEFAULT NULL, 
PRIMARY KEY  (statusid)
) $collate;\n"; 

/*
$subscriber_tracker_table =  SendPress_DB_Tables::subscriber_tracker_table();
$command .= " CREATE TABLE $subscriber_tracker_table (
  subscriberID int(11) unsigned NOT NULL,
  emailID int(11) unsigned NOT NULL,
  sent_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
  opened_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  status tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY  (subscriberID,emailID)
)  $collate;\n"; 
*/
/*
$subscriber_url_table =  SendPress_DB_Tables::subscriber_url_table();
$command .= " CREATE TABLE $subscriber_url_table (
    subscriberID int(11) unsigned NOT NULL,
    emailID int(11) unsigned NOT NULL,
    urlID int(11) unsigned NOT NULL,
    clicked_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
    click_count int(11) unsigned NOT NULL,
    PRIMARY KEY  ( subscriberID , emailID , urlID )
)  $collate;\n"; 
*/
/*
$url_table =  SendPress_DB_Tables::url_table();
$command .= " CREATE TABLE $url_table (
  urlID int(11) unsigned NOT NULL AUTO_INCREMENT,
  url text,
  hash varchar(255) DEFAULT NULL, 
  PRIMARY KEY  (urlID),
  KEY hash (hash)
)  $collate;\n"; 
*/

dbDelta($command);   

            
           


           



            $unconfirmed = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 1) );
            if ($unconfirmed != null) {
                $wpdb->update( 
                    $subscriber_status_table, 
                    array( 
                        'status' => 'Unconfirmed',  // string
                    ), 
                    array( 'statusid' => 1 ), 
                    array( 
                        '%s',   // value1
                    ), 
                    array( '%d' ) 
                );

            } else {
                $wpdb->insert( 
                    $subscriber_status_table, 
                    array( 
                        'statusid' => 1, 
                        'status' => 'Unconfirmed' 
                    ), 
                    array( 
                        '%d', 
                        '%s' 
                    ) 
                );
            }



            $active = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 2) );
            if ($active != null) {
                $wpdb->update( 
                    $subscriber_status_table, 
                    array( 
                        'status' => 'Active',   // string
                    ), 
                    array( 'statusid' => 2 ), 
                    array( 
                        '%s',   // value1
                    ), 
                    array( '%d' ) 
                );

            } else {
                $wpdb->insert( 
                    $subscriber_status_table, 
                    array( 
                        'statusid' => 2, 
                        'status' => 'Active' 
                    ), 
                    array( 
                        '%d', 
                        '%s' 
                    ) 
                );
            }


            $unsubscribed = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 3) );
            if ($unsubscribed != null) {
                $wpdb->update( 
                    $subscriber_status_table, 
                    array( 
                        'status' => 'Unsubscribed', // string
                    ), 
                    array( 'statusid' => 3 ), 
                    array( 
                        '%s',   // value1
                    ), 
                    array( '%d' ) 
                );

            } else {
                $wpdb->insert( 
                    $subscriber_status_table, 
                    array( 
                        'statusid' => 3, 
                        'status' => 'Unsubscribed' 
                    ), 
                    array( 
                        '%d', 
                        '%s' 
                    ) 
                );
            }


            $bounced = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $subscriber_status_table WHERE statusid = %d" , 4) );
            if ($bounced != null) {
                $wpdb->update( 
                    $subscriber_status_table, 
                    array( 
                        'status' => 'Bounced',  // string
                    ), 
                    array( 'statusid' => 4 ), 
                    array( 
                        '%s',   // value1
                    ), 
                    array( '%d' ) 
                );

            } else {
                $wpdb->insert( 
                    $subscriber_status_table, 
                    array( 
                        'statusid' => 4, 
                        'status' => 'Bounced' 
                    ), 
                    array( 
                        '%d', 
                        '%s' 
                    ) 
                );
            }




    }


}

