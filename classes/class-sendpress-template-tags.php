<?php
//avoid direct calls to this file
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Template_Tags {

	private $email_tags;
	private $subscriber_tags;
	private $content_tags;
	private $subscriber_id;
	private $example;
	private $email_id;
	private $template_id;
	private $email_type;
	private $custom_content;
	/**
	 * Add an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string   $tag  Email tag to be replace in email
	 * @param callable $func Hook to run when email tag is found
	 */
	public function add_email_tag( $tag, $description, $func , $internal, $copy) {
			$this->email_tags[$tag] = array(
				'tag'         => $tag,
				'description' => $description,
				'func'        => $func,

			);
			if( $internal != false ){
				$this->email_tags[$tag]['internal'] = $internal;
			}

			if( $copy != false ){
				$this->email_tags[$tag]['copy'] = $copy;
			}
			
		
	}

	/**
	 * Add an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string   $tag  Email tag to be replace in email
	 * @param callable $func Hook to run when email tag is found
	 */
	public function add_subscriber_tag( $tag, $description, $func , $internal, $copy) {
			$this->subscriber_tags[$tag] = array(
				'tag'         => $tag,
				'description' => $description,
				'func'        => $func,

			);
			if( $internal != false ){
				$this->subscriber_tags[$tag]['internal'] = $internal;
			}

			if( $copy != false ){
				$this->subscriber_tags[$tag]['copy'] = $copy;
			}
			
		
	}

	/**
	 * Add an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string   $tag  Email tag to be replace in email
	 * @param callable $func Hook to run when email tag is found
	 */
	public function add_content_tag( $tag, $description, $func , $internal, $copy) {
			$this->content_tags[$tag] = array(
				'tag'         => $tag,
				'description' => $description,
				'func'        => $func,

			);
			if( $internal != false ){
				$this->content_tags[$tag]['internal'] = $internal;
			}

			if( $copy != false ){
				$this->content_tags[$tag]['copy'] = $copy;
			}
			
		
	}


	/**
	 * Remove an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag to remove hook from
	 */
	public function remove_email_tag( $tag ) {
		unset( $this->email_tags[$tag] );
	}

	/**
	 * Check if $tag is a registered email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag that will be searched
	 *
	 * @return bool
	 */
	public function email_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->email_tags );
	}

	/**
	 * Returns a list of all email tags
	 *
	 * @since 0.9.9.8
	 *
	 * @return array
	 */
	public function get_email_tags() {
		return $this->email_tags;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks
	 *
	 * @param string $content Content to search for email tags
	 * @param int $payment_id The payment id
	 *
	 * @since 0.9.9.8
	 *
	 * @return string Content with email tags filtered out.
	 */
	public function do_email_tags( $content, $t_id, $email_id , $subscriber_id , $example ) {
		
		// Check if there is atleast one tag added
		if ( empty( $this->email_tags ) || ! is_array( $this->email_tags ) ) {
			return $content;
		}
		$this->example = $example;
		$this->subscriber_id = $subscriber_id;
		$this->email_id = $email_id;
		$this->template_id = $t_id;

		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_email_tag' ), $content );

		
		return $new_content;
	}

	/**
	 * Do a specific tag, this function should not be used. Please use edd_do_email_tags instead.
	 *
	 * @since 0.9.9.8
	 *
	 * @param $m message
	 *
	 * @return mixed
	 */
	public function do_email_tag( $m ) {
		
		// Get tag
		$tag = $m[1];

		// Return tag if tag not set
		if ( ! $this->email_tag_exists( $tag ) ) {
			return $m[0];
		}
		/*
		if( $this->email_type == 'internal' && isset( $this->email_tags[$tag]['internal'] ) ) {
			return call_user_func( $this->email_tags[$tag]['internal'],$this->template_id, $this->email_id, $this->subscriber_id, $tag );
		}
		*/

		return call_user_func( $this->email_tags[$tag]['func'], $this->template_id,$this->email_id, $this->subscriber_id, $tag );
	}


	/**
	 * Remove an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag to remove hook from
	 */
	public function remove_content_tag( $tag ) {
		unset( $this->content_tags[$tag] );
	}

	/**
	 * Check if $tag is a registered email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag that will be searched
	 *
	 * @return bool
	 */
	public function content_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->content_tags );
	}

	/**
	 * Returns a list of all email tags
	 *
	 * @since 0.9.9.8
	 *
	 * @return array
	 */
	public function get_content_tags() {
		return $this->content_tags;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks
	 *
	 * @param string $content Content to search for email tags
	 * @param int $payment_id The payment id
	 *
	 * @since 0.9.9.8
	 *
	 * @return string Content with email tags filtered out.
	 */
	public function do_content_tags( $content, $t_id, $email_id , $subscriber_id , $example , $custom_content = false) {

		// Check if there is atleast one tag added
		if ( empty( $this->content_tags ) || ! is_array( $this->content_tags ) ) {
			return $content;
		}
		$this->example = $example;
		$this->subscriber_id = $subscriber_id;
		$this->email_id = $email_id;
		$this->template_id = $t_id;
		$this->custom_content = $custom_content;
		
		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_content_tag' ), $content );

		//$this->subscriber_id = null;
		//$this->email_id = null;

		return $new_content;
	}

	/**
	 * Do a specific tag, this function should not be used. Please use edd_do_content_tags instead.
	 *
	 * @since 0.9.9.8
	 *
	 * @param $m message
	 *
	 * @return mixed
	 */
	public function do_content_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if tag not set
		if ( ! $this->content_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->content_tags[$tag]['func'], $this->template_id,$this->email_id, $this->subscriber_id,  $this->example,$this->custom_content, $tag );
	}




	/**
	 * Remove an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag to remove hook from
	 */
	public function remove_subscriber_tag( $tag ) {
		unset( $this->subscriber_tags[$tag] );
	}

	/**
	 * Check if $tag is a registered email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag that will be searched
	 *
	 * @return bool
	 */
	public function subscriber_tag_exists( $tag ) {
		return array_key_exists( $tag, $this->subscriber_tags );
	}

	/**
	 * Returns a list of all email tags
	 *
	 * @since 0.9.9.8
	 *
	 * @return array
	 */
	public function get_subscriber_tags() {
		return $this->subscriber_tags;
	}

	/**
	 * Search content for email tags and filter email tags through their hooks
	 *
	 * @param string $content Content to search for email tags
	 * @param int $payment_id The payment id
	 *
	 * @since 0.9.9.8
	 *
	 * @return string Content with email tags filtered out.
	 */
	public function do_subscriber_tags( $content, $t_id, $email_id , $subscriber_id , $example ) {

		// Check if there is atleast one tag added
		if ( empty( $this->subscriber_tags ) || ! is_array( $this->subscriber_tags ) ) {
			return $content;
		}
		$this->example = $example;
		$this->subscriber_id = $subscriber_id;
		$this->email_id = $email_id;
		$this->template_id = $t_id;
		
		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_subscriber_tag' ), $content );

		
		return $new_content;
	}

	/**
	 * Do a specific tag, this function should not be used. Please use edd_do_subscriber_tags instead.
	 *
	 * @since 0.9.9.8
	 *
	 * @param $m message
	 *
	 * @return mixed
	 */
	public function do_subscriber_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if tag not set
		if ( ! $this->subscriber_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->subscriber_tags[$tag]['func'], $this->template_id,$this->email_id, $this->subscriber_id, $tag );
	}


}



