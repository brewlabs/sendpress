<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Data extends SendPress_DB_Tables {

	function nonce(){
		return 'sendpress-is-awesome';
	}

	/********************* BASE FUNCTIONS **************************/	

	function wpdbQuery($query, $type) {
		global $wpdb;
		$result = $wpdb->$type( $query );
		return $result;
	}

	function wpdbQueryArray($query) {
		global $wpdb;
		$result = $wpdb->get_results( $query , ARRAY_N);
		return $result;
	}

	
	/********************* BASE FUNCTIONS **************************/

	/********************* QUEUE FUNCTIONS **************************/

	function delete_queue_emails(){
		$table = self::queue_table();
		self::wpdbQuery("DELETE FROM $table", 'query');
	}

	function queue_email_process($id){
		$table = self::queue_table();
		global $wpdb;
		$result = $wpdb->update( $table ,array('inprocess'=>'1','last_attempt'=>date('Y-m-d H:i:s')), array('id'=> $id) );

	}

	function emails_in_queue($id = false){
		global $wpdb;
		$table = self::queue_table();
		if($id == false){
			$query = "SELECT COUNT(*) FROM $table";
		} else {
			$query = $wpdb->prepare("SELECT COUNT(*) FROM $table where emailID = %d", $id );
		}	
		return $wpdb->get_var( $query );
	}


	/********************* QUEUE FUNCTIONS **************************/





	/********************* REPORTS FUNCTIONS **************************/	

   
    function update_report_sent_count( $id ) {
    	if( $sent=get_post_meta($id , '_sent_total', true)){
    		$sent++;
    	} else {
    		$sent = 1;
    	}
   		update_post_meta( $id ,'_sent_total' , $sent );
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
    

 	function get_url_by_id( $id ) {
 		global $wpdb;
		$table = self::report_url_table();
		$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE urlID = %d", $id) );
		return $result;	
	}

	function get_url_by_report_url( $id , $url_string ){
		$table = self::report_url_table();
		$result = self::wpdbQuery("SELECT * FROM $table WHERE reportID = '$id' AND url = '$url_string'", 'get_results');
		return $result;	
	}

	function get_open_without_id($rid, $sid){
		global $wpdb;
		$table = SendPress_Data::subscriber_event_table();
		$result = $wpdb->query( $wpdb->prepare("SELECT * FROM $table WHERE reportID = %d AND subscriberID = %d AND type='open' ", $rid, $sid ) );
		return $result;
	}


	function insert_report_url( $url ){
		global $wpdb;
		$wpdb->insert( self::report_url_table(),  $url);
		return $wpdb->insert_id;
	}


	function get_clicks_and_opens($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	function get_clicks_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' AND type = 'click' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	function get_opens_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(eventID) as count,date(eventdate) as day FROM $table WHERE reportID = '$rid' AND type = 'open' GROUP BY date(eventdate) ORDER BY eventID DESC ;");
		return $result;
	}

	function get_opens_unique_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(t.eventID) as count,date(t.eventdate) as day FROM  (Select eventdate,eventID FROM $table  WHERE  reportID = '$rid' AND type = 'open' GROUP BY subscriberID) as t GROUP BY date(eventdate) ORDER BY eventID DESC ");
		return $result;
	}

	function get_clicks_unique_count($rid){
		global $wpdb;
		$table = self::subscriber_event_table();
		$result = $wpdb->get_results("SELECT COUNT(t.eventID) as count,date(t.eventdate) as day FROM  (Select eventdate,eventID FROM $table  WHERE  reportID = '$rid' AND type = 'click' GROUP BY subscriberID) as t GROUP BY date(eventdate) ORDER BY eventID DESC ");
		return $result;
	}

	function track_click( $sid, $rid, $lid, $ip , $device_type, $device ){
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

	function track_open( $sid, $rid, $ip = null , $device_type=null, $device=null ){
		global $wpdb;
		$wpdb->insert( SendPress_Data::subscriber_event_table() , array('reportID'=> $rid,'subscriberID'=>$sid,'eventdate'=>date('Y-m-d H:i:s'),'type'=>'open' ,'ip'=>$ip,'devicetype'=> $device_type,'device'=> $device));
	}

	/********************* END REPORTS FUNCTIONS **************************/

	/********************* SUBSCRIBER FUNCTIONS **************************/

	function remove_all_subscribers( $list_id = false ){
		if($list_id !== false && is_numeric( $list_id )){
			global $wpdb;
			$table = self::list_subcribers_table();
			$wpdb->query( $wpdb->prepare("DELETE FROM $table WHERE listID = %d", $list_id) );
		}
	}


	function get_subscriber($subscriberID, $listID = false){
		if($listID){
        	$query = "SELECT t1.*, t3.status FROM " .  self::subscriber_table() ." as t1,". self::list_subcribers_table()." as t2,". self::subscriber_status_table()." as t3 " ;

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID ." AND t2.subscriberID = ". $subscriberID .")";
        } else {
            $query = "SELECT * FROM " .  self::subscriber_table() ." WHERE subscriberID = ". $subscriberID ;
        }

        return self::wpdbQuery($query, 'get_row');
	}

	function set_subscriber_status($listID, $subscriberID, $status = 0) {
		$table = self::list_subcribers_table();
		$result = self::wpdbQuery("SELECT id FROM $table WHERE listID = $listID AND subscriberID = $subscriberID ", 'get_var');
		
		if($result == false){
			$result = self::wpdbQuery("INSERT INTO $table (listID, subscriberID, status, updated) VALUES( '" . $listID . "', '" . $subscriberID . "','".$status."','".date('Y-m-d H:i:s')."')", 'query');
		}
		return $result;	
	}
	
	function update_subscriber_status( $listID, $subscriberID ,$status){
		$table = SendPress_Data::list_subcribers_table();

		$check = SendPress_Data::get_subscriber_list_status($listID, $subscriberID);
		
		if( $check == false ){
			SendPress_Data::set_subscriber_status($listID,$subscriberID,$status);
		} else {
			global $wpdb;
			$result = $wpdb->update($table,array('status'=>$status,'updated'=>date('Y-m-d H:i:s')), array('subscriberID'=> $subscriberID,'listID'=>$listID) );
		}
		return SendPress_Data::get_subscriber_list_status($listID, $subscriberID);
	}

	function get_subscriber_list_status( $listID,$subscriberID ) {
		$table = self::list_subcribers_table();
		$result = self::wpdbQuery("SELECT status,updated FROM $table WHERE subscriberID = $subscriberID AND listID = $listID", 'get_row');
		return $result;	
	}


	function add_subscriber_event( $sid, $rid, $lid, $ip , $device_type, $device ){
		global $wpdb;

		$event_data = array(
			'eventdate'=>date('Y-m-d H:i:s'),
			'subscriberID' => $sid,
			'reportID' => $rid,
			'urlID'=>$lid,
			'ip'=>$ip,
			'devicetype'=> $device_type,
			'device'=> $device,
			'type'=>'confirm'
		);
		
		$wpdb->insert( SendPress_Data::subscriber_event_table(),  $event_data);

		//print_r($this->get_open_without_id($rid,$sid));
	}

	function unsubscribe_from_list( $sid, $rid, $lid ) {
		global $wpdb;
		$stat = get_post_meta($rid, '_unsubscribe_count', true );
		$stat++;
		update_post_meta($rid, '_unsubscribe_count', $stat );
		$wpdb->update( SendPress_Data::list_subcribers_table() , array('status'=> 3) , array('listID'=> $lid,'subscriberID'=>$sid ));
	}

	function get_subscriber_by_email( $email ){
		global $wpdb;
		$table = SendPress_Data::subscriber_table();
		$result = $wpdb->get_var( $wpdb->prepare("SELECT subscriberID FROM $table WHERE email = %s ", $email) );
		return $result;
	}

	function add_subscriber($values){
		$table =  SendPress_Data::subscriber_table();
		$email = $values['email'];

		if(!isset($values['join_date'])){
			$values['join_date'] =  date('Y-m-d H:i:s');
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



	function subscribe_user($listid, $email, $first, $last){
		
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
		
		//error_log($listids);
		
	    //$lists = $s->getData($s->lists_table());
	    //$listids = array();

		$status = 2;
		if( SendPress_Option::is_double_optin() ){
			$status = 1;
			SendPress_Manager::send_optin( $subscriberID, $listids, $lists);

		}

		//print_r($lists);

		foreach($lists as $list){
			if( in_array($list->ID, $listids) ){
				$current_status = SendPress_Data::get_subscriber_list_status( $list->ID, $subscriberID );
				
				if($current_status->status < 2 ){
					$success = SendPress_Data::update_subscriber_status($list->ID, $subscriberID, $status);
				} else {
					$success = true;
				}
			}
		}

		return $success;
	}

	function random_code() {
	    $now = time();
	    $random_code = substr( $now, strlen( $now ) - 3, 3 ) . substr( md5( uniqid( rand(), true ) ), 0, 8 ) . substr( md5( $now . rand() ), 0, 4);
	    return $random_code;
	}

	/********************* END SUBSCRIBER FUNCTIONS **************************/

	/********************* TEMPLATE POST FUNCTIONS **************************/


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
	function get_template_id_by_slug( $slug ) {
		global $wpdb;
		$_id = 0;
		$slug = strtolower( str_replace( ' ', '_', $slug ) );
		if ( $slug ) {
			// Tell the function what to look for in a post.
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
	function get_template_by_slug( $slug ) {
		global $wpdb;
		$_id = 0;
		$slug = strtolower( str_replace( ' ', '_', $slug ) );
		if ( $slug ) {
			// Tell the function what to look for in a post.
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


	/********************* END TEMPLATE POST FUNCTIONS **************************/

	/********************* PUBLIC VIEW FUNCTIONS **************************/
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


	function set_double_optin_content( ){
		$optin = self::get_template_by_slug('double-optin');
		$optin->post_content = self::optin_content();
		$optin->post_title = self::optin_title();
		delete_transient( 'sendpress_email_html_'. $optin->ID );
		wp_update_post( $optin );
	}

	function optin_title(){
		return "Please respond to join the *|SITE:TITLE|* email list.";
	}

	function optin_content(){
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












	function get_key(){
		$key = SendPress_Option::get('email_key');
		if($key == false){
			$key = sha1(microtime(true).mt_rand(10000,90000));
			SendPress_Option::set('email_key', $key);
		}
		return $key;
	}



	function encrypt( $message ) {
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
		return urlencode(base64_encode($message));
	}

	function decrypt($message) {
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