<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
/**
 * Unsubscribe Form Shortcode
 *
 *
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Unsubscribe_Form extends SendPress_SC_Base {

	public static function title(){
		return __('Unsubscribe Form', 'sendpress');
	}

	public static function options(){
		return 	array(
			'placeholder'	=> __('Email','sendpress'),
			'btntxt'		=> __('Unsubscribe','sendpress') // Possible values are 'IN', 'NOT IN', 'AND'.
			);
	}

	public static function html(){
		return __('You can provide an alternate message to your users after they unsubscribe.','sendpress');
	}

	public static function content(){
		return __('You have been unsubscribed from all lists.','sendpress');
	}
	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts , $content = null ) {
		global $post , $wp;

		extract( shortcode_atts( self::options() , $atts ) );

		if($content === null){
			$content = self::content();
		}

		$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
		$data = SPNL()->validate->_string('sp-unsubscribe');
		if(empty($data ) ){
			?><form method="post" action="<?php echo home_url(); ?>">
			<input type="hidden" name="sendpress" value="post" />
			<input type="hidden" name="sp-shortcode" value="SC-Unsubscribe-Form" />
			<input type="hidden" name="sp-current-page" value="<?php echo esc_url( $current_url ); ?>" />
			<input type="text" name="sp-email" class="sp-input" placeholder="<?php echo esc_attr($placeholder); ?>"/>
			<input type="submit" value="<?php echo esc_attr($btntxt); ?>" />
			</form><?php
		} else {
			echo do_shortcode($content);
		}

	}

	public static function docs(){
		return __('This shortcode creates a form that allows a user to enter an email address and unsubscribe. The default message after unsubscribe is: ', 'sendpress') ."<br><br><code>". self::content()."</code>";
	}

	public static function form_post(){
		global $wp;
		$email = SPNL()->validate->_email('sp-email');
		$message = 'true';
		if ( $email !== false ) {
			if(is_email( $email ) ){
				$id = SendPress_Data::get_subscriber_by_email( $email );
				if($id != false){
					SendPress_Data::unsubscribe_from_all_lists( $id );
				}
			}
		}
		if( SPNL()->validate->_isset('sp-current-page')  ){
			$permalink = SPNL()->validate->_string('sp-current-page');
			$permalink = add_query_arg(array('sp-unsubscribe'=> $message), $permalink );
			wp_safe_redirect( $permalink );
		}

	}

}
