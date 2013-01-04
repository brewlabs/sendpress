<?php
// SendPress Required Class: SendPress_Email
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( ! class_exists('SendPress_Email')){ 


/**
* SendPress_Options
*
* @uses     
*
* 
* @package  SendPRess
* @author   Josh Lyford
* @license  See SENPRESS
* @since 	0.8.7     
*/
class SendPress_Email {
	
	var $_sp = '';
	var $_email = '';
	var $post_info = false;

	private $_emailID = false;
	private $_id = false;
	private $_remove_links = false;
	private $_subscriber_id = false;
	private $_list_id = false;
	private $_list_ids = array();
	private $_purge = false;


	
	function SendPress_Email(){

		}

	function id($id = NULL){
		if ( ! isset( $id ) )
			return $this->_id;
		$this->_id = $id;
	}

	function subscriber_id($subscriber_id = NULL){
		if ( ! isset( $subscriber_id ) )
			return $this->_subscriber_id;
		$this->_subscriber_id = $subscriber_id;
	}

	function remove_links($remove_links = NULL){
		if ( ! isset( $remove_links ) )
			return $this->_remove_links;
		$this->_remove_links = $remove_links;
	}
	function purge($purge = NULL){
		if ( ! isset( $purge ) )
			return $this->_purge;
		$this->_purge = $purge;
	}


	function list_id($list_id = NULL){
		if ( ! isset( $list_id ) )
			return $this->_list_id;
		$this->_list_id = $list_id;
	}

	function list_ids($list_ids = NULL){
		if ( ! isset( $list_ids ) )
			return $this->_list_ids;
		$this->_list_ids = $list_ids;
	}

	function html(){
			global $wpdb;
			//$email =  $this->email();
			// Get any existing copy of our transient data
			if ( false === ( $body_html = get_transient( 'sendpress_report_body_html_'. $this->id() )  ) || ($this->purge() == true) ) {

			    // It wasn't there, so regenerate the data and save the transient
			    if(!$this->post_info){
			    	$this->post_info = get_post( $this->id() );
				}
			    $body_html = SendPress_Template::get_instance()->render( $this->post_info->ID, false, false , $this->remove_links() );
			    set_transient( 'sendpress_report_body_html_'. $this->id(), $body_html , 60*60*2 );

			}
			

			$open_info = array(
				"id"=>$this->subscriber_id(),
				"report"=> $this->id(),
				"view"=>"open"
			);
			$x = SendPress_Data::encrypt( $open_info );

			//[firstname]

			$tracker = "<img src='".site_url()."?sendpress=". $x ."' width='1' height='1'/></body>";
			$body_html = str_replace("</body>",$tracker , $body_html );
			$body_link			=	get_post_meta( $this->id() , 'body_link', true );

			//$pattern ="/(?<=href=(\"|'))[^\"']+(?=(\"|'))/";
			//$body_html = preg_replace( $pattern , site_url() ."?sendpress=link&fxti=".$subscriber_key."&spreport=". $this->id ."&spurl=$0", $body_html );
			
			$dom = new DomDocument();
			$dom->strictErrorChecking = false;
			@$dom->loadHtml($body_html);
			$aTags = $dom->getElementsByTagName('a');
			foreach ($aTags as $aElement) {
				$href = $aElement->getAttribute('href');
				//ADD TO DB?

				$urlinDB = SendPress_Data::get_url_by_report_url( $this->id(), $href );
				if(!isset($urlinDB[0])){
				
					$urlData = array(
						'url' => trim($href),
						'reportID' => $this->id(),
					);
					$urlID = SendPress_Data::insert_report_url( $urlData );
				
				} else {
					$urlID  = $urlinDB[0]->urlID;
				}
				$link = array(
					"id"=>$this->subscriber_id(),
					"report"=> $this->id(),
					"urlID"=> $urlID,
					"view"=>"link"
				);
				$x = SendPress_Data::encrypt( $link );

				$href = site_url() ."?sendpress=".$x;
				$aElement->setAttribute('href', $href);
			}
			$body_html = $dom->saveHtml();

			$link = array(
				"id"=>$this->subscriber_id(),
				"report"=> $this->id(),
				"urlID"=> '0',
				"view"=>"manage",
				"listID"=>$this->list_id(),
				"action"=>"unsubscribe"
			);
			$x = SendPress_Data::encrypt( $link );
			$start_text = __("Not interested anymore?","sendpress");
			$unsubscribe = __("Unsubscribe","sendpress");
			$instantly = __("Instantly","sendpress");

			$remove_me = $start_text.' <a href="'.site_url().'?sendpress='.$x.'"  style="color: '.$body_link.';" >'.$unsubscribe.'</a> '.$instantly.'.';
			$subscriber = SendPress_Data::get_subscriber($this->subscriber_id());
			
			$body_html = str_replace("*|SP:UNSUBSCRIBE|*", $remove_me , $body_html );
			if (!is_null($subscriber)) {
				$body_html = str_replace("*|FNAME|*", $subscriber->firstname , $body_html );
				$body_html = str_replace("*|LNAME|*", $subscriber->lastname , $body_html );
				$body_html = str_replace("*|EMAIL|*", $subscriber->email , $body_html );
				$body_html = str_replace("*|ID|*", $subscriber->subscriberID , $body_html );
			}
				
			//echo  $body_html;

			//print_r($email);
			return $body_html;
	}

	function subject(){
			// Get any existing copy of our transient data
			if ( false === ( $email_subject = get_transient( 'sendpress_report_subject_'. $this->id() ) ) || ($this->purge() == true) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    if(!$this->post_info){
			    	$this->post_info = get_post( $this->id() );
				}
			    $email_subject =  $this->post_info->post_title;
				
			    $email_subject = SendPress_Template::tag_replace($email_subject);
				set_transient( 'sendpress_report_subject_'. $this->id(), $email_subject , 60*60*2);
			    // Get any existing copy of our transient data
			}
			$subscriber = SendPress_Data::get_subscriber($this->subscriber_id());
			if (!is_null($subscriber)) {
				$email_subject = str_replace("*|FNAME|*", $subscriber->firstname , $email_subject );
				$email_subject = str_replace("*|LNAME|*", $subscriber->lastname , $email_subject );
				$email_subject = str_replace("*|EMAIL|*", $subscriber->email , $email_subject );
				$email_subject = str_replace("*|ID|*", $subscriber->subscriberID , $email_subject );
  			}

			return $email_subject;
	}


}
}

