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

	public static function shortcodes(){
		return array(
			'unsubscribe-form' => __CLASS__ . '::unsubscribe_form',
		);
	}

	/**
	 * Init shortcodes
	 */
	public static function init() {
		// Define shortcodes
		$shortcodes = self::shortcodes();

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "sp_{$shortcode}_shortcode_tag", 'sp-'. $shortcode ), $function );
		}

	}


	public static function docs(){
		$shortcodes = self::shortcodes();
		foreach ( $shortcodes as $shortcode => $function ) {
			$classname = ucwords(str_replace('-', ' ', strtolower($shortcode) ));
			$classname = str_replace(' ', '_', $classname );
			$classname = "SendPress_SC_" . $classname;
			?>
			<div class="panel panel-default">
			    <div class="panel-heading">
			      <h4 class="panel-title">
			        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
			        	<?php
			        		$title = $classname::title() !==false ? $classname::title() : "[sp-". $shortcode ."]";
			         	 echo $title;
			         	  ?>
			        </a>
			      </h4>
			    </div>
				<div id="collapseOne" class="panel-collapse collapse ">
					<div class="panel-body">
					<?php
						if($classname::docs() !== false){
							echo "<p>" . $classname::docs() . "</p>";
						}
						echo "<strong class='text-muted'>Basic:</strong><br>";
						echo "<pre>[sp-". $shortcode ."]</pre>";
						$options = $classname::options();

						if(!empty($options)){
						$txt = '';
						foreach ($options as $key => $value) {
							$txt .= $key . "='".$value."' ";
						}
						echo "<strong class='text-muted'>All Options with Defaults:</strong><br>";
						echo "<pre>[sp-". $shortcode ." ". $txt ."]</pre>";
						}
						if( $classname::html() !== false ){
							$message = __('Your Content Here.','sendpress');
							if(is_string( $classname::html() ) ){	
								$message = $classname::html();
							}
							echo "<strong class='text-muted'>Wrapping Content:</strong><br>";
							echo "<pre>[sp-". $shortcode ."]". $message ."[/sp-". $shortcode ."]</pre>";
						}

					?>
					</div>
				</div>
			</div>
			<?php
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
