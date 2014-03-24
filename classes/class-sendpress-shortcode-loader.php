<?php
/**
 * SendPress Shortcode Loader class.
 *
 * @class 		SendPress_Shortcode_Loader
 * @version		0.9.9.4
 * @package		SendPress/Classes
 * @category	Class
 * @author 		SendPRess
 */
class SendPress_Shortcode_Loader {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		// Define shortcodes
		$shortcodes = array(
			'unsubscribe-form'                    => __CLASS__ . '::unsubscribe_form',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "sp_{$shortcode}_shortcode_tag", 'sp-'. $shortcode ), $function );
		}

	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'sendpress',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

	/**
	 * Cart page shortcode.
	 *
	 * @access public
	 * @param mixed $atts
	 * @return string
	 */
	public static function unsubscribe_form( $atts ) {
		return self::shortcode_wrapper( array( 'SendPress_SC_Unsubscribe_Form', 'output' ), $atts );
	}



}
