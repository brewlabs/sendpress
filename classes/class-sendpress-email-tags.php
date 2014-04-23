<?php
//avoid direct calls to this file
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Email_Tags {

	private $tags;

	private $subscriber_id;

	private $email_id;

	/**
	 * Add an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string   $tag  Email tag to be replace in email
	 * @param callable $func Hook to run when email tag is found
	 */
	public function add( $tag, $description, $func ) {
		if ( is_callable( $func ) ) {
			$this->tags[$tag] = array(
				'tag'         => $tag,
				'description' => $description,
				'func'        => $func
			);
		}
	}

	/**
	 * Remove an email tag
	 *
	 * @since 0.9.9.8
	 *
	 * @param string $tag Email tag to remove hook from
	 */
	public function remove( $tag ) {
		unset( $this->tags[$tag] );
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
		return array_key_exists( $tag, $this->tags );
	}

	/**
	 * Returns a list of all email tags
	 *
	 * @since 0.9.9.8
	 *
	 * @return array
	 */
	public function get_tags() {
		return $this->tags;
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
	public function do_tags( $content, $email_id , $subscriber_id ) {

		// Check if there is atleast one tag added
		if ( empty( $this->tags ) || ! is_array( $this->tags ) ) {
			return $content;
		}

		$this->subscriber_id = $subscriber_id;
		$this->email_id = $email_id;

		$new_content = preg_replace_callback( "/{([A-z0-9\-\_]+)}/s", array( $this, 'do_tag' ), $content );

		$this->subscriber_id = null;
		$this->email_id = null;

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
	public function do_tag( $m ) {

		// Get tag
		$tag = $m[1];

		// Return tag if tag not set
		if ( ! $this->email_tag_exists( $tag ) ) {
			return $m[0];
		}

		return call_user_func( $this->tags[$tag]['func'], $this->email_id, $this->subscriber_id, $tag );
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
function spnl_add_email_tag( $tag, $description, $func ) {
	SPNL()->email_tags->add( $tag, $description, $func );
}

/**
 * Remove an email tag
 *
 * @since 0.9.9.8
 *
 * @param string $tag Email tag to remove hook from
 */
function spnl_remove_email_tag( $tag ) {
	SPNL()->email_tags->remove( $tag );
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
	return SPNL()->email_tags->email_tag_exists( $tag );
}

/**
 * Get all email tags
 *
 * @since 0.9.9.8
 *
 * @return array
 */
function spnl_get_email_tags() {
	return SPNL()->email_tags->get_tags();
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
function spnl_do_email_tags( $content, $email_id, $subscriber_id = 0 ) {

	// Replace all tags
	$content = SPNL()->email_tags->do_tags( $content, $email_id, $subscriber_id );
	
	// Return content
	return $content;
}

/**
 * Load email tags
 *
 * @since 0.9.9.8
 */
function spnl_load_email_tags() {
	do_action( 'spnl_add_email_tags' );
}
add_action( 'init', 'spnl_load_email_tags', -999 );




/**
 * Add default SendPress email template tags
 *
 * @since 0.9.9.8
 */
function spnl_setup_email_tags() {

	// Setup default tags array
	$email_tags = array(
		array(
			'tag'         => 'subscriber_list',
			'description' => __( 'A list of download links for each download purchased', 'sendpress' ),
			'function'    => 'spnl_email_test_tag'
		),
	);

	// Apply edd_email_tags filter
	$email_tags = apply_filters( 'spnl_email_tags', $email_tags );

	// Add email tags
	foreach ( $email_tags as $email_tag ) {
		spnl_add_email_tag( $email_tag['tag'], $email_tag['description'], $email_tag['function'] );
	}

}
add_action( 'spnl_add_email_tags', 'spnl_setup_email_tags' );


function spnl_email_test_tag( $email_id, $subscriber_id ) {
	return ' hihihi ';
}