/**
 * Add an email tag
 *
 * @since 0.9.9.8
 *
 * @param string   $tag  Email tag to be replace in email
 * @param callable $func Hook to run when email tag is found
 */
function spnl_add_email_tag( $tag, $description, $func , $int, $copy ) {
	SPNL()->template_tags->add_email_tag( $tag, $description, $func , $int, $copy );
}

/**
 * Add an email tag
 *
 * @since 0.9.9.8
 *
 * @param string   $tag  Email tag to be replace in email
 * @param callable $func Hook to run when email tag is found
 */
function spnl_add_subscriber_tag( $tag, $description, $func , $int, $copy ) {
	SPNL()->template_tags->add_subscriber_tag( $tag, $description, $func , $int, $copy );
}

/**
 * Add an email tag
 *
 * @since 0.9.9.8
 *
 * @param string   $tag  Email tag to be replace in email
 * @param callable $func Hook to run when email tag is found
 */
function spnl_add_content_tag( $tag, $description, $func , $int, $copy ) {
	SPNL()->template_tags->add_content_tag( $tag, $description, $func , $int, $copy );
}
/**
 * Remove an email tag
 *
 * @since 0.9.9.8
 *
 * @param string $tag Email tag to remove hook from
 */
function spnl_remove_email_tag( $tag ) {
	SPNL()->template_tags->remove_email_tag( $tag );
}

