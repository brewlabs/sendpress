<?php

// SendPress Required Class: SendPress_Sender

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Tour')){  

class SendPress_Tour {


	/**
	 * Class constructor.
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue styles and scripts needed for the pointers.
	 */
	function enqueue() {
		if ( ! current_user_can('manage_options') )
			return;
			
		
		SendPress_Option::set( 'allow_tracking' , '' );
		$track = SendPress_Option::get( 'allow_tracking' );
		$tour = false ; //SendPress_Option::get( 'intro_tour' );

		if ( ($track == false || $track== '') && !isset( $_GET['allow_tracking'] ) ) {
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );

			add_action( 'admin_print_footer_scripts', array( $this, 'tracking_request' ) );
		} 
		else if ( $tour == 'false' || $tour == false) {
			/*
			add_action( 'admin_print_footer_scripts', array( $this, 'intro_tour' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			*/
		}
	}

	/**
	 * Load the introduction tour
	 */
	function intro_tour() {
		global $pagenow, $current_user;

		$page = '';
		if ( isset( $_GET['page'] ) )
			$page = $_GET['page'];

		$function = '';
		$button2  = '';
		$opt_arr  = array();
		$id       = '.nav-sp';
		//echo $page;
		if ( 'admin.php' != $pagenow || !array_key_exists( $page, $adminpages ) ) {
			$id      = '#toplevel_page_sp-overview';
			$content = '<h3>' . __( 'Welcome to SendPress', 'sendpress' ) . '</h3>';
			$content .= '<p>' . __( 'You\'ve just installed SendPress! Click &ldquo;Start Tour&rdquo; to view a quick introduction of this plugins core functionality.', 'sendpress' ) . '</p>';
			$opt_arr  = array(
				'content'  => $content,
				'position' => array( 'edge' => 'left', 'align' => 'left' )
			);
			$button2  = __( "Start Tour", 'sendpress' );
			$function = 'document.location="' . admin_url( 'admin.php?page=sp-overview' ) . '";';
		} else {
			if ( '' != $page && in_array( $page, array_keys( $adminpages ) ) ) {
				$opt_arr  = array(
					'content'      => $adminpages[$page]['content'],
					'position'     => array( 'edge' => 'top', 'align' => 'left' ),
					'pointerWidth' => 400
				);
				$button2  = $adminpages[$page]['button2'];
				$function = $adminpages[$page]['function'];
			}
		}

		$this->print_scripts( $id, $opt_arr, __( "Close", 'sendpress' ), $button2, $function );
	}



	/**
	 * Load a tiny bit of CSS in the head
	 */
	function admin_head() {
		?>
	<style type="text/css" media="screen">
		#pointer-primary {
			margin: 0 5px 0 0;
		}
	</style>
	<?php
	}
	/**
	 * Shows a popup that asks for permission to allow tracking.
	 */
	function tracking_request() {
		$id      = '#wpadminbar';
		$content = '<h3>' . __( 'Help Make SendPress Better', 'sendpress' ) . '</h3>';
		$content .= '<p>' . __( 'You\'ve just installed SendPres. Please helps us improve it by allowing us to gather anonymous usage stats so we know which configurations, plugins and themes to test with.', 'sendpress' ) . '</p>';
		$opt_arr   = array(
			'content'  => $content,
			'position' => array( 'edge' => 'top', 'align' => 'center' )
		);
		$button2   = __( 'Allow tracking', 'sendpress' );
		$nonce     = wp_create_nonce( 'sp_activate_tracking' );

		$function2 = 'document.location="' . admin_url( 'admin.php?page=sp-overview&action=tracking&allow_tracking=yes&nonce='.$nonce ) . '";';
		$function1 = 'document.location="' . admin_url( 'admin.php?page=sp-overview&action=tracking&allow_tracking=no&nonce='.$nonce ) . '";';

		$this->print_scripts( $id, $opt_arr, __( 'Do not allow tracking', 'sendpress' ), $button2, $function2, $function1 );
	}

	/**
	 * Prints the pointer script
	 *
	 * @param string      $selector         The CSS selector the pointer is attached to.
	 * @param array       $options          The options for the pointer.
	 * @param string      $button1          Text for button 1
	 * @param string|bool $button2          Text for button 2 (or false to not show it, defaults to false)
	 * @param string      $button2_function The JavaScript function to attach to button 2
	 * @param string      $button1_function The JavaScript function to attach to button 1
	 */
	function print_scripts( $selector, $options, $button1, $button2 = false, $button2_function = '', $button1_function = '' ) {
		?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			var sp_pointer_options = <?php echo json_encode( $options ); ?>, setup;

			sp_pointer_options = $.extend(sp_pointer_options, {
				buttons:function (event, t) {
					button = jQuery('<a id="pointer-close" style="margin-left:5px" class="button-secondary">' + '<?php echo $button1; ?>' + '</a>');
					button.bind('click.pointer', function () {
						t.element.pointer('close');
					});
					return button;
				},
				close:function () {
				}
			});

			setup = function () {
				$('<?php echo $selector; ?>').pointer(sp_pointer_options).pointer('open');
				<?php if ( $button2 ) { ?>
					jQuery('#pointer-close').after('<a id="pointer-primary" class="button-primary">' + '<?php echo $button2; ?>' + '</a>');
					jQuery('#pointer-primary').click(function () {
						<?php echo $button2_function; ?>
					});
					jQuery('#pointer-close').click(function () {
						<?php if ( $button1_function == '' ) { ?>
							//wpseo_setIgnore("tour", "wp-pointer-0", "<?php echo wp_create_nonce( 'wpseo-ignore' ); ?>");
							<?php } else { ?>
							<?php echo $button1_function; ?>
							<?php } ?>
					});
					<?php } ?>
			};

			if (sp_pointer_options.position && sp_pointer_options.position.defer_loading)
				$(window).bind('load.wp-pointers', setup);
			else
				$(document).ready(setup);
		})(jQuery);
		//]]>
	</script>
	<?php
	}

}



}

