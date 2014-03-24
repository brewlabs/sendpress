<?php
/**
 * Unsubscribe Form Shortcode
 *
 * 
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Unsubscribe_Form {

	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		extract( shortcode_atts( array(
			'placeholder'		=> 'Email',
			'btntxt'      => __('Unsubscribe','senpress') // Possible values are 'IN', 'NOT IN', 'AND'.
			), $atts ) );
		?><form method="post" action="<?php echo home_url(); ?>">
			<input type="hidden" name="sendpress" value="post" />
			<input type="hidden" name="sp-shortcode" value="SC-Unsubscribe-Form" />
			<input type="text" class="sp-input" placeholder="<?php echo $placeholder; ?>"/>
			<input type="submit" value="<?php echo $btntxt; ?>" />
		</form><?php
	}

	public static function docs(){
		//might use this at a later date.
	}

	public static function form_post(){
		echo "made it here";
	}

}