/**
 * Check if $tag is a registered email tag
 *
 * @since 0.9.9.8
 *
 * @param string $tag Email tag that will be searched
 *
 * @return bool
 */
function spnl_email_tag_exists( $tag ) {
	return SPNL()->template_tags->email_tag_exists( $tag );
}

/**
 * Get all email tags
 *
 * @since 0.9.9.8
 *
 * @return array
 */
function spnl_get_email_tags() {
	return SPNL()->template_tags->get_email_tags();
}

/**
 * Get a formatted HTML list of all available email tags
 *
 * @since 0.9.9.8
 *
 * @return string
 */
function spnl_get_emails_tags_list() {
	// The list
	$list = '';

	// Get all tags
	$email_tags = spnl_get_email_tags();

	// Check
	if ( count( $email_tags ) > 0 ) {

		// Loop
		foreach ( $email_tags as $email_tag ) {

			// Add email tag to list
			$list .= '{' . $email_tag['tag'] . '} - ' . $email_tag['description'] . '<br/>';

		}

	}

	// Return the list
	return $list;
}

/**
 * Search content for email tags and filter email tags through their hooks
 *
 * @param string $content Content to search for email tags
 * @param int $email_id The email id
 * @param int $subscriber_id The subscriber id
 *
 * @since 0.9.9.8
 *
 * @return string Content with email tags filtered out.
 */
function spnl_do_content_tags( $content, $t_id , $email_id, $subscriber_id = 0 , $example = false, $raw = false ) {
	// Replace Content Tags
	remove_filter( 'the_content', 'pdfprnt_content' );
	$content = SPNL()->template_tags->do_content_tags( $content,$t_id ,  $email_id, $subscriber_id , $example , $raw);
	//RE ADD THE FILTER..
	if( function_exists('pdfprnt_content') ){
		add_filter( 'the_content', 'pdfprnt_content' );
	}
	// Replace Email tags
	//$content = SPNL()->template_tags->do_email_tags( $content, $t_id , $email_id, $subscriber_id , $example );
	
	// Return content
	return $content;
}

/**
 * Search content for email tags and filter email tags through their hooks
 *
 * @param string $content Content to search for email tags
 * @param int $email_id The email id
 * @param int $subscriber_id The subscriber id
 *
 * @since 0.9.9.8
 *
 * @return string Content with email tags filtered out.
 */
function spnl_do_email_tags( $content, $t_id , $email_id, $subscriber_id = 0 , $example = false ) {
	// Replace Content Tags
	//$content = SPNL()->template_tags->do_content_tags( $content,$t_id ,  $email_id, $subscriber_id , $example );

	// Replace Email tags
	$content = SPNL()->template_tags->do_email_tags( $content, $t_id , $email_id, $subscriber_id , $example );
	
	// Return content
	return $content;
}



