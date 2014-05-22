<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Data extends SendPress_DB_Tables {


	static function nonce(){
		return 'sendpress-is-awesome';
	}

	static function nonce_field(){
		wp_nonce_field( SendPress_Data::nonce() );
	}

	static function email_post_type(){
		return 'sp_newsletters';
	}
	
	static function template_post_type(){
		return 'sptemplates';
	}

	static function report_post_type(){
		return 'sp_report';
	}

	static function gmdate(){
		return gmdate('Y-m-d H:i:s');
	}

	/********************* BASE static functionS **************************/	

	static function wpdbQuery($query, $type) {
		global $wpdb;
		$result = $wpdb->$type( $query );
		return $result;
	}

	static function wpdbQueryArray($query) {
		global $wpdb;
		$result = $wpdb->get_results( $query , ARRAY_N);
		return $result;
	}


	static function let_to_num( $v ) {

		$l   = substr( $v, -1 );
		$ret = substr( $v, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
				break;
		}
		return $ret;
	}
	
	/********************* BASE static functionS **************************/

	/********************* QUEUE static functionS **************************/

	static function get_single_email_from_queue(){
		global $wpdb;
		$date = date_i18n('Y-m-d H:i:s');
		$info =  $wpdb->get_row($wpdb->prepare("SELECT * FROM ". SendPress_Data::queue_table() ." WHERE success = 0 AND max_attempts != attempts AND inprocess = 0 and ( date_sent = '0000-00-00 00:00:00' or date_sent < %s ) ORDER BY id LIMIT 1", $date));

		return $info;

	}

	static function remove_from_queue($id){
		global $wpdb;
		$table = self::queue_table();
		$wpdb->query( $wpdb->prepare("DELETE FROM $table WHERE emailID = %d", $id ) );
	}

	static function delete_queue_emails(){
		$table = self::queue_table();
		self::wpdbQuery("DELETE FROM $table WHERE success = 0 AND max_attempts > attempts", 'query');
	}

	static function delete_stuck_queue_emails(){
		$table = self::queue_table();
		self::wpdbQuery("DELETE FROM $table WHERE success = 0 AND max_attempts = attempts ", 'query');
	}

	static function queue_email_process($id){
		$table = self::queue_table();
		global $wpdb;
		$result = $wpdb->update( $table ,array('inprocess'=>'1','last_attempt'=>date('Y-m-d H:i:s')), array('id'=> $id) );

	}

	static function requeue_emails(){
		$table = self::queue_table();
		global $wpdb;
		$result = $wpdb->update( $table ,array('attempts'=>'0'), array('attempts'=> '1' ,'success'=>'0') );

	}

	static function emails_in_queue($id = false){
		global $wpdb;
		$table = self::queue_table();
		if($id == false){
			$query = "SELECT COUNT(*) FROM $table where success = 0";
			 $query.=" AND ( date_sent = '0000-00-00 00:00:00' or date_sent < '".date_i18n('Y-m-d H:i:s')."') ";
       
	        if(isset($_GET["listid"]) &&  $_GET["listid"]> 0 ){
	            $query .= ' AND listID = '. $_GET["listid"];
	        }

	        if(isset($_GET["qs"] )){
	            $query .= ' AND to_email LIKE "%'. $_GET["qs"] .'%"';

	        }

		} else {
			$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where emailID = %d and success = 0", $id );
		}	
		return $wpdb->get_var( $query );
	}

	static function emails_maxed_in_queue($id = false){
		global $wpdb;
		$table = self::queue_table();
		if($id == false){
			$query = "SELECT COUNT(*) FROM $table where success = 0";
			 $query.=" AND ( date_sent = '0000-00-00 00:00:00' or date_sent < '".date_i18n('Y-m-d H:i:s')."') ";
       
	        if(isset($_GET["listid"]) &&  $_GET["listid"]> 0 ){
	            $query .= ' AND listID = '. $_GET["listid"];
	        }

	        if(isset($_GET["qs"] )){
	            $query .= ' AND to_email LIKE "%'. $_GET["qs"] .'%"';

	        }

	        $query .= " AND max_attempts = attempts ";

		} else {
			$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where emailID = %d and success = 0", $id );
		}	
		return $wpdb->get_var( $query );
	}

	static function emails_active_in_queue($id = false){
		global $wpdb;
		$table = self::queue_table();
		if($id == false){
			$query = "SELECT COUNT(*) FROM $table where success = 0";
			 $query.=" AND ( date_sent = '0000-00-00 00:00:00' or date_sent < '".date_i18n('Y-m-d H:i:s')."') ";
       
	        if(isset($_GET["listid"]) &&  $_GET["listid"]> 0 ){
	            $query .= ' AND listID = '. $_GET["listid"];
	        }

	        if(isset($_GET["qs"] )){
	            $query .= ' AND to_email LIKE "%'. $_GET["qs"] .'%"';

	        }

	        $query .= " AND max_attempts > attempts ";

		} else {
			$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where emailID = %d and success = 0", $id );
		}	
		return $wpdb->get_var( $query );
	}



	static function emails_stuck_in_queue($id = false){
		global $wpdb;
		$table = self::queue_table();
		$hour_ago = strtotime('-1 hour');
		$hour = date('Y-m-d H:i:s', $hour_ago);
		$query = "SELECT COUNT(*) FROM $table where success = 0 and (( inprocess = 1 and last_attempt < %s and last_attempt != '0000-00-00 00:00:00') or (max_attempts = attempts) ) ";
		return $wpdb->get_var( $wpdb->prepare($query,  $hour) );
	}



	static function get_lists_in_queue(){
		global $wpdb;
		$table = self::queue_table();
		$hour_ago = strtotime('-1 hour');
		$hour = date('Y-m-d H:i:s', $hour_ago);
		$query = "SELECT listID FROM $table where success = 0 group by listID ";
		$id=$wpdb->get_results( $query );
		$listdata = array();
		foreach ($id as $list) {
			$listdata[] = array('id'=>$list->listID,'title'=>get_the_title($list->listID));
		}

	
		return $listdata;
	}

	static function clean_queue_table(){
		global $wpdb;

		
		$hour_ago = strtotime('-1 hour');
		$hour = date('Y-m-d H:i:s', $hour_ago);
		$days_to_save = SendPress_Option::get('queue-history',7);
		$x_days_past = strtotime('-'.$days_to_save.' day');
		$day = date('Y-m-d H:i:s', $x_days_past);
		
		$table = self::queue_table();
		$query = $wpdb->prepare("DELETE FROM $table where last_attempt < %s and success = %d", $day, 1 );
		$wpdb->query( $query );
		$query = $wpdb->prepare("UPDATE $table set inprocess = 0 where last_attempt < %s and success = %d and inprocess = %d", $hour, 0 ,1 );
		$wpdb->query( $query );


	}

	static function emails_sent_in_queue($type = "hour" ){

		global $wpdb;

		if($type == "hour"){
			$hour_ago = strtotime('-1 hour');
			$time = date('Y-m-d H:i:s', $hour_ago);
		}
		if($type == "day"){
			$hour_ago = strtotime('-1 day');
			$time = date('Y-m-d H:i:s', $hour_ago);
			
		}
		$table = self::queue_table();
		if($type == "All"){

			$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where success = %d", 1 );
			
			return $wpdb->get_var( $query );

		}
		
		$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where last_attempt > %s and success = %d", $time, 1 );
		return $wpdb->get_var( $query );
	}

	static function emails_sent_in_queue_for_report($id = false){

		global $wpdb;

		if($id == false){
			return 0;
		}

		
		$table = self::queue_table();
		$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where emailID = %d AND success >= %d", $id, 1 );
			
		return $wpdb->get_var( $query );
	}


	static function process_with_iron( $id ){
		global $wpdb;
		$table = self::queue_table();
		$query = $wpdb->prepare("SELECT id from $table where id = %d and inprocess = %d" , $id,0);
		$id = $wpdb->get_var($query);
		if(!isset($id)){
			return 0;
		}
		$result = $wpdb->update( $table ,array('inprocess'=>'1','last_attempt'=>date('Y-m-d H:i:s')), array('id'=> $id) );
		return $result;
	}


	static function fetch_queue_for_iron(){
		global $wpdb;
		$table = self::queue_table();

		$counter = SendPress_Option::get('last_queue_id');
		if($counter == false){
			$coutner = 0;
		} else {
				$query = $wpdb->prepare("SELECT id from $table where id = %d" , $counter+1);
				$id = $wpdb->get_var($query);
				if(!isset($id)){
					$counter= 0;
				}
		}





		$query = $wpdb->prepare("SELECT * FROM $table  WHERE id > %d LIMIT 10" , $counter);


		$data =  $wpdb->get_results($query,ARRAY_A);

		$end = end($data);
		if($end['id']){
			SendPress_Option::set('last_queue_id', $end['id']);
		}
		return $data;

		
	}


	static function add_email_to_queue($values){
		global $wpdb;
		$table = SendPress_Data::queue_table();
		$messageid = SendPress_Data::unique_message_id();
		$values["messageID"] = $messageid;
		$values["max_attempts"] = 1;
		$values["date_published"] = date('Y-m-d H:i:s');
		$wpdb->insert( $table, $values);
	}

	static	function unique_message_id() {
		if ( isset($_SERVER['SERVER_NAME'] ) ) {
	      	$servername = $_SERVER['SERVER_NAME'];
	    } else {
	      	$servername = 'localhost.localdomain';
	    }
	    $uniq_id = md5(uniqid(time()));
	    $result = sprintf('%s@%s', $uniq_id, $servername);
	    return $result;
	}



	static function get_charset_types(){
		return array(
				"UTF-8",
				"UTF-7",
				"BIG5",
				"ISO-8859-1",
				"ISO-8859-2",
				"ISO-8859-3",
				"ISO-8859-4",
				"ISO-8859-5",
				"ISO-8859-6",
				"ISO-8859-7",
				"ISO-8859-8",
				"ISO-8859-9",
				"ISO-8859-10",
				"ISO-8859-13",
				"ISO-8859-14",
				"ISO-8859-15",
				"Windows-1251",
				"Windows-1252");

	}

	static function build_social($color = ''){
		$link = SendPress_Option::get('socialicons');
		$socialsize = SendPress_Option::get('socialsize','large');
		$px = '32px';
		switch($socialsize){
			case  'small':
				$px = '16px';
			break;
			case 'text':
				$px = 'text';
				break;


		}
		$output ='';
		$c = 1;
		if($color !== ''){
			$color = 'style="color: '.$color.';"';
		} 
		if( is_array( $link ) && !empty($link) ) {
		ksort($link);
		foreach($link as $key => $url ){
				if($px !== 'text'){
					$output .= '<a href="'. $url .'" ><img src="'.  SENDPRESS_URL .'img/'. $px .'/'. $key .'.png" alt="'. $key .'" /></a> ';
				} else {
					if($c > 1){
						$output .= ' | ';
					}

					$output .= '<a href="'. $url .'" '.$color.'>'. $key .'</a>';
				}
				$c++;
			}
		}
			return $output;
	}

	static function social_icons(){
		return array(
    		'500px' => 'e.g. http://500px.com/username',
			'AddThis' => 'e.g. http://www.addthis.com',
			'AppNet' => 'e.g. http://app.net/username',
			'Behance' => 'e.g. http://www.behance.net/username',
			'Blogger' => 'e.g. http://username.blogspot.com',
			'Mail' => 'e.g. mailto:user@name.com',
			'Delicious' => 'e.g. http://delicious.com/username',
			'DeviantART' => 'e.g. http://username.deviantart.com/',
			'Digg' => 'e.g. http://digg.com/username',
			'Dopplr' => 'e.g. http://www.dopplr.com/traveller/username',
			'Dribbble' => 'e.g. http://dribbble.com/username',
			'Evernote' => 'e.g. http://www.evernote.com',
			'Facebook' => 'e.g. http://www.facebook.com/username',
			'Flickr' => 'e.g. http://www.flickr.com/photos/username',
			'Forrst' => 'e.g. http://forrst.me/username',
			'GitHub' => 'e.g. https://github.com/username',
			'Google+' => 'e.g. http://plus.google.com/userID',
			'Grooveshark' => 'e.g. http://grooveshark.com/username',
			'Instagram' => 'e.g. http://instagr.am/p/picID',
			'Lastfm' => 'e.g. http://www.last.fm/user/username',
			'LinkedIn' => 'e.g. http://www.linkedin.com/in/username',
			'MySpace' => 'e.g. http://www.myspace.com/userID',
			'Path' => 'e.g. https://path.com/p/picID',
			'PayPal' => 'e.g. mailto:email@address',
			'Picasa' => 'e.g. https://picasaweb.google.com/userID',
			'Pinterest' => 'e.g. http://pinterest.com/username',
			'Posterous' => 'e.g. http://username.posterous.com',
			'Reddit' => 'e.g. http://www.reddit.com/user/username',
			'RSS' => 'e.g. http://example.com/feed',
			'ShareThis' => 'e.g. http://sharethis.com',
			'Skype' => 'e.g. skype:username',
			'Soundcloud' => 'e.g. http://soundcloud.com/username',
			'Spotify' => 'e.g. http://open.spotify.com/user/username',
			'StumbleUpon' => 'e.g. http://www.stumbleupon.com/stumbler/username',
			'Tumblr' => 'e.g. http://username.tumblr.com',
			'Twitter' => 'e.g. http://twitter.com/username',
			'Viddler' => 'e.g. http://www.viddler.com/explore/username',
			'Vimeo' => 'e.g. http://vimeo.com/username',
			'Virb' => 'e.g. http://username.virb.com',
			'Windows' => 'e.g. http://www.apple.com',
			'WordPress' => 'e.g. http://username.wordpress.com',
			'YouTube' => 'e.g. http://www.youtube.com/user/username',
			'Zerply' => 'e.g. http://zerply.com/username'
    	);
	}

	static function get_encoding_types(){
		return array("8bit", "7bit", "binary", "base64",  "quoted-printable");
	}
	/********************* QUEUE static functionS **************************/

	/********************* POST NOTIFICATION static functionS ***************/

	static function get_post_notification_types(){
		return array("pn-instant" => "Instant", "pn-daily" => "Daily", "pn-weekly" => "Weekly");
	}

	/********************* POST NOTIFICATION static functionS ***************/

	/********************* REPORTS static functionS **************************/	

   	//TO BE REMOVED
    static function update_report_sent_count( $id ) {
    	return;
    }

    static function get_last_report(){
    	$f = get_posts(array('post_type' => 'sp_report','posts_per_page'   => 1));
    	return $f[0];

    }



 	/**
     * Get url's in the database by the report and url string
     * 
     * @param mixed $id         the report id.
     * @param mixed $url_string the url string.
     *
     * @access public
     *
     * @return mixed Value.
     */
    

 	static function get_url_by_id( $id ) {
 		global $wpdb;
		$table = SendPress_Data::report_url_table();
		$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE urlID = %d", $id) );
		return $result;	
	}

	static function get_url_by_report_url( $id , $url_string ){
		$table = self::report_url_table();
		$result = self::wpdbQuery("SELECT * FROM $table WHERE reportID = '$id' AND url = '$url_string'", 'get_results');
		return $result;	
	}

	static function get_open_without_id($rid, $sid){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();
		$result = $wpdb->query( $wpdb->prepare("SELECT * FROM $table WHERE reportID = %d AND subscriberID = %d AND type='open' ", $rid, $sid ) );
		return $result;
	}


	static function insert_report_url( $url ){
		global $wpdb;
		$wpdb->insert( self::report_url_table(),  $url);
		return $wpdb->insert_id;
	}


	static function get_clicks_and_opens($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	static function get_clicks_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' AND type = 'click' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	static function get_opens_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' AND type = 'open' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	static function get_open_total($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE reportID = '$rid' AND type = 'open' GROUP BY subscriberID ;");
		if(empty($result)){
			return 0;
		}
		return $result;
	}

	static function get_opens_unique_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(t.eventID) as count,date(t.eventdate) as day FROM  (Select eventdate,eventID FROM $table  WHERE  reportID = '$rid' AND type = 'open' GROUP BY subscriberID) as t GROUP BY date(eventdate) ORDER BY eventID DESC ");

		return $result;
	}

	static function get_clicks_unique_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(t.eventID) as count,date(t.eventdate) as day FROM  (Select eventdate,eventID FROM $table  WHERE  reportID = '$rid' AND type = 'click' GROUP BY subscriberID) as t GROUP BY date(eventdate) ORDER BY eventID DESC ");
		return $result;
	}

	static function get_click_total($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE reportID = '$rid' AND type = 'click' GROUP BY subscriberID ;");
		if(empty($result)){
			return 0;
		}
		return $result;
	}

	static function get_bounce_total($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE reportID = '$rid' AND type = 'bounce' GROUP BY subscriberID ;");
		if(empty($result)){
			return 0;
		}
		return $result;
	}


	static function get_unsubscribed_total($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE reportID = '$rid' AND type = 'unsubscribed' GROUP BY subscriberID ;");
		if(empty($result)){
			return 0;
		}
		return $result;
	}

	static function track_click( $sid, $rid, $lid, $ip , $device_type, $device ){
		global $wpdb;

		if(false == SendPress_Data::get_open_without_id($rid,$sid) ){
			SendPress_Data::track_open($sid, $rid, $ip, $device_type, $device );
		}

		$urlData = array(
			'eventdate'=>date('Y-m-d H:i:s'),
			'subscriberID' => $sid,
			'reportID' => $rid,
			'urlID'=>$lid,
			'ip'=>$ip,
			'devicetype'=> $device_type,
			'device'=> $device,
			'type'=>'click'
		);
		
		$wpdb->insert( SendPress_Data::subscriber_event_table(),  $urlData);

		//print_r($this->get_open_without_id($rid,$sid));
	}

	static function track_open( $sid, $rid, $ip = null , $device_type=null, $device=null ){
		global $wpdb;
		$wpdb->insert( SendPress_Data::subscriber_event_table() , array('reportID'=> $rid,'subscriberID'=>$sid,'eventdate'=>date('Y-m-d H:i:s'),'type'=>'open' ,'ip'=>$ip,'devicetype'=> $device_type,'device'=> $device));
	}

	static function get_subscriber_event_count_month($date, $type){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();//SELECT * FROM table_name WHERE MONTH(date_column) = 4;
		$result = $wpdb->get_var("SELECT COUNT(eventdate) as count FROM $table WHERE type = '$type' AND MONTH(eventdate) = $date");
		return $result;
	}
	//date_column between "2001-01-05" and "2001-01-10"
	static function get_subscriber_event_count_week($date1, $date2, $type){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();
		$result = $wpdb->get_var("SELECT COUNT(eventdate) as count FROM $table WHERE type = '$type' AND date(eventdate) BETWEEN '$date1' AND '$date2'");
		return $result;
	}

	static function get_subscriber_event_count_day($date, $type){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();//SELECT * FROM table_name WHERE MONTH(date_column) = 4;
		$result = $wpdb->get_var("SELECT COUNT(eventdate) as count FROM $table WHERE type = '$type' AND date(eventdate) = '$date'");
		return $result;
	}

	/********************* END REPORTS static functionS **************************/

	/************************* LIST static functionS ****************************/

	static function get_list_details($id){
		return get_post( $id  );
	}
	static function create_list($values){
		// Create post object
		  $my_post = array(
		     'post_title' => $values['name'],
		     'post_content' => '',
		     'post_status' => 'publish',
		     'post_type'=>'sendpress_list'
		  );

		// Insert the post into the database
  		$new_id = wp_insert_post( $my_post );
  		update_post_meta($new_id,'public',$values['public']);
		//add_post_meta($new_id,'last_send_date',$newlist->last_send_date);
		//add_post_meta($new_id,'legacy_id',$newlist->listID);
		//$this->upgrade_lists_new_id( $newlist->listID, $new_id);
		//	$table = $this->lists_table();

		
		//$result = $this->wpdbQuery("INSERT INTO $table (name, created, public) VALUES( '" .$values['name'] . "', '" . date('Y-m-d H:i:s') . "','" .$values['public'] . "')", 'query');

		return $new_id;	
	}
	
	static function update_list($listID, $values){
		global $wpdb;

		//$table = $this->lists_table();

		//$result = $wpdb->update($table,$values, array('listID'=> $listID) );
		
		$my_post = array(
		    'post_title' => $values['name'],
		    'ID'=> $listID,     
		);

		// Insert the post into the database
  		$new_id = wp_update_post( $my_post );
  		update_post_meta($new_id,'public',$values['public']);


		return $new_id;
	}

	static function get_lists($args = array(), $use_wpquery = true){

		$args = apply_filters('sendpress_get_lists', array_merge($args, array(
			'numberposts'     => -1,
	    	'offset'          => 0,
	    	'orderby'         => 'post_title',
	    	'order'           => 'DESC'
	    )));
		//set the post type after filter so our function name always makes sense ;)
	    $args['post_type'] = 'sendpress_list';

		return ( $use_wpquery ) ? new WP_Query( $args ) : get_posts( $args );
	}

	/********************* END LIST FUNCTIONS ****************************/


	/********************* SUBSCRIBER static functionS **************************/

	static function remove_all_subscribers( $list_id = false ){
		if($list_id !== false && is_numeric( $list_id )){
			global $wpdb;
			$table = self::list_subcribers_table();
			$wpdb->query( $wpdb->prepare("DELETE FROM $table WHERE listID = %d", $list_id) );
		}
	}


	static function delete_all_subscribers(){
		global $wpdb;
			$wpdb->query( 
					"DELETE FROM " .  SendPress_Data::subscriber_table() ." "
				);
			$wpdb->query(
					"DELETE FROM " .  SendPress_Data::list_subcribers_table() ." "
				);
			$wpdb->query( 
				"DELETE FROM " .  SendPress_Data::subscriber_event_table() ." " 
				);
	}

	static function delete_subscriber($subscriberID = false){
		if($subscriberID != false){
			global $wpdb;
			$wpdb->query( 
				$wpdb->prepare(
					"DELETE FROM " .  SendPress_Data::subscriber_table() ." WHERE subscriberID = %d",
					$subscriberID ) 
				);
			$wpdb->query( 
				$wpdb->prepare(
					"DELETE FROM " .  SendPress_Data::list_subcribers_table() ." WHERE subscriberID = %d",
					$subscriberID ) 
				);
			$wpdb->query( 
				$wpdb->prepare(
					"DELETE FROM " .  SendPress_Data::subscriber_event_table() ." WHERE subscriberID = %d",
					$subscriberID ) 
				);
		}

	}

	static function sync_emails_to_list($listid, $emails){
		global $wpdb;


		$query_get = "SELECT subscriberID FROM ". SendPress_Data::subscriber_table(). " WHERE email in ('".implode("','", $emails)."')";
		$data = $wpdb->get_results($query_get);
	
		$query_update_status ="INSERT IGNORE INTO ". SendPress_Data::list_subcribers_table(). "(subscriberID,listID,status,updated ) VALUES ";
		$total = count($data);
		$x = 0;
		if($total > 0){
			$good_ids =array();
			foreach ($data as $value) {
				$x++;
				$good_ids[] = $value->subscriberID;
				$query_update_status .= "( ".$value->subscriberID . "," . $listid . ",2,'" .date('Y-m-d H:i:s') . "') ";
				if($total > $x ){ $query_update_status .=",";}
			}
			$query_update_status .=";";
			$wpdb->query($query_update_status);
			/*
			$clean_list_query =  "DELETE FROM "	.SendPress_Data::list_subcribers_table(). " WHERE listID = ".$listid." AND subscriberID not in ('".implode("','", $good_ids)."')";
			$wpdb->query($clean_list_query);	
			*/
		}
	}


	static function drop_active_subscribers_for_sync( $listid  = false ) {
		if( $listid != false ){
			global $wpdb;
			$clean_list_query =  "DELETE FROM "	.SendPress_Data::list_subcribers_table(). " WHERE listID = ".$listid." AND status = 2";
			$wpdb->query($clean_list_query);	

		}

	}


	static function sync_lists_micro( $listid, $emails ) {



	}



	static function update_subscriber($subscriberID, $values){
		$table = SendPress_Data::subscriber_table();
		global $wpdb;
		
		$result = $wpdb->update($table,$values, array('subscriberID'=> $subscriberID) );
	}
	static function update_subscriber_by_email($email, $values){
		$table = SendPress_Data::subscriber_table();
		global $wpdb;
		$key = SendPress_Data::random_code();
		//$id = SendPress_Data::get_subscriber_by_email($email);
		$q = "INSERT INTO $table (email,wp_user_id,identity_key,join_date,firstname,lastname) VALUES (%s,%d,%s,%s,%s,%s) ON DUPLICATE KEY UPDATE wp_user_id=%d,firstname=%s,lastname=%s";
		$q = $wpdb->prepare($q,$email,$values['wp_user_id'],$key,date('Y-m-d H:i:s'),$values['firstname'],$values['lastname'],$values['wp_user_id'],$values['firstname'],$values['lastname']);
		$result = $wpdb->query($q);
		//$result = $wpdb->update($table, $values, array('email'=> $email) );
	}

	static function update_subscriber_by_wp_user($wp_user_id, $values){
		$table = SendPress_Data::subscriber_table();
		global $wpdb;
		$key = SendPress_Data::random_code();
		
		//Check by WordPress user ID
		$current = $wpdb->get_var( $wpdb->prepare("SELECT subscriberID FROM $table WHERE wp_user_id = %d", $wp_user_id) );
		if( $current !== null ){
			$wpdb->update($table , $values, array( 'subscriberID' => $current ) );
		} else {
			//Check by Email
			$current_email = $wpdb->get_var( $wpdb->prepare("SELECT subscriberID FROM $table WHERE email = %s", $values['email']) );
			if( $current_email !== null ){
				$wpdb->update($table , array('firstname' => $values['firstname'] , 'lastname'=>$values['lastname'], 'wp_user_id'=> $wp_user_id ), array( 'subscriberID' => $current_email ) );
			} else {
				//Add New
				$q = "INSERT INTO $table (email,wp_user_id,identity_key,join_date,firstname,lastname) VALUES (%s,%d,%s,%s,%s,%s) ON DUPLICATE KEY UPDATE wp_user_id=%d,firstname=%s,lastname=%s";
				$q = $wpdb->prepare($q,$values['email'],$wp_user_id,$key,date('Y-m-d H:i:s'),$values['firstname'],$values['lastname'],$wp_user_id,$values['firstname'],$values['lastname']);
				$result = $wpdb->query($q);
			}
		}
		//$result = $wpdb->update($table, $values, array('email'=> $email) );
	}


	static function get_subcribers_by_meta($meta_key = false, $meta_value = false, $list_id= false){

		if($meta_key == false){
			return false;
		}

		global $wpdb;
		$meta_table = SendPress_Data::subscriber_meta_table();
		$subscriber_table = SendPress_Data::subscriber_table();
		$list_table = SendPress_Data::list_subcribers_table();
		if($list_id == false){
			$query = "SELECT t2.*,t1.* FROM $meta_table as t1, $subscriber_table as t2  WHERE (t1.subscriberID = t2.subscriberID) ";
		} else {
		$query = "SELECT t2.*,t1.meta_value,t1.listID FROM $meta_table as t1, $subscriber_table as t2 , $list_table as t3 WHERE (t1.subscriberID = t2.subscriberID) AND (t2.subscriberID = t3.subscriberID) ";
		}


		$query .= $wpdb->prepare(" AND t1.meta_key = %s", $meta_key );
		
		if($meta_value != false){
			$query .=  $wpdb->prepare(" AND t1.meta_value = %s", $meta_value); 
		}

		if($list_id != false){
			$query .=  $wpdb->prepare(" AND t3.subscriberID = t1.subscriberID AND t3.listID = %d AND t3.status = 2 ", $list_id); 
		}

		return $wpdb->get_results($query);


	}

	static function get_subscriber_meta($subscriber_id =false, $meta_key =false, $list_id = false, $multi = false){
		if($subscriber_id == false){
			return false;
		}

		global $wpdb;
		$meta_table = SendPress_Data::subscriber_meta_table();
		$subscriber_table = SendPress_Data::subscriber_table();
		$list_table = SendPress_Data::list_subcribers_table();
		if($meta_key == false){
			$query = $wpdb->prepare("SELECT meta_key,meta_value FROM $meta_table WHERE subscriberID = %s",$subscriber_id);
			if($list_id != false){
				$query .= $wpdb->prepare(" AND listID = %s ", $list_id );
			}
		} else {
			$query = $wpdb->prepare("SELECT meta_value FROM $meta_table WHERE subscriberID = %d AND meta_key = %s ",$subscriber_id, $meta_key);
			if($list_id != false){
				$query .= $wpdb->prepare(" AND listID = %s ", $list_id );
			}
			if($multi == false){
				$query .= " ORDER BY smeta_id DESC LIMIT 1";
				return $wpdb->get_var($query);
			} else {
				$query .= " ORDER BY smeta_id DESC ";
			}

		}

		

		return $wpdb->get_results($query);

	}
	static function add_subscriber_meta($subscriber_id,$meta_key,$meta_value,$list_id = false){
		global $wpdb;
		$meta_table = SendPress_Data::subscriber_meta_table();
		
		if($list_id == false){
			$list_id = 0;
		}

		return $wpdb->insert( $meta_table, array('subscriberID'=>$subscriber_id,'meta_key' => $meta_key , 'meta_value' => $meta_value ,'listID'=>$list_id) );
	}

	static function update_subscriber_meta($subscriber_id,$meta_key,$meta_value,$list_id = false){
		global $wpdb;
		$meta_table = SendPress_Data::subscriber_meta_table();
		$has_data = SendPress_Data::get_subscriber_meta( $subscriber_id, $meta_key, $list_id, true );
		if(empty($has_data)){
			return SendPress_Data::add_subscriber_meta( $subscriber_id, $meta_key, $meta_value, $list_id );
		} else {
			return $wpdb->update( $meta_table, array('meta_value'=>$meta_value), array('subscriberID'=>$subscriber_id,'meta_key' => $meta_key , 'meta_value' => $has_data[0]->meta_value ) );
		}


	}

	static function get_most_active_subscriber( $limit = 10 ){
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$query = "select count(subscriberID), subscriberID  from $table group by subscriberID order by count(subscriberID) DESC LIMIT $limit";
		return $wpdb->get_results( $query );
	}

	
	function export_subscirbers($listID = false){
		global $wpdb;
		if($listID){
        $query = "SELECT t1.*, t3.status FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID .")";
        } else {
            $query = "SELECT * FROM " .  SendPress_Data::subscriber_table();
        }

        return $wpdb->get_results( $query );
	}



	static function get_subscriber($subscriberID, $listID = false){
		if($listID){
        	$query = "SELECT t1.*, t3.status FROM " .  self::subscriber_table() ." as t1,". self::list_subcribers_table()." as t2,". self::subscriber_status_table()." as t3 " ;

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID ." AND t2.subscriberID = ". $subscriberID .")";
        } else {
            $query = "SELECT * FROM " .  self::subscriber_table() ." WHERE subscriberID = ". $subscriberID ;
        }

        return self::wpdbQuery($query, 'get_row');
	}

	static function get_active_subscribers_lists($list_ids = array() ){
		global $wpdb;
		$lists = implode(',', $list_ids);
		$query = "SELECT t1.subscriberID,t1.email, t3.status, t2.listid, count(*) FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.status = 2) AND (t2.listID in  ( ". $lists ."  )) GROUP BY t1.subscriberID  ";
        
     
        return $wpdb->get_results( $query );
	}

	static function get_active_subscribers_lists_with_id($list_ids = array() , $id = 0 ){
		global $wpdb;
		$lists = implode(',', $list_ids);
		$get = intval( SendPress_Option::get('queue-per-call' , 1000 ) );
		$query = "SELECT t1.subscriberID,t1.email, t3.status, t2.listid, count(*) FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.status = 2) AND (t2.listID in  ( ". $lists ."  )) AND t1.subscriberID > ".$id." GROUP BY t1.subscriberID LIMIT " . $get;
        
     
        return $wpdb->get_results( $query );
	}


	static function get_active_subscribers_count($list_ids = array() ){
		global $wpdb;
		$lists = implode(',', $list_ids);
		$query = "SELECT  t1.subscriberID FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.status = 2) AND (t2.listID in  ( ". $lists ."  )) GROUP BY t1.subscriberID  ";
        
        $x =$wpdb->get_results( $query );

        return count($x);
	}



	static function set_subscriber_status($listID, $subscriberID, $status = 0) {
		$table = self::list_subcribers_table();
		$result = self::wpdbQuery("SELECT id FROM $table WHERE listID = $listID AND subscriberID = $subscriberID ", 'get_var');
		
		if($result == false){
			$result = self::wpdbQuery("INSERT INTO $table (listID, subscriberID, status, updated) VALUES( '" . $listID . "', '" . $subscriberID . "','".$status."','".date('Y-m-d H:i:s')."')", 'query');
		}
		return $result;	
	}
	
	static function update_subscriber_status( $listID, $subscriberID ,$status, $event = true){
		$table = SendPress_Data::list_subcribers_table();

		$check = SendPress_Data::get_subscriber_list_status($listID, $subscriberID);
		
		if( $check == false ){
			SendPress_Data::set_subscriber_status($listID,$subscriberID,$status);
		} else {
			global $wpdb;
			$result = $wpdb->update($table,array('status'=>$status,'updated'=>date('Y-m-d H:i:s')), array('subscriberID'=> $subscriberID,'listID'=>$listID) );
		}

		if($event == true){
		//add event for notification tracking
		SendPress_Data::add_subscribe_event($subscriberID, $listID, $status);
		} 
		return SendPress_Data::get_subscriber_list_status($listID, $subscriberID);
	}

	static function remove_subscriber_status( $list_id = false , $subscriberID = false){
		if($list_id !== false && is_numeric( $list_id ) && $subscriberID !== false){
			global $wpdb;
			$table = self::list_subcribers_table();
			$wpdb->query( $wpdb->prepare("DELETE FROM $table WHERE listID = %d AND subscriberID = %d ", $list_id, $subscriberID) );
		}
	}


	static function get_subscriber_list_status( $listID,$subscriberID ) {
		$table = SendPress_Data::list_subcribers_table();
		$result = SendPress_Data::wpdbQuery("SELECT t3.status,t2.updated,t3.statusid FROM ". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 WHERE t2.subscriberID = $subscriberID AND t2.listID = $listID AND t2.status = t3.statusid ", 'get_row');
		return $result;	
	}

	static function is_subsriber_on_list($lid,$sid){
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();
		$id = $wpdb->get_var( $wpdb->prepare("SELECT id FROM $table WHERE listID = %d AND subscriberID = %d ", $lid, $sid) );
		if($id > 0 ){
			return true;
		}
		return false;
	}


	static function get_subscriber_events($sid){
		global $wpdb;
			$table  = SendPress_Data::subscriber_event_table();
			return $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table WHERE subscriberID = %d order by eventID DESC", $sid) );

	}

	static function get_optin_events($limit = 10){
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		return $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table WHERE type = 'optin' order by eventID DESC LIMIT %d", $limit) );

	}

	static function register_event( $event='unknown_event_type' , $sid = null , $rid = null  ){
		global $wpdb;

		$event_data = array(
			'eventdate'=>SendPress_Data::gmdate(),
			'subscriberID' => $sid,
			'reportID' => $rid,
			'type'=> $event
		);
		$wpdb->insert( SendPress_Data::subscriber_event_table(),  $event_data);

	}


	static function add_subscriber_event( $sid, $rid, $lid=null, $uid=null, $ip=null, $device_type=null, $device=null, $type='confirm' ){
		global $wpdb;

		$event_data = array(
			'eventdate'=>SendPress_Data::gmdate(),
			'subscriberID' => $sid,
			'reportID' => $rid,
			'listID'=>$lid,
			'urlID'=>$uid,
			'ip'=>$ip,
			'devicetype'=> $device_type,
			'device'=> $device,
			'type'=>$type
		);
		
		$wpdb->insert( SendPress_Data::subscriber_event_table(),  $event_data);
	}


	static  function get_bad_post_count(){
		global $wpdb;
		$result = $wpdb->get_var("select count(*) from ". $wpdb->posts ." where post_type='sptemplates' and post_name not in('sp-template-user-style','sp-template-default-style','sp-template-double-optin')");
		return $result;
	}


	static function delete_extra_posts(){
		global $wpdb;
		$wpdb->query("delete from ". $wpdb->posts ." where post_type='sptemplates' and post_name not in('sp-template-user-style','sp-template-default-style','sp-template-double-optin')");


	}

	static function add_subscribe_event( $sid, $lid, $type ){
		global $wpdb;

		$event_type = 'unknown_event_type';
		if( is_numeric($type) ){

			if($type == 2){
				$event_type = 'subscribed';
			}elseif($type == 3){
				$event_type = 'unsubscribed';
			}elseif($type==4){
				$event_type = 'bounce';
			}elseif($type==1){
				$event_type = 'optin';
			}
		}

		$event_data = array(
			'eventdate'=>SendPress_Data::gmdate(),
			'subscriberID' => $sid,
			'listID'=>$lid,
			'type'=>$event_type
		);
		
		$wpdb->insert( SendPress_Data::subscriber_event_table(),  $event_data);

		//if instant, check if we need a notification and send one
		SendPress_Notifications_Manager::send_instant_notification($event_data);
	}

	static function unsubscribe_from_list( $sid, $rid, $lid ) {
		global $wpdb;
		$stat = get_post_meta($rid, '_unsubscribe_count', true );
		if($stat ==''){
			$stat = 1;
		} else{
			$stat++;
		}
		update_post_meta($rid, '_unsubscribe_count', $stat );
		$wpdb->update( SendPress_Data::list_subcribers_table() , array('status'=> 3) , array('listID'=> $lid,'subscriberID'=>$sid ));
	}

	static function unsubscribe_from_all_lists( $sid ) {
		global $wpdb;
		$wpdb->update( SendPress_Data::list_subcribers_table() , array('status'=> 3) , array('subscriberID'=>$sid ));
	}


	static function get_subscriber_by_email( $email ){
		global $wpdb;
		$table = SendPress_Data::subscriber_table();
		$result = $wpdb->get_var( $wpdb->prepare("SELECT subscriberID FROM $table WHERE email = %s ", $email) );
		return $result;
	}

	static function add_subscriber($values){
		$table =  SendPress_Data::subscriber_table();
		$email = $values['email'];

		if(!isset($values['join_date'])){
			$values['join_date'] = SendPress_Data::gmdate();
		}
		if(!isset($values['identity_key'])){
			$values['identity_key'] =  SendPress_Data::random_code();
		}

		if( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
			return false;
		}

		$result = SendPress_Data::get_subscriber_by_email($email);
		
		if(	$result ){ return $result; }
		global $wpdb;
		$result = $wpdb->insert($table,$values);
		//$result = $this->wpdbQuery("SELECT @lastid2 := LAST_INSERT_ID()",'query');
		return $wpdb->insert_id;
	}


	static function bounce_email($email){
		$id = SendPress_Data::get_subscriber_by_email($email);
		if($id !== false){
			$lists = SendPress_Data::get_lists_for_subscriber($id);
			foreach ($lists as $list) {
				if( $list->status == 2 ) {
					$report_id = SendPress_Data::get_last_send( $id );
					SendPress_Data::update_subscriber_status($list->listID, $id, 4 , false);
					//( $sid, $rid, $uid, $ip , $device_type, $device, $type='confirm' )
					//( $sid, $rid, $lid=null, $uid=null, $ip=null, $device_type=null, $device=null, $type='confirm' )
					SendPress_Data::add_subscriber_event( $id,$report_id,$list->listID,null ,null, null, null, 'bounce');
				}
			}

		}

	}


	static function bounce_subscriber_by_id($sid = false){
		if($sid !== false){
			$lists = SendPress_Data::get_lists_for_subscriber($sid);
			foreach ($lists as $list) {
				if( $list->status == 2 ) {
					SendPress_Data::update_subscriber_status($list->listID, $sid, 4 , false);
					//( $sid, $rid, $uid, $ip , $device_type, $device, $type='confirm' )
					//( $sid, $rid, $lid=null, $uid=null, $ip=null, $device_type=null, $device=null, $type='confirm' )
				}
			}

		}

	}

	static function get_last_send( $sid ){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();//SELECT * FROM table_name WHERE MONTH(date_column) = 4;
		$result = $wpdb->get_var( $wpdb->prepare("SELECT reportID FROM $table WHERE type = 'send' AND subscriberID = %d ORDER BY eventdate DESC",$sid));
		return $result;

	}

	static function get_lists_for_subscriber( $value ) {
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();
		$result = $wpdb->get_results("SELECT * FROM $table WHERE subscriberID = '$value'");
		return $result;	
	}
	static function get_list_ids_for_subscriber( $value ) {
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();
		$result = $wpdb->get_results("SELECT listID FROM $table WHERE subscriberID = '$value'");
		return $result;	
	}


	static function subscribe_user($listid, $email, $first, $last, $status = 2, $custom = array()){
		
		$success = false;
		$subscriberID = SendPress_Data::add_subscriber(array('firstname' => $first,'lastname' => $last,'email' => $email));
		
		if( false === $subscriberID ){
			return false;
		}
		$args = array( 'post_type' => 'sendpress_list','numberposts'  => -1,
	    'offset'          => 0,
	    'orderby'         => 'post_title',
	    'order'           => 'DESC', );
		$lists = get_posts( $args );

		$listids = explode(',', $listid);
		
	    //$lists = $s->getData($s->lists_table());
	    //$listids = array();

		
		if( $status == 2 && SendPress_Option::is_double_optin() ){
			$status = 1;
			SendPress_Manager::send_optin( $subscriberID, $listids, $lists);
		}
		
		foreach($lists as $list){
			if( in_array($list->ID, $listids) ){
				$current_status = SendPress_Data::get_subscriber_list_status( $list->ID, $subscriberID );
				if( empty($current_status) || ( isset($current_status->status) && $current_status->status < 2 ) ){
					$success = SendPress_Data::update_subscriber_status($list->ID, $subscriberID, $status);
				} else {
					$success = true;
				}
				foreach ($custom as $key => $value) {
					SendPress_Data::update_subscriber_meta( $subscriberID, $key, $value, $list->ID );
				}

			}
		}

		

		return $success;
	}

	static function read_file_to_str($file){
		return file_get_contents($file);
	}
	
	static function import_csv_array($data, $map, $list){

		global $wpdb;
		$query ="INSERT IGNORE INTO ". SendPress_Data::subscriber_table(). "(email,firstname,lastname,join_date,identity_key) VALUES ";
		$total = count($data);
		$emails_added = array();
		$x = 0;
		foreach($data as $key_line => $line){
			$values ="";

			if( array_key_exists('email',$map)  ){
				$email = $line[$map['email']];
				
				if(is_email($email)){

					$values.="'".mysql_real_escape_string($email,$wpdb->dbh)."',";
					$emails_added[] = $email;
					if(array_key_exists('firstname',$map)){
						$values.="'".mysql_real_escape_string(trim($line[$map['firstname']]),$wpdb->dbh)."',";
					} else {
						$values .= "'',";
					}
					
					if(array_key_exists('lastname',$map)){
						$values.="'".mysql_real_escape_string(trim($line[$map['lastname']]),$wpdb->dbh)."',";
					} else {
						$values .= "'',";
					}


					$values .=  "'".date('Y-m-d H:i:s') ."',";
					$values .= "'".SendPress_Data::random_code()."'";
					
					$query .= " ($values) ";

				}


			}
			$x++;
			if($total > $x && $values != ""){ $query .=",";}
			
			unset($data[$key_line]);
		}
		$query .=";";
		$wpdb->query($query);
		
		$query_get = "SELECT subscriberID FROM ". SendPress_Data::subscriber_table(). " WHERE email in ('".implode("','", $emails_added)."')";
		
		$data = $wpdb->get_results($query_get);

		$txt = '';
		foreach ($data as $value) {
			$txt .= $value->subscriberID . ',';
		}
		$txt .= '0';

		$current_status = "SELECT * FROM ". SendPress_Data::list_subcribers_table(). " WHERE subscriberID in (". $txt .") AND listID = " . $list ;
		
		
		$my_data_x = $wpdb->get_results($current_status, OBJECT_K);

	
		$query_update_status ="INSERT IGNORE INTO ". SendPress_Data::list_subcribers_table(). "(subscriberID,listID,status,updated ) VALUES ";
		
		$total = count($data);
		$x = 0;
		if($total > 0){
		foreach ($data as $value) {
			$x++;
			if( !isset( $my_data_x[ $value->subscriberID ]) ){
				$query_update_status .= "( ".$value->subscriberID . "," . $list . ",2,'" .date('Y-m-d H:i:s') . "') ";
			
				if($total > $x ){ $query_update_status .=",";}
			}
		}
		$query_update_status .=";";
		$wpdb->query($query_update_status);
		}
		
	}

	static function csv_to_array($csv_file_content , $rows_to_read = 0 , $delimiter = ',' , $enclosure = '' ){
        $data = array();

        $csv_data_array = explode( "\n" , $csv_file_content );
        $i=1;
        foreach($csv_data_array as $csv_line){
            if($rows_to_read!=0 && $i> $rows_to_read) return $data;

            if(function_exists('str_getcsv')){
				$data[] = str_getcsv($csv_line, $delimiter,$enclosure);
                
            }else{
               $data[]= SendPress_Data::break_csv_apart($csv_line, $delimiter,$enclosure);
            }

            $i++;
        }

        return $data;
    }


	/*
	*
	*	Creates an array from a posted textarea
	*	
	*	expects 3 fields or less: @sendpress.me, fname, lname
	*
	*/
	static function subscriber_csv_post_to_array($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n") { 
	    $r = array(); 
	    $rows = explode($terminator,trim($csv)); 
	    $names = array_shift($rows); 
	    $names = explode(',', $names);
		$nc = count($names);
 
	    foreach ($rows as $row) { 
	        if (trim($row)) { 
	        	$needle = substr_count($row, ',');
	        	if($needle == false){
	        		$row .=',,';
	        	} 
	        	if($needle == 1){
	        		$row .=',';
	        	} 

	            $values = explode(',' , $row);
	            if (!$values) $values = array_fill(0,$nc,null); 
	            $r[] = array_combine($names,$values); 
	        } 
	    } 
	    return $r; 
	} 

    static function break_csv_apart($csv_line , $delimiter , $enclose , $preserve=false){
        $response = array();
        $n = 0;
        if(empty($enclose)){
            $response = explode($delimiter, $csv_line);
        }else{
            $dtx = explode($enclose, $csv_line);
            foreach($dtx as $item){
                if($n++%2){
                    array_push($response, array_pop($response) . ($preserve ? $enclose : '') . $item.( $preserve ? $enclose : ''));
                }else{
                    $del = explode($delimiter, $item);
                    array_push($response, array_pop($response) . array_shift($del));
                    $response = array_merge($response, $del);
                }
            }
        }

        return $response;
    }

	static function random_code() {
	    $now = time();
	    $random_code = substr( $now, strlen( $now ) - 3, 3 ) . substr( md5( uniqid( rand(), true ) ), 0, 8 ) . substr( md5( $now . rand() ), 0, 4);
	    return $random_code;
	}

	/**
	 * Gets all currently created subscriber status out of the DB
	 * @return array
	 */
	static function get_statuses( ) {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM " . SendPress_Data::subscriber_status_table() );
	}

	// COUNT DATA
	static function get_count_subscribers($listID = false, $status = 2) {
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();

		$query = "SELECT COUNT(t1.subscriberID) FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3";

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND (t2.status = t3.statusid ) AND (t2.status = %d) ";
            if($listID  !== false){
            	$query .= "AND (t2.listID =  %d)";
            } else {
            	 $query .= " ";
            }
          //  "SELECT COUNT(*) FROM $table WHERE listID = $listID AND status = $status"
		$count = $wpdb->get_var( $wpdb->prepare( $query, $status, $listID));
		return $count;
	}

	static function get_total_subscribers(){
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();
		$query = "SELECT DISTINCT subscriberID FROM " . $table . " WHERE status = 2";
		$count = $wpdb->get_results( $query );
		return count($count);
	}


 	static function bd_nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));
        
        // is this a number?
        if(!is_numeric($n)) return false;
        
        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),1).' trillion';
        else if($n>1000000000) return round(($n/1000000000),1).' billion';
        else if($n>1000000) return round(($n/1000000),1).' million';
        else if($n>99999) return round(($n/1000),0).'K';
        
        return number_format($n);
    }



	/********************* END SUBSCRIBER static functionS **************************/

	/********************* TEMPLATE POST static functionS **************************/


	/**
	* Takes a key and looks up or creates a template post for storing data.
	* 
	* 
	* @param mixed $_token Description.
	*
	* @access public
	*
	* @return mixed Value.
	*/
	static function get_template_id_by_slug( $slug ) {
		global $wpdb;
		$_id = 0;
		$slug = strtolower( str_replace( ' ', '_', $slug ) );
		if ( $slug ) {
			// Tell the static function what to look for in a post.
			$_args = array('post_parent' => '0', 'post_type' => 'sptemplates', 'name' => 'sp-template-' . $slug, 'post_status' => 'draft', 'comment_status' => 'closed', 'ping_status' => 'closed' );



			 $querystr = "
			    SELECT $wpdb->posts.* 
			    FROM $wpdb->posts 
			    WHERE $wpdb->posts.post_name = 'sp-template-$slug'
			    ORDER BY $wpdb->posts.post_date DESC
			 ";

 			$_posts = $wpdb->get_results($querystr, OBJECT);
 			//print_r($_posts);
			// look in the database for a "silent" post that meets our criteria.
			//$_posts = get_posts( $_args );
			// If we've got a post, loop through and get it's ID.
			if ( count( $_posts ) ) {
				$_id = $_posts[0]->ID;
			} else {
				// If no post is present, insert one.
				// Prepare some additional data to go with the post insertion.
				$_words = explode( '_', $slug );
				$_title = join( ' ', $_words );
				$_title = ucwords( $_title );
				$_post_data = array( 'post_title' => $_title );
				$_post_data = array( 'post_name' => $_args['name'] );
				
				//$_post_data = array( 'post_name' => );
				$_post_data = array_merge( $_post_data, $_args );

				$_id = wp_insert_post( $_post_data );
			} // End IF Statement
		}
		return $_id;
	} 

	/**
	* Takes a key and looks up or creates a template post for storing data.
	* 
	* 
	* @param mixed $_token Description.
	*
	* @access public
	*
	* @return mixed Value.
	*/
	static function get_html_template_id_by_slug( $slug ) {
		global $wpdb;
		$_id = 0;
		$slug = strtolower( str_replace( ' ', '_', $slug ) );
		if ( $slug ) {
			// Tell the static function what to look for in a post.
			$_args = array('post_parent' => '0', 'post_type' => 'sptemplates', 'name' => 'sp-template-' . $slug, 'post_status' => 'pending', 'comment_status' => 'closed', 'ping_status' => 'closed' );



			 $querystr = "
			    SELECT $wpdb->posts.* 
			    FROM $wpdb->posts 
			    WHERE $wpdb->posts.post_name = 'sp-template-$slug'
			    ORDER BY $wpdb->posts.post_date DESC
			 ";

 			$_posts = $wpdb->get_results($querystr, OBJECT);
 			//print_r($_posts);
			// look in the database for a "silent" post that meets our criteria.
			//$_posts = get_posts( $_args );
			// If we've got a post, loop through and get it's ID.
			if ( count( $_posts ) ) {
				$_id = $_posts[0]->ID;
			} else {
				// If no post is present, insert one.
				// Prepare some additional data to go with the post insertion.
				$_words = explode( '_', $slug );
				$_title = join( ' ', $_words );
				$_title = ucwords( $_title );
				$_post_data = array( 'post_title' => $_title );
				$_post_data = array( 'post_name' => $_args['name'] );
				
				//$_post_data = array( 'post_name' => );
				$_post_data = array_merge( $_post_data, $_args );

				$_id = wp_insert_post( $_post_data );
			} // End IF Statement
		}
		return $_id;
	} 

	/**
	* Takes a key and looks up or creates a template post for storing data.
	* 
	* 
	* @param mixed $_token Description.
	*
	* @access public
	*
	* @return mixed Value.
	*/
	static function get_template_by_slug( $slug ) {
		global $wpdb;
		$_id = 0;
		$slug = strtolower( str_replace( ' ', '_', $slug ) );
		if ( $slug ) {
			// Tell the static function what to look for in a post.
			$_args = array('post_parent' => '0', 'post_type' => 'sptemplates', 'name' => 'sp-template-' . $slug, 'post_status' => 'draft', 'comment_status' => 'closed', 'ping_status' => 'closed' );
			// look in the database for a "silent" post that meets our criteria.
			//$_posts = get_posts( $_args );
			 $querystr = "
			    SELECT $wpdb->posts.* 
			    FROM $wpdb->posts 
			    WHERE $wpdb->posts.post_name = 'sp-template-$slug'
			    ORDER BY $wpdb->posts.post_date DESC
			 ";

 			$_posts = $wpdb->get_results($querystr, OBJECT);
			// If we've got a post, loop through and get it's ID.
			if ( count( $_posts ) ) {
				$_id = $_posts[0]->ID;
			} else {
				// If no post is present, insert one.
				// Prepare some additional data to go with the post insertion.
				$_words = explode( '_', $slug );
				$_title = join( ' ', $_words );
				$_title = ucwords( $_title );
				$_post_data = array( 'post_title' => $_title );
				$_post_data = array( 'post_name' => $_args['name'] );
				
				//$_post_data = array( 'post_name' => );
				$_post_data = array_merge( $_post_data, $_args );

				$_id = wp_insert_post( $_post_data );
			} // End IF Statement
		}
		return get_post( $_id );
	} 


	/********************* END TEMPLATE POST static functionS **************************/

	/********************* PUBLIC VIEW static functionS **************************/
	/**
	 * Get field class name
	 *
	 * @param string $type Field type
	 *
	 * @return bool|string Field class name OR false on failure
	 */
	static function get_public_view_class( $view = false){
		
		if($view !== false){
			$view = str_replace('-',' ',$view);
			$view  = ucwords( $view );
			$view = str_replace(' ','_',$view);
			$class = "SendPress_Public_View_{$view}";

			if ( class_exists( $class ) )
				return $class;
		}
		
		return "SendPress_Public_View";
	}

	/********************* END PUBLIC VIEW FUNCTIONS **************************/


	static function set_double_optin_content( ){
		$optin = self::get_template_by_slug('double-optin');
		$optin->post_content = self::optin_content();
		$optin->post_title = self::optin_title();
		delete_transient( 'sendpress_email_html_'. $optin->ID );
		wp_update_post( $optin );
	}

	static function optin_title(){
		return "Please respond to join the *|SITE:TITLE|* email list.";
	}

	static function optin_content(){
		return "Howdy.

We're ready to send you emails from *|SITE:TITLE|*, but first we need you to confirm that this is what you really want.

If you want *|SITE:TITLE|* content delivered by email, all you have to do is click the link below. Thanks!

-----------------------------------------------------------
CONFIRM BY VISITING THE LINK BELOW:

*|SP:CONFIRMLINK|*

Click the link above to give us permission to send you
information.  It's fast and easy!  If you cannot click the
full URL above, please copy and paste it into your web
browser.

-----------------------------------------------------------
If you do not want to confirm, simply ignore this message.
";
	}












	static function get_key(){
		$key = SendPress_Option::get('email_key');
		if($key == false){
			$key = sha1(microtime(true).mt_rand(10000,90000));
			SendPress_Option::set('email_key', $key);
		}
		return $key;
	}



	static function encrypt( $message ) {
		$key = self::get_key();
		$message = json_encode($message);
		$encstring = '';
		$keylength = strlen($key);
		$messagelength = strlen($message);
		/*
		for($i=0;$i<=$messagelength - 1;$i++)
		{
			$msgord = ord(substr($message,$i,1));
			$keyord = ord(substr($key,$i % $keylength,1));

			if ($msgord + $keyord <= 255){$encstring .= chr($msgord + $keyord);}
			if ($msgord + $keyord > 255){$encstring .= chr(($msgord + $keyord)-256);}
		}
		*/
		
		return urlencode(str_replace("=","",base64_encode( $message)));
	}

	static function decrypt($message) {
		$key = self::get_key();
		$decstring ='';
		$keylength = strlen($key);
		$message = base64_decode($message);

		$json = json_decode($message);

		if( is_object($json) && !is_null($json) ){
			return $json;
		} else {
			$messagelength = strlen($message);
			for($i=0;$i<=$messagelength - 1;$i++)
			{
				$msgord = ord(substr($message,$i,1));
				$keyord = ord(substr($key,$i % $keylength,1));

				if ($msgord - $keyord >= 0){$decstring .= chr($msgord - $keyord);}
				if ($msgord + $keyord < 0){$decstring .= chr(($msgord - $keyord)+256);}
			}
			return json_decode($decstring);
		}
	}


}