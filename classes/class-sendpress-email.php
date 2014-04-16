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
		if ( ! isset( $subscriber_id ) ){
			return $this->_subscriber_id;
		}
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

	function text_convert($html,$fullConvert = true){

		if($fullConvert){
			$html = preg_replace('# +#',' ',$html);
			$html = str_replace(array("\n","\r","\t"),'',$html);
		}
		$removepictureslinks = "#< *a[^>]*> *< *img[^>]*> *< *\/ *a *>#isU";
		$removeScript = "#< *script(?:(?!< */ *script *>).)*< */ *script *>#isU";
		$removeStyle = "#< *style(?:(?!< */ *style *>).)*< */ *style *>#isU";
		$removeStrikeTags =  '#< *strike(?:(?!< */ *strike *>).)*< */ *strike *>#iU';
		$replaceByTwoReturnChar = '#< *(h1|h2)[^>]*>#Ui';
		$replaceByStars = '#< *li[^>]*>#Ui';
		$replaceByReturnChar1 = '#< */ *(li|td|tr|div|p)[^>]*> *< *(li|td|tr|div|p)[^>]*>#Ui';
		$replaceByReturnChar = '#< */? *(br|p|h1|h2|legend|h3|li|ul|h4|h5|h6|tr|td|div)[^>]*>#Ui';
		$replaceLinks = '/< *a[^>]*href *= *"([^#][^"]*)"[^>]*>(.*)< *\/ *a *>/Uis';
		$text = preg_replace(array($removepictureslinks,$removeScript,$removeStyle,$removeStrikeTags,$replaceByTwoReturnChar,$replaceByStars,$replaceByReturnChar1,$replaceByReturnChar,$replaceLinks),array('','','','',"\n\n","\n* ","\n","\n",'${2} ( ${1} )'),$html);
		$text = str_replace(array(" ","&nbsp;"),' ',strip_tags($text));
		$text = trim(@html_entity_decode($text,ENT_QUOTES,'UTF-8'));
		if($fullConvert){
			$text = preg_replace('# +#',' ',$text);
			$text = preg_replace('#\n *\n\s+#',"\n\n",$text);
		}
		return $text;
	}

	function text(){
		return $this->text_convert( $this->html() , true);
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
			$subscriber = SendPress_Data::get_subscriber($this->subscriber_id());
			if (!is_null($subscriber)) {
				$body_html = str_replace("*|FNAME|*", $subscriber->firstname , $body_html );
				$body_html = str_replace("*|LNAME|*", $subscriber->lastname , $body_html );
				$body_html = str_replace("*|EMAIL|*", $subscriber->email , $body_html );
				$body_html = str_replace("*|ID|*", $subscriber->subscriberID , $body_html );
			}

			$open_info = array(
				"id"=>$this->subscriber_id(),
				"report"=> $this->id(),
				"view"=>"open"
			);
			$code = SendPress_Data::encrypt( $open_info );

			$link = SendPress_Manager::public_url($code);


			$tracker = "<img src='". $link ."' width='1' height='1'/></body>";
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
				/*
				$style = $aElement->getAttribute('style');

				if($style == ""){
					$aElement->setAttribute('style');
				}
				*/

				//ADD TO DB?
				
				if(strrpos( $href, "*|" ) === false ) {

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
					$code = SendPress_Data::encrypt( $link );
					$link = SendPress_Manager::public_url($code);

					$href = $link;
					$aElement->setAttribute('href', $href);
				}
			}
			$body_html = $dom->saveHtml();

			$link_data = array(
				"id"=>$this->subscriber_id(),
				"report"=> $this->id(),
				"urlID"=> '0',
				"view"=>"manage",
				"listID"=>$this->list_id(),
				"action"=>"unsubscribe"
			);
			$code = SendPress_Data::encrypt( $link_data );
			$link =  SendPress_Manager::public_url($code);






			
			
			
			if( SendPress_Option::get('old_unsubscribe_link', false) === true ){
				$start_text = __("Not interested anymore?","sendpress");
				$unsubscribe = __("Unsubscribe","sendpress");
				$instantly = __("Instantly","sendpress");

				$remove_me_old = $start_text.' <a href="'.$link.'"  style="color: '.$body_link.';" >'.$unsubscribe.'</a> '.$instantly.'.';


				$body_html = str_replace("*|SP:UNSUBSCRIBE|*", $remove_me_old , $body_html );
				$body_html = str_replace("*|SP:MANAGE|*", '' , $body_html );
			} else {

				$link_data = array(
					"id"=>$this->subscriber_id(),
					"report"=> $this->id(),
					"urlID"=> '0',
					"view"=>"manage",
					"listID"=>$this->list_id(),
					"action"=>""
				);
				$code = SendPress_Data::encrypt( $link_data );
				$manage_link = SendPress_Manager::public_url($code);


				$unsubscribe = __("Unsubscribe","sendpress");
				$manage = __("Manage Subscription","sendpress");
				
				$remove_me = ' <a href="'.$link.'"  style="color: '.$body_link.';" >'.$unsubscribe.'</a> | ';
				$manage = ' <a href="'.$manage_link.'"  style="color: '.$body_link.';" >'.$manage.'</a> ';

				$body_html = str_replace("*|SP:UNSUBSCRIBE|*", $remove_me , $body_html );
				$body_html = str_replace("*|SP:MANAGE|*", $manage , $body_html );

			}
			if (!is_null($subscriber)) {
				$body_html = str_replace("*|FNAME|*", $subscriber->firstname , $body_html );
				$body_html = str_replace("*|LNAME|*", $subscriber->lastname , $body_html );
				$body_html = str_replace("*|EMAIL|*", $subscriber->email , $body_html );
				$body_html = str_replace("*|ID|*", $subscriber->subscriberID , $body_html );
			}
			
			
            $body_html = apply_filters('sendpress_post_render_email', $body_html);
			//echo  $body_html;

			//print_r($email);
			return $body_html;
	}

	function subject(){
			// Get any existing copy of our transient data
			//if ( false === ( $email_subject = get_transient( 'sendpress_report_subject_'. $this->id() ) ) || ($this->purge() == true) ) {
			    // It wasn't there, so regenerate the data and save the transient
			    if(!$this->post_info){
			    	$this->post_info = get_post( $this->id() );
				}

				if($this->post_info->post_type == 'sp_newsletters' || $this->post_info->post_type == 'sp_report'){
					$email_subject =  get_post_meta($this->id(),'_sendpress_subject',true );
				} else {
			    	$email_subject =  $this->post_info->post_title;
				}
				
			    $email_subject = SendPress_Template::tag_replace($email_subject);
			//	set_transient( 'sendpress_report_subject_'. $this->id(), $email_subject , 60*60*2);
			    // Get any existing copy of our transient data
			//}
			$subscriber = SendPress_Data::get_subscriber($this->subscriber_id());
			if (!is_null($subscriber)) {
				$email_subject = str_replace("*|FNAME|*", $subscriber->firstname , $email_subject );
				$email_subject = str_replace("*|LNAME|*", $subscriber->lastname , $email_subject );
				$email_subject = str_replace("*|EMAIL|*", $subscriber->email , $email_subject );
				$email_subject = str_replace("*|ID|*", $subscriber->subscriberID , $email_subject );
  			}

			return $email_subject;
	}


	static function set_default_style( $id ){
		if( false == get_post_meta( $id , 'body_bg', true) ) {

			$default_styles_id = SendPress_Data::get_template_id_by_slug( 'user-style' );

			if(false == get_post_meta( $default_styles_id , 'body_bg', true) ){
				$default_styles_id = SendPress_Data::get_template_id_by_slug('default-style');
			}

			$default_post = get_post( $default_styles_id );

			update_post_meta( $id , 'body_bg',  get_post_meta( $default_post->ID , 'body_bg', true) );
			update_post_meta( $id , 'body_text',  get_post_meta( $default_post->ID , 'body_text', true) );
			update_post_meta( $id , 'body_link',  get_post_meta( $default_post->ID , 'body_link', true) );
			
			update_post_meta( $id , 'header_bg',  get_post_meta( $default_post->ID , 'header_bg', true) );
			update_post_meta( $id , 'header_text_color',  get_post_meta( $default_post->ID , 'header_text_color', true) );
			//update_post_meta( $id , 'header_text',  get_post_meta( $default_post->ID , 'header_text', true) );

			update_post_meta( $id, 'content_bg',  get_post_meta( $default_post->ID , 'content_bg', true) );
			update_post_meta( $id , 'content_text',  get_post_meta( $default_post->ID , 'content_text', true) );
			update_post_meta( $id , 'sp_content_link_color',  get_post_meta( $default_post->ID , 'sp_content_link_color', true) );
			update_post_meta( $id , 'content_border',  get_post_meta( $default_post->ID , 'content_border', true) );
			update_post_meta( $id , 'upload_image',  get_post_meta( $default_post->ID , 'upload_image', true) );

		} 
	}


}
}

