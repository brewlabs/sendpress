<?php
// SendPress Required Class: SendPress_Data

class SendPress_Data extends SendPress_DB_Tables {

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

	/********************* REPORTS FUNCTIONS **************************/	

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
	function get_url_by_report_url( $id , $url_string ){
		$table = self::report_url_table();
		$result = self::wpdbQuery("SELECT * FROM $table WHERE reportID = '$id' AND url = '$url_string'", 'get_results');
		return $result;	
	}


	function insert_report_url( $url ){
		global $wpdb;
		$wpdb->insert( self::report_url_table(),  $url);
		return $wpdb->insert_id;
	}



	/********************* END REPORTS FUNCTIONS **************************/

	/********************* SUBSCRIBER FUNCTIONS **************************/


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
		$table = self::list_subcribers_table();

		$check = self::get_subscriber_list_status($listID, $subscriberID);
		if($check == false && $status == '2'){
			self::set_subscriber_status($listID,$subscriberID,$status);

		} else {
			global $wpdb;
			$result = $wpdb->update($table,array('status'=>$status,'updated'=>date('Y-m-d H:i:s')), array('subscriberID'=> $subscriberID,'listID'=>$listID) );
		}
		return self::get_subscriber_list_status($listID, $subscriberID);
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
		
		$wpdb->insert( self::subscriber_event_table(),  $event_data);

		//print_r($this->get_open_without_id($rid,$sid));
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
			// look in the database for a "silent" post that meets our criteria.
			$_posts = get_posts( $_args );
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
			$_posts = get_posts( $_args );
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
		
		for($i=0;$i<=$messagelength - 1;$i++)
		{
			$msgord = ord(substr($message,$i,1));
			$keyord = ord(substr($key,$i % $keylength,1));

			if ($msgord + $keyord <= 255){$encstring .= chr($msgord + $keyord);}
			if ($msgord + $keyord > 255){$encstring .= chr(($msgord + $keyord)-256);}
		}
		return urlencode(base64_encode($encstring));
	}

	function decrypt($message) {
		$key = self::get_key();
		$decstring ='';
		$keylength = strlen($key);
		$message = base64_decode($message);
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