/**
 * Search content for email tags and filter email tags through their hooks
 *
 * @param string $content Content to search for email tags
 * @param int $email_id The email id
 * @param int $subscriber_id The subscriber id
 *
 * @since 0.9.9.8
 *
 * @return string Content with email tags filtered out.
 */
function spnl_do_subscriber_tags( $content, $t_id , $email_id, $subscriber_id = 0 , $example = false ) {
	// Replace Content Tags
	//$content = SPNL()->template_tags->do_content_tags( $content,$t_id ,  $email_id, $subscriber_id , $example );

	// Replace Email tags
	$content = SPNL()->template_tags->do_subscriber_tags( $content, $t_id , $email_id, $subscriber_id , $example );
	
	// Return content
	return $content;
}

/**
 * Load email tags
 *
 * @since 0.9.9.8
 */
function spnl_load_template_tags() {
	do_action( 'spnl_add_template_tags' );
}
add_action( 'init', 'spnl_load_template_tags', -999 );




/**
 * Add default SendPress email template tags
 *
 * @since 0.9.9.8
 */
function spnl_setup_template_tags() {

	// Setup default tags array
	$email_tags = array(
		/*
		array(
			'tag'         => 'subscriber_list',
			'description' => __( 'A list of download links for each download purchased', 'sendpress' ),
			'function'    => 'spnl_email_test_tag',
			'internal'    => false,
			'copy'    => false,
		),
		*/
		array(
			'tag'         => 'sp-social-links',
			'description' => __( 'Inserts Social Links.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Social_Links','external'),
			'internal'    => array('SendPress_Tag_Social_Links','internal'),
			'copy'    => array('SendPress_Tag_Social_Links','copy'),
		),
		
		array(
			'tag'         => 'sp-browser-link-html',
			'description' => __( 'Inserts view in browser html link.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Browser_Link_Html','external'),
			'internal'    => array('SendPress_Tag_Browser_Link_Html','internal'),
			'copy'    => array('SendPress_Tag_Browser_Link_Html','copy'),
		),
		
		array(
			'tag'         => 'sp-site-name',
			'description' => __( 'Inserts Site Name.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Site_Name','external'),
			'internal'    => array('SendPress_Tag_Site_Name','internal'),
			'copy'    => array('SendPress_Tag_Site_Name','copy'),
		),
		array(
			'tag'         => 'canspam',
			'description' => __( 'Inserts the CANSPAM text.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Canspam','external'),
			'internal'    => array('SendPress_Tag_Canspam','internal'),
			'copy'    => array('SendPress_Tag_Canspam','copy'),
		),
		
		array(
			'tag'         => 'sp-unsubscribe-link-html',
			'description' => __( 'Inserts an unsubscribe html link.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Unsubscribe_Link_Html','external'),
			'internal'    => array('SendPress_Tag_Unsubscribe_Link_Html','internal'),
			'copy'    => array('SendPress_Tag_Unsubscribe_Link_Html','copy'),
		)
		

	);

	$subscriber_tags = array(
	array(
			'tag'         => 'sp-subscriber-id',
			'description' => __( 'Inserts the subscriber id.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Subscriber_Id','external'),
			'internal'    => array('SendPress_Tag_Subscriber_Id','internal'),
			'copy'    => array('SendPress_Tag_Subscriber_Id','copy'),
		),array(
			'tag'         => 'sp-subscriber-id-encoded',
			'description' => __( 'Inserts the subscriber id.', 'sendpress' ),
			'function'    => array('SendPress_Tag_Subscriber_Id_Encoded','external'),
			'internal'    => array('SendPress_Tag_Subscriber_Id_Encoded','internal'),
			'copy'    => array('SendPress_Tag_Subscriber_Id_Encoded','copy'),
		)
		);

	$content_tags = array(
		array(
			'tag'         => 'sp-body-color',
			'description' => __( 'Body Color', 'sendpress' ),
			'function'    => array('SendPress_Tag_Body_Color','external'),
			'internal'    => array('SendPress_Tag_Body_Color','internal'),
			'copy'    => array('SendPress_Tag_Body_Color','copy'),
		),
		array(
			'tag'         => 'sp-content-bg-color',
			'description' => __( 'Content Color', 'sendpress' ),
			'function'    => array('SendPress_Tag_Content_Color','external'),
			'internal'    => array('SendPress_Tag_Content_Color','internal'),
			'copy'    => array('SendPress_Tag_Content_Color','copy'),
		),
		array(
			'tag'         => 'sp-content-text-color',
			'description' => __( 'Content Color', 'sendpress' ),
			'function'    => array('SendPress_Tag_Content_Text_Color','external'),
			'internal'    => array('SendPress_Tag_Content_Text_Color','internal'),
			'copy'    => array('SendPress_Tag_Content_Text_Color','copy'),
		),
		array(
			'tag'         => 'sp-content-area-one',
			'description' => __( 'Content Area', 'sendpress' ),
			'function'    => array('SendPress_Tag_Content_Area_One','external'),
			'internal'    => array('SendPress_Tag_Content_Area_One','internal'),
			'copy'    => array('SendPress_Tag_Content_Area_One','copy'),
		),
		array(
			'tag'         => 'sp-header-content',
			'description' => __( 'Header Content', 'sendpress' ),
			'function'    => array('SendPress_Tag_Header_Content','external'),
			'internal'    => false,//array('SendPress_Tag_Header_Content','internal'),
			'copy'    => array('SendPress_Tag_Header_Content','copy'),
		),
		array(
			'tag'         => 'sp-header-page',
			'description' => __( 'Header Page', 'sendpress' ),
			'function'    => array('SendPress_Tag_Header_Page','external'),
			'internal'    => false,//array('SendPress_Tag_Header_Content','internal'),
			'copy'    => array('SendPress_Tag_Header_Page','copy'),
		),
		array(
			'tag'         => 'sp-footer-page',
			'description' => __( 'Footer Page', 'sendpress' ),
			'function'    => array('SendPress_Tag_Footer_Page','external'),
			'internal'    => false,//array('SendPress_Tag_Header_Content','internal'),
			'copy'    => array('SendPress_Tag_Footer_Page','copy'),
		),
		array(
			'tag'         => 'sp-footer-content',
			'description' => __( 'Footer Content', 'sendpress' ),
			'function'    => array('SendPress_Tag_Footer_Content','external'),
			'internal'    => false,//array('SendPress_Tag_Footer_Content','internal'),
			'copy'    => array('SendPress_Tag_Footer_Content','copy'),
		),
		array(
			'tag'         => 'sp-email-title',
			'description' => __( 'Email Title', 'sendpress' ),
			'function'    => array('SendPress_Tag_Email_Title','external'),
			'internal'    => false,//array('SendPress_Tag_Footer_Content','internal'),
			'copy'    => array('SendPress_Tag_Email_Title','copy'),
		)
	);


	
	// Apply edd_email_tags filter
	$subscriber_tags = apply_filters( 'spnl_subscriber_tags', $subscriber_tags );

	// Add email tags
	foreach ( $subscriber_tags as $subscriber_tag ) {
		spnl_add_subscriber_tag( $subscriber_tag['tag'], $subscriber_tag['description'], $subscriber_tag['function'], $subscriber_tag['internal'] , $subscriber_tag['copy']);
	}

	// Apply edd_email_tags filter
	$email_tags = apply_filters( 'spnl_email_tags', $email_tags );

	// Add email tags
	foreach ( $email_tags as $email_tag ) {
		spnl_add_email_tag( $email_tag['tag'], $email_tag['description'], $email_tag['function'], $email_tag['internal'] , $email_tag['copy']);
	}

	// Add email tags
	foreach ( $content_tags as $content_tag ) {
		spnl_add_content_tag( $content_tag['tag'], $content_tag['description'], $content_tag['function'], $content_tag['internal'] , $content_tag['copy']);
	}

}
add_action( 'spnl_add_template_tags', 'spnl_setup_template_tags' );



