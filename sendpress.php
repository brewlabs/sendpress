<?php
/*
Plugin Name: SendPress Newsletters
Version: 1.9.3.29.1
Plugin URI: https://sendpress.com
Description: Easy to manage Newsletters for WordPress.
Author: SendPress
Author URI: https://sendpress.com/

Text Domain: sendpress
Domain Path: /languages/

*/
if ( ! defined( 'DB_NAME' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}
global $blog_id;
defined( 'SENDPRESS_API_BASE' ) or define( 'SENDPRESS_API_BASE', 'http://api.sendpress.com' );
define( 'SENDPRESS_API_VERSION', 1 );
define( 'SENDPRESS_MINIMUM_WP_VERSION', '3.6' );
define( 'SENDPRESS_VERSION', '1.9.3.29.1' );
define( 'SENDPRESS_URL', plugin_dir_url( __FILE__ ) );
define( 'SENDPRESS_PATH', plugin_dir_path( __FILE__ ) );
define( 'SENDPRESS_BASENAME', plugin_basename( __FILE__ ) );
define( 'SENDPRESS_IRON', 'http://sendpress.com/iron' );
define( 'SENDPRESS_SENDER_KEY', md5( __FILE__ . $blog_id ) );
define( 'SENDPRESS_CRON', md5( __FILE__ . $blog_id ) );

if ( ! defined( 'SENDPRESS_FILE' ) ) {
	define( 'SENDPRESS_FILE', __FILE__ );
}

define( 'SENDPRESS_LOG_ERROR', true );

if ( ! defined( 'SENDPRESS_STORE_URL' ) ) {
	define( 'SENDPRESS_STORE_URL', 'https://store.sendpress.com' );
}
if ( ! defined( 'SENDPRESS_PRO_NAME' ) ) {
	define( 'SENDPRESS_PRO_NAME', 'SendPress Pro' );
}

global $pro_names;
$pro_names = array( 256, 806, 807,30800 ); //pro1, pro3, pro20, Sp Pro

/*
*
*	Supporting Classes they build out the WordPress table views.
*
*/
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/*
*
*	Supporting Classes they build out the WordPress table views.
*
*/
if ( ! class_exists( 'SendPress_Pro_Updater' ) ) {
	require_once( SENDPRESS_PATH . 'classes/class-sendpress-pro-updater.php' );
}

// AutoLoad Classes
spl_autoload_register( array( 'SendPress', 'autoload' ) );

require_once( SENDPRESS_PATH . 'inc/functions.php' );
/*
require_once( SENDPRESS_PATH . 'classes/class-file-loader.php' );
$sp_loader = new File_Loader('SendPress Required Class');
*/
//require_once( SENDPRESS_PATH . 'classes/selective-loader.php' );
if ( ! defined( 'SENDPRESS_TRANSIENT_LENGTH' ) ) {
	define( 'SENDPRESS_TRANSIENT_LENGTH', 7 * 86400 );
}

/**
 * The Main Brain.
 *
 * @package SendPress
 * @subpackage
 * @since thedawnoftime
 */
class SendPress {

	
	var $prefix = 'sendpress_';
	var $ready = false;
	var $_nonce_value = 'sendpress-is-awesome';

	var $_current_action = '';
	var $_current_view = '';

	var $_email_post_type = 'sp_newsletters';

	var $_report_post_type = 'sp_report';

	var $adminpages = array(
		'sp',
		'sp-overview',
		'sp-reports',
		'sp-emails',
		'sp-templates',
		'sp-subscribers',
		'sp-settings',
		'sp-queue',
		'sp-pro',
		'sp-help'
	);

	var $_templates = array();
	var $_messages = array();

	var $_page = '';

	var $testmode = false;

	var $_posthelper = '';

	var $_debugAddress = 'josh@sendpress.com';

	var $_debugMode = false;


	public $email_tags;
	public $log;
	public $db;
	public $api;
	public $validate;
	public $customizer;
	public $loader;
	private static $instance;


	function nonce_value() {
		return 'sendpress-is-awesome';
	}


	function __construct() {
		//add_action( 'admin_init' , array( 'SendPress' , 'wp' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'widgets_init', array( $this , 'load_widgets' ) );

		/*
		add_action( 'widgets_init',
			create_function( '', 'return register_widget("SendPress_Widget_Forms");' )
		);
		add_action( 'widgets_init',
			create_function( '', 'return register_widget("SendPress_Widget_Signup");' )
		);
		*/

		add_action( 'plugins_loaded', array( $this, 'load_plugin_language' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );
		add_action( 'init', array( 'SendPress_Shortcode_Loader', 'init' ) );

		do_action( 'sendpress_loaded' );
	}

	public static function autoload( $className ) {
		if ( strpos( $className, 'SendPress' ) !== 0 ) {
			return;
		}
		// Convert Classname to filename
		$cls = str_replace( '_', '-', strtolower( $className ) );
		if ( substr( $cls, - 1 ) == '-' ) {
			//AutoLoad seems to get odd clasname sometimes that ends with _
			return;
		}
		if ( class_exists( $className ) ) {
			return;
		}

		if ( strpos( $className, '_SC_' ) != false ) {
			if ( defined( 'SENDPRESS_PRO_PATH' ) ) {
				$pro_file = SENDPRESS_PRO_PATH . "classes/sc/class-" . $cls . ".php";
				if ( file_exists( $pro_file ) ) {
					include SENDPRESS_PRO_PATH . "classes/sc/class-" . $cls . ".php";

					return;
				}
			}
			include SENDPRESS_PATH . "classes/sc/class-" . $cls . ".php";

			return;
		}

		if ( strpos( $className, '_Tag_' ) != false ) {

			include SENDPRESS_PATH . "classes/tag/class-" . $cls . ".php";

			return;
		}

		if ( strpos( $className, '_DB' ) != false ) {

			include SENDPRESS_PATH . "classes/db/class-" . $cls . ".php";

			return;
		}

		if ( strpos( $className, '_REST' ) != false ) {

			include SENDPRESS_PATH . "classes/api/v1/class-" . $cls . ".php";

			return;
		}

		if ( strpos( $className, 'Public_View' ) != false ) {
			if ( defined( 'SENDPRESS_PRO_PATH' ) ) {
				$pro_file = SENDPRESS_PRO_PATH . "classes/public-views/class-" . $cls . ".php";
				if ( file_exists( $pro_file ) ) {
					include SENDPRESS_PRO_PATH . "classes/public-views/class-" . $cls . ".php";

					return;
				}
			}
			if ( file_exists( SENDPRESS_PATH . "classes/public-views/class-" . $cls . ".php" ) ) {
				include SENDPRESS_PATH . "classes/public-views/class-" . $cls . ".php";
			}

			return;
		}

		if ( strpos( $className, 'View' ) != false ) {
			if ( defined( 'SENDPRESS_PRO_PATH' ) ) {
				$pro_file = SENDPRESS_PRO_PATH . "classes/views/class-" . $cls . ".php";
				if ( file_exists( $pro_file ) ) {
					include SENDPRESS_PRO_PATH . "classes/views/class-" . $cls . ".php";

					return;
				}
			}
			include SENDPRESS_PATH . "classes/views/class-" . $cls . ".php";

			return;
		}

		if ( strpos( $className, 'Module' ) != false ) {
			if ( defined( 'SENDPRESS_PRO_PATH' ) ) {
				$pro_file = SENDPRESS_PRO_PATH . "classes/modules/class-" . $cls . ".php";
				if ( file_exists( $pro_file ) ) {
					include SENDPRESS_PRO_PATH . "classes/modules/class-" . $cls . ".php";

					return;
				}
			}

			include SENDPRESS_PATH . "classes/modules/class-" . $cls . ".php";

			return;
		}

		if ( defined( 'SENDPRESS_PRO_PATH' ) ) {
			$pro_file = SENDPRESS_PRO_PATH . "classes/class-" . $cls . ".php";
			if ( file_exists( $pro_file ) ) {
				include SENDPRESS_PRO_PATH . "classes/class-" . $cls . ".php";

				return;
			}
		}

		if ( file_exists( SENDPRESS_PATH . "classes/class-" . $cls . ".php" ) ) {
			include SENDPRESS_PATH . "classes/class-" . $cls . ".php";
		}

		return;

	}

	static $array_of_db_objects;
	public function load( $object ){
		$class_name = "SendPress_DB_" . $object;
		if(isset(self::$array_of_db_objects[$object])) {
			return self::$array_of_db_objects[$object];
		}
		if(class_exists($class_name)){
			$class =  new $class_name();
			self::$array_of_db_objects[$object] = $class;
		} else {
			$class =  new WP_Error();
		}
		return $class;
	}
	


	public static function get_instance() {
		
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SendPress ) ) {
			self::$instance                          = new SendPress;
			self::$instance->template_tags           = new SendPress_Template_Tags();
			self::$instance->api                     = new SendPress_API();
			self::$instance->rest_api                = new SendPress_Api_Loader();
			self::$instance->validate                = new SendPress_Security();
			self::$instance->log                     = new SendPress_Logging();
			self::$instance->db                      = new stdClass();
			self::$instance->db->subscribers_tracker = new SendPress_DB_Subscribers_Tracker();
			self::$instance->db->url                 = new SendPress_DB_Url();
			self::$instance->db->subscribers_url     = new SendPress_DB_Subscribers_Url();
			self::$instance->db->suppression     	 = new SendPress_DB_Suppression();
			self::$instance->loader  = new SendPress_Loader();

		}

		return self::$instance;
	}

		/**
	 * Create the wp-admin menu link
	 */
	public function add_menu_link() {
		$link = $this->get_customizer_link();
		add_submenu_page( 'themes.php', 'Email Templates', 'Email Templates', apply_filters( 'mailtpl/roles', 'edit_theme_options'), $link , null );
	}

	/**
	 * Simple function to generate link for customizer
	 * @return string
	 */
	public function get_customizer_link( $email  = 0 , $return = false) {
		if($return == false){
			$return = admin_url();
		}

		$link = add_query_arg(
			array(
				'url'             => urlencode( site_url( '/?sendpress_display=true&spemail='.$email ) ),
				'return'          => urlencode( $return ),
				'sendpress_display' => 'true',
				'spemail' => $email
			),
			'customize.php'
		);
		return $link;
	}



	static function update_templates() {
		sendpress_register_template(
			array(
				'slug' => 'original',
				'path' => SENDPRESS_PATH . 'templates/original.html',
				'name' => 'SendPress Original'
			)
		);
		sendpress_register_template(
			array(
				'slug' => '1column',
				'path' => SENDPRESS_PATH . 'templates/1column.html',
				'name' => 'Responsive 1 Column'
			)
		);
		sendpress_register_template(
			array(
				'slug' => '2columns-to-rows',
				'path' => SENDPRESS_PATH . 'templates/2columns-to-rows.html',
				'name' => '2 Column Top - Wide Bottom - Responsive'
			)
		);

	}

	function init() {
		add_action('sendpress_template_loaded', array('SendPress_Videos', 'add_video_filter') );
		//add_action('register_form',array( $this , 'add_registration_fields'));
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
		{
			SendPress_Ajax_Loader::init();
		} else {
			SendPress_Pro_Manager::init();

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			if ( defined( 'WP_ADMIN' ) && WP_ADMIN == true ) {
				$sendpress_screen_options = new SendPress_Screen_Options();
			}
				//add_filter( 'cron_schedules', array($this,'cron_schedule' ));
				//add_action( 'wp_loaded', array( $this, 'add_cron' ) );

				if ( SendPress_Option::get( 'sp_widget_shortdoces' ) ) {
						add_filter( 'widget_text', 'do_shortcode' );
					}
				add_image_size( 'sendpress-max', 600, 600 );
				add_filter( 'template_include', array( $this, 'template_include' ), 1 );
				add_action( 'sendpress_cron_action', array( $this, 'sendpress_cron_action_run' ) );


				//using this for now, might find a different way to include things later
				// global $load_signup_js;
				// $load_signup_js = false;

				add_action( 'wp_enqueue_scripts', array( $this, 'add_front_end_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'add_front_end_styles' ) );

				
				add_action( 'wp_loaded', array( 'SendPress_Cron' , 'auto_cron' ) );
		        add_filter( 'cron_schedules', array( 'SendPress_Cron', 'cron_schedules' ) );
		        

		}
		
		if( !defined('SPNL_DISABLE_SENDING_WP_MAIL') && apply_filters('spnl_wpmail_sending', true ) ){
			sendpress_register_sender( 'SendPress_Sender_Website' );
		}
		
		if( !defined('SPNL_DISABLE_SENDING_GMAIL') && apply_filters('spnl_gmail_sending', true ) ){
			sendpress_register_sender( 'SendPress_Sender_Gmail' );
		}

		if( !defined('SPNL_DISABLE_SENDING_DELIVERY') && apply_filters('spnl_delivery_sending', true ) ){
			sendpress_register_sender( 'SendPress_Sender_SPNL' );
		}
		

	

		do_action( 'sendpress_init' );

		SendPress_Admin::add_cap( 'Emails_Send', 'sendpress_email_send' );
		$indexer    = "";
		$permalinks = get_option( 'permalink_structure' );

		if ( $permalinks ) {
			$pos = strpos( $permalinks, "index.php" );

			if ( $pos > 0 ) { // note: three equal signs
				$indexer = "index.php/";
			}
		}

		add_rewrite_rule(
			"^{$indexer}sendpress/([^/]+)/?",
			'index.php?sendpress=$matches[1]',
			"top" );

		
		$this->add_custom_post();


		if( defined( 'DOING_AJAX' ) || ( isset( $_GET['sendpress_display'] ) && 'true' == $_GET['sendpress_display'] ) ) {
			$this->loader->add_action( 'customize_register', $this->customizer, 'register_customize_sections' );
			$this->loader->add_action( 'customize_section_active', $this->customizer, 'remove_other_sections', 10, 2 );
			$this->loader->add_action( 'customize_panel_active', $this->customizer, 'remove_other_panels', 10, 2 );
			$this->loader->add_action( 'template_include', $this->customizer, 'capture_customizer_page' );
		}

		if( isset( $_GET['sendpress_display'] ) ) {
			$this->loader->add_action( 'customize_controls_enqueue_scripts', $this->customizer, 'enqueue_scripts' );
			$this->loader->add_action( 'customize_preview_init', $this->customizer, 'enqueue_template_scripts', 99 );
			//$this->loader->add_action( 'init', $this->customizer, 'remove_all_actions', 99 );
			$this->customizer->remove_all_actions();
		}
		$this->loader->run();

	}

	function add_registration_fields() {

		//Get and set any values already sent
		$user_extra = SPNL()->validate->_isset('user_extra') ? SPNL()->validate->_string('user_extra') : '';
		?>

		<p>
			<label for="user_extra">
				<input type="checkbox" name="user_extra" id="user_extra"
				       value="<?php echo esc_attr( stripslashes( $user_extra ) ); ?>"/> <?php _e( 'Join our mailing List.', 'sendpress' ); ?>
			</label><br>
		</p><br>

		<?php
	}

	static function add_cron() {

		if ( SendPress_Option::get( 'autocron', 'no' ) == 'yes' && wp_next_scheduled( 'sendpress_cron_action' ) ) {
			wp_clear_scheduled_hook( 'sendpress_cron_action' );
		} else {
			if ( ! wp_next_scheduled( 'sendpress_cron_action' ) ) {

				wp_schedule_event( time(), 'hourly', 'sendpress_cron_action' );
			}
		}
	}

	function user_has_cap( $all, $caps, $args ) {

		if ( isset( $args[2] ) ) {
			$post = get_post( $args[2] );
			if ( $post !== null && $post->post_type == 'sp_newsletters' ) {
				if ( current_user_can( 'sendpress_email' ) ) {
					foreach ( $caps as $cap ) {
						$all[ $cap ] = 1;
					}


				}


			}

		}

		return $all;

	}


	function load_plugin_language() {
		//load_plugin_textdomain( 'sendpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		// Set filter for plugin's languages directory
		$sendpress_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$sendpress_lang_dir = apply_filters( 'sendpress_languages_directory', $sendpress_lang_dir );
		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'sendpress' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'sendpress', $locale );
		// Setup paths to current locale file
		$mofile_local  = $sendpress_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/sendpress/' . $mofile;
		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/sendpress folder
			load_textdomain( 'sendpress', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/easy-digital-downloads/languages/ folder
			load_textdomain( 'sendpress', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'sendpress', false, $sendpress_lang_dir );
		}
	}

	/**
	 * Register our widget.
	 * 'SendPress_Signup_Widget' is the widget class used below.
	 *
	 * @since 1.0
	 */
	function load_widgets() {
		register_widget( 'SendPress_Widget_Signup' );
		register_widget( 'SendPress_Widget_Forms' );
	}


	function admin_notice() {
		//This is the WordPress one shows above menu area.
		//echo 'wtf';
	}

	function sendpress_notices() {
		if ( in_array( 'settings', $this->_messages ) ) {
			echo '<div class="error"><p>';
			echo "<strong>";
			_e( 'Warning!', 'sendpress' );
			echo "</strong>&nbsp;";
			printf( __( '  Before sending any emails please setup your <a href="%1s">information</a>.', 'sendpress' ), SendPress_Admin::link( 'Settings_Account' ) );
			echo '</p></div>';
		}

		$pause_sending = SendPress_Option::get( 'pause-sending', 'no' );
		//Stop Sending for now
		if ( $pause_sending == 'yes' ) {
			echo '<div class="error"><p>';
			echo "<strong>";
			_e( 'Warning!', 'sendpress' );
			echo "</strong>&nbsp;";
			printf( __( '  Sending has been paused. You can resume sending on the <a href="%1s">Queue</a> page.', 'sendpress' ), SendPress_Admin::link( 'Queue' ) );
			echo '</p></div>';
		}


	}

	/**
	 * ready_for_sending
	 *
	 * @access public
	 *
	 * @return mixed Value.
	 */
	function ready_for_sending() {

		$ready   = true;
		$message = '';

		$from = SendPress_Option::get( 'fromname' );
		if ( $from == false || $from == '' ) {
			$ready = false;
			$this->show_message( 'settings' );
		}

		$fromemail = SendPress_Option::get( 'fromemail' );
		if ( ( $from == false || $from == '' ) && ! is_email( $fromemail ) ) {
			$ready = false;
			$this->show_message( 'settings' );
		}

		/*
		$canspam = SendPress_Option::get('canspam');
		if($canspam == false || $canspam == ''){
			$ready = false;
			$this->show_message('settings');
		}
		 */

		$this->ready = $ready;
	}

	function show_message( $item ) {
		if ( ! in_array( $item, $this->_messages ) ) {
			array_push( $this->_messages, $item );
		}
	}

	// Hook into that action that'll fire weekly
	function sendpress_cron_action_run() {
		if ( SendPress_Option::get( 'autocron', 'no' ) == 'yes' ) {
			return;
		}

		$cron_url     = site_url( 'wp-cron.php' ) . '?&action=sendpress&silent=1&t=' . time();
		$cron_request = apply_filters( 'cron_request', array(
			'url'  => $cron_url,
			'args' => array(
				'timeout'   => 0.01,
				'blocking'  => false,
				'sslverify' => apply_filters( 'https_local_ssl_verify', true )
			)
		) );

		wp_remote_post( $cron_url, $cron_request['args'] );

	}

	function cron_schedule( $schedules ) {
		$schedules['tenminutes'] = array(
			'interval' => 300, // 1 week in seconds
			'display'  => __( 'Once Every Minute' ),
		);

		return $schedules;
	}

	function template_include( $template ) {
		global $post;
		if ( ( get_query_var( 'sendpress' ) ) ||  SPNL()->validate->_isset('sendpress')  ) {
			add_filter( 'do_rocket_lazyload', '__return_false' );

			$action = SPNL()->validate->_isset('sendpress') ? SPNL()->validate->_string('sendpress'): get_query_var( 'sendpress' ) ;
			//Look for encrypted data
			$data = SendPress_Data::decrypt( $action );
			$view = false;

			if ( is_object( $data ) ) {
				$view = isset( $data->view ) ? $data->view : false;
			} else {
				$view = $action;
			}
			$view_class = SendPress_Data::get_public_view_class( $view );
			if ( class_exists( $view_class ) ) {
				$view_class = NEW $view_class;
				$view_class->data( $data );
				if ( isset( $_POST['sp'] ) && wp_verify_nonce( $_POST['sp'], 'sendpress-form-post' ) && method_exists( $view_class, 'save' ) ) {
					$view_class->save();
				}
				$view_class->prerender();
				$view_class->render();
			}
			//$this->load_default_screen($action);
			die();
		}

		if ( isset( $post ) ) {
			if ( $post->post_type == $this->_email_post_type || $post->post_type == $this->_report_post_type ) {

				$inline = false;
				if ( SPNL()->validate->_isset('inline') ) {
					$inline = true;
				}

				SendPress_Email_Cache::build_cache_for_email( $post->ID );

				$message = new SendPress_Email();
				$message->id( $post->ID );
				$message->subscriber_id( 0 );
				$message->list_id( 0 );
				$body = $message->html();
				//print_r( $body );
				unset( $message );

				echo $body;
				die();
				//SendPress_Template::get_instance()->render_html(false, true, $inline );
				//return SENDPRESS_PATH. '/template-loader.php';
				//return dirname(__FILE__) . '/my_special_template.php';
			}
			/**
			 *
			 * if($post->post_type == 'sp-standard' ){
			 *    return 'You Bet';
			 * }
			 **/
		}

		return $template;
	}

	static function add_vars( $public_query_vars ) {
		$public_query_vars[] = 'sendpress';
		$public_query_vars[] = 'spmanage';
		$public_query_vars[] = 'splist';
		$public_query_vars[] = 'spreport';
		$public_query_vars[] = 'spurl';
		$public_query_vars[] = 'spemail';
		$public_query_vars[] = 'spms';

		return $public_query_vars;
	}


	function add_custom_post() {
		SendPress_Posts::email_post_type( $this->_email_post_type );
		SendPress_Posts::report_post_type( $this->_report_post_type );
		SendPress_Posts::template_post_type();
		SendPress_Posts::list_post_type();

		do_action( 'sendpress_custom_post_types_created', $this );
	}


	function create_color_picker( $value ) { ?>
		<input class="cpcontroller " data-id="<?php echo $value['id']; ?>" css-id="<?php echo $value['css']; ?>"
		       link-id="<?php echo $value['link']; ?>" name="<?php echo $value['id']; ?>"
		       id="<?php echo $value['id']; ?>" type="text"
		       value="<?php echo isset( $value['value'] ) ? $value['value'] : $value['std']; ?>"/>
		<input type='hidden' value='<?php echo $value['std']; ?>' id='default_<?php echo $value['id']; ?>'/>
		<a href="#" class="btn btn-default btn-xs reset-line" data-type="cp"
		   data-id="<?php echo $value['id']; ?>">Reset</a>
		<div id="pickholder_<?php echo $value['id']; ?>" class="colorpick clearfix" style="display:none;">
			<a class="close-picker">x</a>

			<div id="<?php echo $value['id']; ?>_colorpicker" class="colorpicker_space"></div>
		</div>
		<?php
	}


	function create_color_picker_iframe( $value ) { ?>
		<input class="cpcontroller" iframe="true" data-id="<?php echo $value['id']; ?>"
		       css-id="<?php echo $value['css']; ?>" target="<?php echo $value['iframe']; ?>"
		       link-id="<?php echo $value['link']; ?>" name="<?php echo $value['id']; ?>"
		       id="<?php echo $value['id']; ?>" type="text"
		       value="<?php echo isset( $value['value'] ) ? $value['value'] : $value['std']; ?>"/>
		<input type='hidden' value='<?php echo $value['std']; ?>' id='default_<?php echo $value['id']; ?>'/>
		<a href="#" class="btn btn-default btn-xs reset-line" data-type="cp"
		   data-id="<?php echo $value['id']; ?>">Reset</a>
		<div id="pickholder_<?php echo $value['id']; ?>" class="colorpick clearfix" style="display:none;">
			<a class="close-picker">x</a>

			<div id="<?php echo $value['id']; ?>_colorpicker" class="colorpicker_space"></div>
		</div>
		<?php
	}

	function plugin_mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$mce_css .= plugins_url( '/templates/simple.css', __FILE__ );
		$mce_css .= ',';
		$mce_css .= plugins_url( '/css/editor.css', __FILE__ );

		return $mce_css;
	}




	/* Display a notice that can be dismissed */

	function sendpress_ignore_087() {
		/* Check that the user hasn't already clicked to ignore the message */
		if ( ! SendPress_Option::get( 'sendpress_ignore_087' ) ) {
			echo '<div class="updated"><p>';
			printf( __( '<b>SendPress</b>: We have upgraded your lists to a new format. Please check your <a href="%1$s">widget settings</a> to re-enable your list(s). | <a href="%2$s">Hide Notice</a>' ), admin_url( 'widgets.php' ), admin_url( 'widgets.php?sendpress_ignore_087=0' ) );
			echo "</p></div>";
		}
	}


	function myformatTinyMCE( $in ) {
		if ( isset( $in['plugins'] ) ) {
			$in['plugins'] = str_replace( 'wpeditimage,', '', $in['plugins'] );
		}

		return $in;
	}


	function admin_init() {
			
		$this->maybe_upgrade();
		if ( ! empty( $_GET['_wp_http_referer'] ) && ( isset( $_GET['page'] ) && in_array( SPNL()->validate->page(), $this->adminpages ) ) ) {
			//safe redirect with esc_url 4/20
			wp_safe_redirect( esc_url_raw( remove_query_arg( array(
				'_wp_http_referer',
				'_wpnonce'
			), stripslashes( $_SERVER['REQUEST_URI'] ) ) ) );
			exit;
		}


		if ( isset( $_REQUEST['post_id'] ) ) {
			$p = get_post( $_REQUEST['post_id'] );
			if ( $p !== null && $p->post_type == 'sp_newsletters' ) {
				add_filter( 'disable_captions', create_function( '$a', 'return true;' ) );
			}
		}


		//Removed in 0.9.2
		//$this->create_initial_list();

		/*
		if( SendPress_Option::get('emails-today') == false ){
			$emails_today = array( date("z") => '0' );
			SendPress_Option::set('emails-today', $emails_today);
		}

		$emails_today = SendPress_Option::get('emails-today');
		$emails_today[date("z") + 1 ] = '0';

		SendPress_Option::set('emails-today', $emails_today);
		*/
		//SendPress_Option::set('emails-today', '');
		//SendPress_Option::set('allow_tracking', '');
		//wp_clear_scheduled_hook( 'sendpress_cron_action' );
		// Schedule an action if it's not already scheduled
		/*
		if ( ! wp_next_scheduled( 'sendpress_cron_action' ) ) {
		    wp_schedule_event( time(), 'tenminutes',  'sendpress_cron_action' );
		}
		*/

		//wp_clear_scheduled_hook( 'sendpress_cron_action' );

		/*
		add_meta_box( 'email-status', __( 'Email Status', 'sendpress' ), array( $this, 'email_meta_box' ), $this->_email_post_type, 'side', 'low' );

		*/


		//MAKE SURE WE ARE ON AN ADMIN PAGE
		if ( SPNL()->validate->page() !== false ) {
			$this->_page = SPNL()->validate->page();
			$this->_current_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : '';

			//echo $this->_page;
			$view_class = $this->get_view_class( $this->_page, $this->_current_view );

            //Securiry check for view 
			if( !is_user_logged_in() ){
				wp_die('Cheating I see..');
			};
			
			//SendPress_Tracking::init();
			SendPress_Notifications_Manager::init();

			if ( isset( $_GET['spv'] ) ) {
				SendPress_Option::set( 'version', $_GET['spv'] );
			}

			if ( isset( $_GET['sp-admin-code'] ) && current_user_can( 'manage_options' ) ) {
				switch ( $_GET['sp-admin-code'] ) {
					case 'install-tables':
						$this->install_tables();
						break;
					case 'remove-key':
						SendPress_Option::set( 'api_key', '' );
						SendPress_Pro_Manager::set_pro_state( false ); //this will delete the transient
						break;
					default:
						# code...
						break;
				}


			}

			$this->ready_for_sending();
			
			add_action( 'admin_print_scripts', array( $this, 'editor_insidepopup' ) );
			add_filter( 'gettext', array( $this, 'change_button_text' ), null, 2 );
			add_action( 'sendpress_notices', array( $this, 'sendpress_notices' ) );
			add_filter( 'user_has_cap', array( $this, 'user_has_cap' ), 10, 3 );

			//SendPress_Option::set('default-signup-widget-settings',false);

		



			remove_action( 'admin_init', 'Zotpress_add_meta_box', 1 );
			remove_filter( 'mce_external_plugins', 'cforms_plugin' );
			remove_filter( 'mce_buttons', 'cforms_button' );
			remove_filter( "mce_plugins", "cforms_plugin" );
			remove_filter( 'mce_buttons', 'cforms_button' );
			remove_filter( 'tinymce_before_init', 'cforms_button_script' );


			if ( SPNL()->validate->page() == 'sp-templates' || ( isset( $_GET['view'] ) && $_GET['view'] == 'style-email' ) ) {
				wp_register_script( 'sendpress_js_styler', SENDPRESS_URL . 'js/styler.js', '', SENDPRESS_VERSION );
			}
			if ( defined( 'WPE_PLUGIN_BASE' ) ) {
				add_action( 'admin_print_styles', array( $this, 'remove_wpengine_style' ) );
			}


			
			add_filter( 'tiny_mce_before_init', array( $this, 'myformatTinyMCE' ) );


			if ( isset( $_GET['beta'] ) ) {
				SendPress_Option::set( 'beta', absint( $_GET['beta'] ) );
			}

			if ( isset( $_GET['trackeroff'] ) ) {
				SendPress_Option::set( 'tracker_off',  $_GET['trackeroff'] );
			}

			remove_editor_styles();
			add_filter( 'mce_css', array( $this, 'plugin_mce_css' ) );
			//Stop Facebook Plugin from posting emails to Facebook.
			remove_action( 'transition_post_status', 'fb_publish_later', 10, 3 );

			$tiny                = new SendPress_TinyMCE();
			

			
			//Securiry check for view
			$view_class = NEW $view_class;
			$view_class->admin_init();
			add_action( 'sendpress_admin_scripts', array( $view_class, 'admin_scripts_load' ) );
			$this->_current_action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
			$this->_current_action = isset( $_GET['action2'] ) ? sanitize_text_field( $_GET['action2'] ) : $this->_current_action;
			$this->_current_action = isset( $_POST['action2'] ) ? sanitize_text_field( $_POST['action2'] ) : $this->_current_action;
			$this->_current_action = isset( $_POST['action'] ) && sanitize_text_field( $_POST['action'] ) !== '-1' ? sanitize_text_field( $_POST['action'] ) : $this->_current_action;
			$method                = str_replace( "-", "_", $this->_current_action );
			$method                = str_replace( " ", "_", $method );

			if ( method_exists( $view_class, 'security_check' ) ) {
				call_user_func( array( $view_class, 'security_check' ) );
     		}

			if ( ! empty( $_POST ) && ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->_nonce_value ) ) ) {

				if ( method_exists( $view_class, $method ) ) {


					//view_class$save_class = new $view_class;

					$view_class->$method();
					//print_r($save_class);
				} elseif ( method_exists( $view_class, 'save' ) ) {
					//$view_class::save($this);
					//$save_class = new $view_class;
					$view_class->save( );
				} else {


					require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-post-actions.php' );

				}

			} else if ( isset( $_GET['action'] ) || isset( $_GET['action2'] ) ) {
				$this->_current_action = sanitize_text_field( $_GET['action'] );
				$this->_current_action = ( isset( $_GET['action2'] ) && sanitize_text_field( $_GET['action2'] ) !== '-1' ) ? sanitize_text_field( $_GET['action2'] ) : $this->_current_action;
				$method                = str_replace( "-", "_", $this->_current_action );
				$method                = str_replace( " ", "_", $method );
				if ( method_exists( $view_class, $method ) ) {
					call_user_func( array( $view_class, $method ) );
					die();
				}


				require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-get-actions.php' );
			}

		}
	}

	function wp_enqueue_script() {


		global $pagenow;

		wp_register_script( 'sendpress-widget-js', SENDPRESS_URL . 'js/sendpress.widget.js', '', SENDPRESS_VERSION );
		if ( $pagenow === 'widgets.php' ) {
			wp_enqueue_script( 'sendpress-widget-js' );
		}

		//MAKE SURE WE ARE ON AN ADMIN PAGE
		if ( is_admin() && SPNL()->validate->page() !== false ) {

			wp_enqueue_style( 'thickbox' );
			wp_register_script( 'spfarb', SENDPRESS_URL . 'js/farbtastic.js', '', SENDPRESS_VERSION );
			wp_register_script( 'sendpress-doughnut', SENDPRESS_URL . 'js/jquery.circliful.min.js', '', SENDPRESS_VERSION );

			wp_register_script( 'sendpress-flot', SENDPRESS_URL . 'js/flot/jquery.flot.js', '', SENDPRESS_VERSION );
			wp_register_script( 'sendpress-flot-selection', SENDPRESS_URL . 'js/flot/jquery.flot.selection.js', '', SENDPRESS_VERSION );
			wp_register_script( 'sendpress-flot-resize', SENDPRESS_URL . 'js/flot/jquery.flot.resize.js', '', SENDPRESS_VERSION );


			wp_register_script( 'sendpress-admin-js', SENDPRESS_URL . 'js/sendpress.js', array( 'backbone' ), SENDPRESS_VERSION );
			//wp_register_script('sendpress-backbone-js', SENDPRESS_URL .'js/spnl-backbone.js',array('wp-backbone','jquery','underscore'), SENDPRESS_VERSION );
			wp_register_script( 'sendpress_bootstrap', SENDPRESS_URL . 'bootstrap/js/bootstrap.min.js', '', SENDPRESS_VERSION );

			wp_register_style( 'sendpress_bootstrap_css', SENDPRESS_URL . 'bootstrap/css/bootstrap.css', '', SENDPRESS_VERSION );

			wp_register_style( 'sendpress_css_admin', SENDPRESS_URL . 'css/admin.css', array(
				'sendpress_bootstrap_css',
				'sendpress_css_base'
			), SENDPRESS_VERSION );
			wp_register_style( 'sendpress_css_base', SENDPRESS_URL . 'css/style.css', array( 'sendpress_bootstrap_css' ), SENDPRESS_VERSION );

			wp_register_script( 'sendpress_ls', SENDPRESS_URL . 'js/jquery.autocomplete.js', '', SENDPRESS_VERSION );
			wp_enqueue_script( 'sendpress-doughnut' );

			wp_register_script( 'sendpress_js_styler', SENDPRESS_URL . 'js/styler.js', '', SENDPRESS_VERSION );


			wp_enqueue_script( 'sendpress_bootstrap' );
			wp_enqueue_style( 'sendpress_bootstrap_css' );
			wp_enqueue_style( 'sendpress_css_base' );
			wp_enqueue_style( 'sendpress_css_admin' );
			if ( SPNL()->validate->page() == 'sp-templates' || SPNL()->validate->_string('view') == 'style-email'  ) {
				wp_enqueue_script( 'sendpress_js_styler' );
			}


			if ( $this->_page == 'help' ) {
				wp_enqueue_script( 'dashboard' );
			}
			wp_enqueue_style( 'farbtastic' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'editor' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'sendpress-admin-js' );
			wp_enqueue_script( 'spfarb' );
			wp_enqueue_script( 'sendpress_ls' );

		
		wp_enqueue_script( 'sendpress-backbone-js' );


		wp_localize_script( 'sendpress-admin-js', 'spvars', array(
				// URL to wp-admin/admin-ajax.php to process the request
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'wpcronurl'      => site_url( 'wp-cron.php' ),
				// generate a nonce with a unique ID "myajax-post-comment-nonce"
				// so that you can check it later when an AJAX request is sent
				'sendpressnonce' => wp_create_nonce( SendPress_Ajax_Loader::$priv_ajax_nonce ),
			)
		);
		do_action( 'sendpress_admin_scripts' );
		}
	}

	static function remove_wpengine_style() {
		wp_dequeue_style( 'wpe-common' );
		wp_deregister_style( 'wpe-common' );
	}


	function add_front_end_scripts() {
		$widget_options = SendPress_Option::get( 'widget_options' );
		if ( isset( $widget_options['load_ajax'] ) && $widget_options['load_ajax'] != 1 && ! is_admin() ) {
			wp_register_script( 'sendpress-signup-form-js', SENDPRESS_URL . 'js/sendpress.signup.js', array( 'jquery' ), SENDPRESS_VERSION, (bool) $widget_options['load_scripts_in_footer'] );
			wp_enqueue_script( 'sendpress-signup-form-js' );
			wp_localize_script( 'sendpress-signup-form-js', 'sendpress', array(
				'invalidemail' => __( "Please enter your e-mail address", "sendpress" ),
				'missingemail' => __( "Please enter your e-mail address", "sendpress" ),
				'required' => __( "Please enter all the required fields. <br> Required fields are marked with an (*)", "sendpress" ),
				'ajaxurl'      => admin_url( 'admin-ajax.php' )
			) );
		}
	}


	function add_front_end_styles() {
		$widget_options = SendPress_Option::get( 'widget_options' );
		if ( ! $widget_options['load_css'] ) {
			wp_enqueue_style( 'sendpress-fe-css', SENDPRESS_URL . 'css/front-end.css' );
		}
	}

	function change_button_text( $translation, $original ) {
		// We don't pass "type" in our custom upload fields, yet WordPress does, so ignore our function when WordPress has triggered the upload popup.
		if (  SPNL()->validate->_isset('type')  ) {
			return $translation;
		}

		if ( $original == 'Insert into Post' ) {
			$translation = __( 'Use this Image', 'sendpress' );
			if ( SPNL()->validate->_isset('title') ) {
				$translation = sprintf( __( 'Use as %s', 'sendpress' ), esc_attr( SPNL()->validate->_string('title') ) );
			}
		}

		return $translation;
	}

	function save_redirect() {
		$act = SPNL()->validate->_string('save-action');
		if ( !empty($act) ) {
			switch ( $act ) {
				case 'save-confirm-send':
					wp_redirect( esc_url_raw( admin_url( '?page=' . SPNL()->validate->page() . '&view=send-confirm&emailID=' . SPNL()->validate->_int('post_ID')) ) );
					break;
				case 'save-style':
					wp_redirect( esc_url_raw( admin_url( '?page=' . SPNL()->validate->page() . '&view=style&emailID=' . SPNL()->validate->_int('post_ID') )) );
					break;
				case 'save-create':
					wp_redirect( esc_url_raw( admin_url( '?page=' . SPNL()->validate->page() . '&view=style&emailID=' . SPNL()->validate->_int('post_ID') ) ) );
					break;
				case 'save-send':
					wp_redirect( esc_url_raw( admin_url( '?page=' . SPNL()->validate->page() . '&view=send&emailID=' . SPNL()->validate->_int('post_ID') ) ) );
					break;
				default:
					wp_redirect( esc_url_raw( admin_url( SPNL()->validate->_string('_wp_http_referer') ) ) );
					break;
			}
		}

	}

	function styler_menu( $active ) {
		?>
		<div id="styler-menu">
			<div style="float:right;" class="btn-group">
				<?php if ( $this->_current_view == 'edit-email' ) { ?>
					<a href="#" id="save-update" class="btn btn-primary btn-large "><i
							class="icon-white icon-ok"></i> <?php echo __( 'Update', 'sendpress' ); ?></a><a href="#"
					                                                                                         id="save-update"
					                                                                                         class="btn btn-primary btn-large"><i
							class="icon-ok icon-white"></i> <?php echo __( 'Save & Next', 'sendpress' ); ?></a>
				<?php } ?>
				<?php if ( $this->_current_view == 'style' ) { ?>
					<a href="#" id="save-update" class="btn btn-primary btn-large "><i
							class="icon-white icon-ok"></i> <?php echo __( 'Update', 'sendpress' ); ?></a>
					<?php if ( SendPress_Admin::access( 'Emails_Send' ) ) { ?>
						<a href="#" id="save-send-email" class="btn btn-primary btn-large "><i
								class="icon-envelope icon-white"></i> <?php echo __( 'Send', 'sendpress' ); ?></a>
					<?php } ?>
				<?php } ?>
				<?php if ( $this->_current_view == 'styles' ) { ?>
					<a href="#" id="save-update" class="btn btn-primary btn-large "><i
							class="icon-white icon-ok"></i> <?php echo __( 'Update', 'sendpress' ); ?></a><a href="#"
					                                                                                         id="save-send-email"
					                                                                                         class="btn btn-primary btn-large "><i
							class="icon-envelope icon-white"></i> <?php echo __( 'Send', 'sendpress' ); ?></a>
				<?php } ?>
				<?php if ( $this->_current_view == 'send' ) { ?>
					<a href="?page=<?php echo SPNL()->validate->page(); ?>&view=style&emailID=<?php echo SPNL()->validate->_int('emailID'); ?>"
					   class="btn btn-primary btn-large "><i
							class="icon-white icon-pencil"></i> <?php echo __( 'Edit', 'sendpress' ); ?></a><a href="#"
					                                                                                           id="save-update"
					                                                                                           class="btn btn-primary btn-large"><i
							class="icon-white icon-envelope"></i> <?php echo __( 'Send', 'sendpress' ); ?></a>
				<?php } ?>
				<?php if ( $this->_current_view == 'create' ) { ?>
					<a href="#" id="save-update" class="btn btn-primary btn-large"><i
							class="icon-ok icon-white"></i> <?php echo __( 'Save & Next', 'sendpress' ); ?></a>
				<?php } ?>
			</div>
			<div id="sp-cancel-btn" style="float:right; margin-top: 5px;">
				<a href="?page=<?php echo SPNL()->validate->page(); ?>" id="cancel-update"
				   class="btn"><?php echo __( 'Cancel', 'sendpress' ); ?></a>&nbsp;
			</div>
		</div>
		<?php
	}

	function editor_insidepopup() {


		if ( isset( $_REQUEST['is_sendpress'] ) && $_REQUEST['is_sendpress'] == 'yes' ) {
			add_action( 'admin_head', array( &$this, 'js_popup' ) );

			//dd_filter( 'media_upload_tabs', 'woothemes_mlu_modify_tabs' );
		}
	}

	function js_popup() {
		$_title = 'file';

		if ( isset( $_REQUEST['sp_title'] ) ) {
			$_title = $_REQUEST['sp_title'];
		} // End IF Statement
		?>
		<script type="text/javascript">
			<!--
			jQuery(function ($) {
				jQuery.noConflict();

				// Change the title of each tab to use the custom title text instead of "Media File".
				$('h3.media-title').each(function () {
					var current_title = $(this).html();

					var new_title = current_title.replace('media file', '<?php echo $_title; ?>');

					$(this).html(new_title)
				});

				// Hide the "Insert Gallery" settings box on the "Gallery" tab.
				$('div#gallery-settings').hide();

				// Preserve the "is_woothemes" parameter on the "delete" confirmation button.
				$('.savesend a.del-link').click(function () {
					var continueButton = $(this).next('.del-attachment').children('a.button[id*="del"]');

					var continueHref = continueButton.attr('href');

					continueHref = continueHref + '&is_sendpress=yes';

					continueButton.attr('href', continueHref);
				});
			});
			-->
		</script>
		<?php
	}

	function my_help_tabs_to_theme_page() {
		require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-help-tabs.php' );
	}

	function admin_menu() {


		if ( current_user_can( 'sendpress_view' ) ) {
			$role = "sendpress_view";
		} else {
			$role = "manage_options";
		}
		$queue = '';

		if ( SPNL()->validate->page() !== false ) {
			$queue = '(<span id="queue-count-menu">-</span>)';//SendPress_Data::emails_in_queue();
		}
		
		$plugin_name = __( 'SendPress', 'sendpress' );
		if ( defined( 'SENDPRESS_PRO_VERSION' ) ) {
			$plugin_name .= " " . __( 'Pro', 'sendpress' );
		}

		$this->add_menu_link();

		add_menu_page( $plugin_name, $plugin_name, $role, 'sp-overview', array(
			&$this,
			'render_view'
		), 'dashicons-email' );

		

			add_submenu_page( 'sp-overview', __( 'Overview', 'sendpress' ), __( 'Overview', 'sendpress' ), $role, 'sp-overview', array(
				&$this,
				'render_view'
			) );
		

		if( apply_filters( 'spnl_emails', true ) ) {
			
			add_submenu_page( 'sp-overview', __( 'Emails', 'sendpress' ), __( 'Emails', 'sendpress' ), $role, 'sp-emails', array(
				&$this,
				'render_view'
			) );

		}

		if( apply_filters( 'spnl_reports', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Reports', 'sendpress' ), __( 'Reports', 'sendpress' ), $role, 'sp-reports', array(
				&$this,
				'render_view'
			) );
		}

		if( apply_filters( 'spnl_subscribers', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Subscribers', 'sendpress' ), __( 'Subscribers', 'sendpress' ), $role, 'sp-subscribers', array(
				&$this,
				'render_view'
			) );
		}

		if( apply_filters( 'spnl_queue', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Queue', 'sendpress' ), __( 'Queue', 'sendpress' ) . " " . $queue, $role, 'sp-queue', array(
				&$this,
				'render_view'
			) );
		}

		if( apply_filters( 'spnl_settings', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Settings', 'sendpress' ), __( 'Settings', 'sendpress' ), $role, 'sp-settings', array(
				&$this,
				'render_view'
			) );
		}

		if( apply_filters( 'spnl_help', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Help', 'sendpress' ), __( 'Help', 'sendpress' ), $role, 'sp-help', array(
				&$this,
				'render_view'
			) );
		}

		if( apply_filters( 'spnl_pro', true ) ) {
			add_submenu_page( 'sp-overview', __( 'Pro', 'sendpress' ), __( 'Pro', 'sendpress' ), $role, 'sp-pro', array(
				&$this,
				'render_view'
			) );
		}


	}

	function render_view() {

		$view_class = $this->get_view_class( $this->_page, $this->_current_view );
		//echo "About to render: $view_class, $this->_page";
		$view_class = NEW $view_class;
		$queue      = '<span id="queue-count-menu-tab">-</span>';
		//$queue = //SendPress_Data::emails_in_queue();

		//add tabs
		$view_class->add_tab( __( 'Overview', 'sendpress' ), 'sp-overview', ( $this->_page === 'sp-overview' ) );

		if( apply_filters( 'spnl_emails', true ) ) {
			$view_class->add_tab( __( 'Emails', 'sendpress' ), 'sp-emails', ( $this->_page === 'sp-emails' ) );
		}

		if( apply_filters( 'spnl_reports', true ) ) {
			$view_class->add_tab( __( 'Reports', 'sendpress' ), 'sp-reports', ( $this->_page === 'sp-reports' ) );
		}

		if( apply_filters( 'spnl_subscribers', true ) ) {
			$view_class->add_tab( __( 'Subscribers', 'sendpress' ), 'sp-subscribers', ( $this->_page === 'sp-subscribers' ) );
		}

		if( apply_filters( 'spnl_queue', true ) ) {
			$view_class->add_tab( __( 'Queue', 'sendpress' ) . ' <small>(' . $queue . ')</small>', 'sp-queue', ( $this->_page === 'sp-queue' ) );
		}

		if( apply_filters( 'spnl_settings', true ) ) {
			$view_class->add_tab( __( 'Settings', 'sendpress' ), 'sp-settings', ( $this->_page === 'sp-settings' ) );
		}

		if( apply_filters( 'spnl_help', true ) ) {
			$view_class->add_tab( __( 'Help', 'sendpress' ), 'sp-help', ( $this->_page === 'sp-help' ) );
		}

		if( apply_filters( 'spnl_pro', true ) ) {
			$view_class->add_tab( __( 'Pro', 'sendpress' ), 'sp-pro', ( $this->_page === 'sp-pro' ) );
		}

		$view_class->prerender( $this );
		$view_class->render( $this );
	}

	/**
	 * Get field class name
	 *
	 * @param string $type Field type
	 *
	 * @return bool|string Field class name OR false on failure
	 */
	static function get_view_class( $main, $view = false ) {
		$page      = explode( '-', $main );
		$classname = '';
		foreach ( $page as $p ) {
			if ( $p != 'sp' ) {
				if ( $classname !== '' ) {
					$classname = '_';
				}
				$classname .= ucwords( $p );

			}
		}

		if ( $view !== false ) {
			$view  = str_replace( '-', ' ', $view );
			$view  = ucwords( $view );
			$view  = str_replace( ' ', '_', $view );
			$class = "SendPress_View_{$classname}_{$view}";

			if ( class_exists( $class ) ) {
				return $class;
			}
		}

		$class = "SendPress_View_{$classname}";

		if ( class_exists( $class ) ) {
			return $class;
		}

		return "SendPress_View";
	}


	function maybe_upgrade() {

		//SendPress::update_templates();
		$current_version = SendPress_Option::get( 'version', '0' );
		//SendPress_Error::log($current_version);

		if ( version_compare( $current_version, SENDPRESS_VERSION, '==' ) ) {
			return;
		}

		$current_version_base = SendPress_Option::base_get( 'version', '0' );

		if ( version_compare( $current_version_base, SENDPRESS_VERSION, '==' ) ) {
			return;
		}

		update_option('sendpress_flush_rewrite_rules', true);
		SendPress_DB_Tables::install();
		@SPNL()->load("Subscribers_Tracker")->create_table();
		@SPNL()->load("Subscribers_Url")->create_table();
		@SPNL()->load("Url")->create_table();
		@SPNL()->load("Autoresponder")->create_table();
		@SPNL()->load("Schedules")->create_table();
		@SPNL()->load("Remote_Connection")->create_table();
		@SPNL()->load("Customfields")->create_table();
		//@SPNL()->load("Suppression")->create_table();

		SendPress_Option::base_set( 'update-info', 'show' );
		//On version change update default template
		$this->set_template_default();

		SendPress_Template_Manager::update_template_content();

		//SendPress_Data::create_default_form();

		SendPress_Option::check_for_keys();

		if ( version_compare( $current_version, '0.8.6', '<' ) ) {
			$widget_options = array();

			$widget_options['widget_options']['load_css']               = 0;
			$widget_options['widget_options']['load_ajax']              = 0;
			$widget_options['widget_options']['load_scripts_in_footer'] = 0;

			SendPress_Option::set( $widget_options );
		}


		if ( version_compare( $current_version, '0.8.7.5', '<' ) ) {
			SendPress_Data::set_double_optin_content();
		}


		if ( version_compare( $current_version, '0.8.8', '<' ) ) {
			$pro_plugins                               = array();
			$pro_plugins['pro_plugins']['setup_value'] = false;
			SendPress_Option::set( $pro_plugins );
		}


		if ( version_compare( $current_version, '0.9.3', '<' ) ) {

			$options = SendPress_Option::get( 'notification_options' );

			$new_options = array(
				'email'                              => '',
				'name'                               => '',
				'notifications-enable'               => false,
				'notifications-subscribed-instant'   => false,
				'notifications-subscribed-daily'     => false,
				'notifications-subscribed-weekly'    => false,
				'notifications-subscribed-monthly'   => false,
				'notifications-unsubscribed-instant' => false,
				'notifications-unsubscribed-daily'   => false,
				'notifications-unsubscribed-weekly'  => false,
				'notifications-unsubscribed-monthly' => false
			);

			if ( $options === false || $options === '' ) {

				SendPress_Option::set( 'notification_options', $new_options );
			} else if ( is_array( $options ) ) {
				$result = array_merge( $new_options, $options );
				SendPress_Option::set( 'notification_options', $result );
			}

		}
		/*

		if(version_compare( $current_version, '0.9.4.7', '<' )){
			SendPress_Data::update_tables_0947();
		}
		if(version_compare( $current_version, '0.9.5.2', '<' )){
			SendPress_Data::update_tables_0952();
		}

		if(version_compare( $current_version, '0.9.5.4', '<' )){
			SendPress_Data::update_tables_0954();
		}
		*/


		if ( version_compare( $current_version, '0.9.6', '<' ) ) {

			$options = SendPress_Option::get( 'notification_options' );

			$new_options = array(
				'email'                     => '',
				'notifications-enable'      => false,
				'subscribed'                => 1,
				'unsubscribed'              => 1,
				'send-to-admins'            => false,
				'enable-hipchat'            => false,
				'hipchat-api'               => '',
				'hipchat-room'              => '',
				'post-notifications-enable' => false,
				'post-notification-subject' => ''
			);

			if ( $options === false || $options === '' ) {
				SendPress_Option::set( 'notification_options', $new_options );
			} else if ( is_array( $options ) ) {
				$result = array_merge( $new_options, $options );
				SendPress_Option::set( 'notification_options', $result );
			}

		}

		if ( version_compare( $current_version, '0.9.9', '<' ) ) {
			$link = SendPress_Option::get( 'socialicons' );

			if ( $twit = SendPress_Option::get( 'twitter' ) ) {
				$link['Twitter'] = $twit;
			}

			if ( $fb = SendPress_Option::get( 'facebook' ) ) {
				$link['Facebook'] = $fb;
			}
			if ( $ld = SendPress_Option::get( 'linkedin' ) ) {
				$link['LinkedIn'] = $ld;
			}
			SendPress_Option::set( 'socialicons', $link );
		}

		if ( version_compare( $current_version, '1.9.2.21', '<' ) ) {
			SendPress_Data::upgrade_custom_fields();
		}
	

		$update_options_sp = array();

		if ( SendPress_Option::get( 'sendmethod' ) == false ) {
			$update_options_sp['sendmethod'] = 'SendPress_Sender_Website';
		}

		if ( SendPress_Option::get( 'send_optin_email' ) == false ) {
			$update_options_sp['send_optin_email'] = 'yes';
		}

		if ( SendPress_Option::get( 'try-theme' ) == false ) {
			$update_options_sp['try-theme'] = 'yes';
		}

		if ( SendPress_Option::get( 'confirm-page' ) == false ) {
			$update_options_sp['confirm-page'] = 'default';
		}

		if ( SendPress_Option::get( 'manage-page' ) == false ) {
			$update_options_sp['manage-page'] = 'default';
		}

		if ( SendPress_Option::get( 'cron_send_count' ) == false ) {
			$update_options_sp['cron_send_count'] = '100';
		}

		if ( SendPress_Option::get( 'emails-per-day' ) == false ) {
			$update_options_sp['emails-per-day'] = '1000';
		}		
		if ( SendPress_Option::get( 'emails-per-hour' ) == false ) {
			$update_options_sp['emails-per-hour'] = '100';
		}
		if ( SendPress_Option::get( 'queue-per-call' ) == false ) {
			$update_options_sp['queue-per-call'] = '1000';
	
		}

		if ( ! empty( $update_options_sp ) ) {
			SendPress_Option::set( $update_options_sp );
			unset( $update_options_sp );
		}
		SendPress_Option::base_set( 'version', SENDPRESS_VERSION );
		SendPress_Option::set( 'version', SENDPRESS_VERSION );
	}

	function set_template_default() {
		$default_style_post = SendPress_Data::get_template_id_by_slug( 'default-style' );
		update_post_meta( $default_style_post, 'body_bg', '#E8E8E8' );
		update_post_meta( $default_style_post, 'body_text', '#231f20' );
		update_post_meta( $default_style_post, 'body_link', '#21759B' );
		update_post_meta( $default_style_post, 'header_bg', '#DDDDDD' );
		update_post_meta( $default_style_post, 'header_text_color', '#333333' );

		update_post_meta( $default_style_post, 'content_bg', '#FFFFFF' );
		update_post_meta( $default_style_post, 'content_text', '#222222' );
		update_post_meta( $default_style_post, 'sp_content_link_color', '#21759B' );
		update_post_meta( $default_style_post, 'content_border', '#E3E3E3' );

		$optin        = SendPress_Data::get_template_by_slug( 'double-optin' );
		$update_optin = false;

		if ( $optin->post_content == "" ) {
			$optin->post_content = SendPress_Data::optin_content();
			$update_optin        = true;
		}

		//clear the cached file.
		if ( $optin->post_title == "" ) {
			$optin->post_title = SendPress_Data::optin_title();
			$update_optin      = true;
		}

		if ( $update_optin == true ) {
			wp_update_post( $optin );
		}

		delete_transient( 'sendpress_email_html_' . $optin->ID );
	}

	function wpdbQuery( $query, $type ) {
		global $wpdb;
		// eliminate warnings with debug mode
		if ( $type == 'prepare' ) {
			$result = $wpdb->$type( $query, array() );
		} else {
			$result = $wpdb->$type( $query );
		}

		return $result;
	}

	function wpdbQueryArray( $query ) {
		global $wpdb;
		$result = $wpdb->get_results( $query, ARRAY_N );

		return $result;
	}


	function get_opens_unique_count( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT COUNT( DISTINCT subscriberID ) FROM $table WHERE reportID = %d AND type = 'open';", $rid ), 'get_var' );

		return $result;
	}

	function get_opens_unique( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT * FROM $table WHERE reportID = %d AND type = 'open' GROUP BY subscriberID ORDER BY eventID DESC; ", $rid ), 'get_results' );

		return $result;
	}

	function get_opens( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT * FROM $table WHERE reportID =  %d AND type = 'open'  ORDER BY eventID DESC;", $rid ), 'get_results' );

		return $result;
	}

	function get_opens_count( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT COUNT(1) as count FROM $table WHERE reportID = %d AND type = 'open';", $rid ), 'get_var' );

		return $result;
	}

	function get_clicks_unique_count( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT COUNT( DISTINCT subscriberID )  FROM $table WHERE reportID =  %d AND type = 'click';", $rid ), 'get_var' );

		return $result;
	}

	function get_clicks_unique( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT * FROM $table WHERE reportID = %d AND type = 'click' GROUP BY subscriberID ORDER BY eventID DESC;", $rid ), 'get_results' );

		return $result;
	}

	function get_clicks( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT * FROM $table WHERE reportID = %d AND type = 'click'  ORDER BY eventID DESC;", $rid ), 'get_results' );

		return $result;
	}

	function get_clicks_count( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT COUNT(1) FROM $table WHERE reportID = %d AND type = 'click';", $rid ), 'get_var' );

		return $result;
	}

	function get_clicks_and_opens( $rid ) {
		global $wpdb;
		$table  = SendPress_Data::subscriber_event_table();
		$result = $this->wpdbQuery( $wpdb->prepare( "SELECT * FROM $table WHERE reportID = %d ORDER BY eventID DESC;", $rid ), 'get_results' );

		return $result;
	}



	function updateList( $listID, $values ) {
		return SendPress_Data::update_list( $listID, $values );
	}

	



	function the_content( $content ) {

		global $post;
		$optin = SendPress_Data::get_template_id_by_slug( 'double-optin' );
		if ( $post->post_type == 'sptemplates' && $post->ID == $optin ) {
			$content .= "";
		}

		return $content;

	}


	/**
	 * plugin_activation
	 *
	 * @access public
	 *
	 * @return mixed Value.
	 */
	static function plugin_activation( $networkwide = false ) {

		if ( ! is_multisite() || ! $networkwide ) {
			SendPress::plugin_install();
		} else {
			SendPress::network_activate_deactivate( true );
		}

	}


	/**
	 * Run network-wide (de-)activation of the plugin
	 *
	 * @param bool $activate True for plugin activation, false for de-activation
	 */
	static function network_activate_deactivate( $activate = true ) {
		global $wpdb;

		$original_blog_id = get_current_blog_id(); // alternatively use: $wpdb->blogid
		$all_blogs        = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

		if ( is_array( $all_blogs ) && $all_blogs !== array() ) {
			foreach ( $all_blogs as $blog_id ) {
				switch_to_blog( $blog_id );

				if ( $activate === true ) {
					SendPress::plugin_install();
				} else {
					SendPress::plugin_remove();
				}
			}
			// Restore back to original blog
			switch_to_blog( $original_blog_id );
		}

	}


	/**
	 * Run SendPress install if plugin is network activated
	 * network-wide.
	 *
	 * Will only be called by multisite actions.
	 * @internal Unfortunately will fail if the plugin is in the must-use directory
	 * @see https://core.trac.wordpress.org/ticket/24205
	 */
	static function on_activate_blog( $blog_id ) {

		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( is_plugin_active_for_network( plugin_basename( SENDPRESS_FILE ) ) ) {
			switch_to_blog( $blog_id );
			SendPress::plugin_install();
			restore_current_blog();
		}
	}


	static function plugin_install() {
		if ( version_compare( $GLOBALS['wp_version'], SENDPRESS_MINIMUM_WP_VERSION, '<' ) ) {
			deactivate_plugins( __FILE__ );
			wp_die( sprintf( __( 'SendPress requires WordPress version %s or later.', 'sendpress' ), SENDPRESS_MINIMUM_WP_VERSION ) );
		} else {
			SendPress_DB_Tables::install();
			@SPNL()->load("Subscribers_Tracker")->create_table();
			@SPNL()->load("Subscribers_Url")->create_table();
			@SPNL()->load("Url")->create_table();
		}
		//Make sure we stop the old action from running
		wp_clear_scheduled_hook( 'sendpress_cron_action_run' );
		/*
		$api = new SendPress_API();
		$api->add_endpoint();
		flush_rewrite_rules();
		*/
		

		SendPress_Option::set( 'install_date', time() );
		update_option('sendpress_flush_rewrite_rules', true);
	}

	static function plugin_remove() {
		flush_rewrite_rules();
		wp_clear_scheduled_hook( 'sendpress_cron_action' );
		wp_clear_scheduled_hook( 'sendpress_notification_daily' );
	}

	/**
	 *
	 *    Nothing going on here yet
	 * @static
	 */
	static function plugin_deactivation( $networkwide = false ) {
		if ( ! is_multisite() || ! $networkwide ) {
			SendPress::plugin_remove();
		} else {

			SendPress::network_activate_deactivate( false );
		}
	}

	function cron_stop() {
		$upload_dir = wp_upload_dir();
		$filename   = $upload_dir['basedir'] . '/sendpress.pause';
		if ( file_exists( $filename ) ) {
			return true;
		}

		return false;
	}

	function cron_start() {
		$upload_dir = wp_upload_dir();
		$filename   = $upload_dir['basedir'] . '/sendpress.pause';
		if ( file_exists( $filename ) ) {
			unlink( $filename );
		}
	}

	static function flush_rewrite_rules()
	{
	    if ( get_option('sendpress_flush_rewrite_rules') ) {
	    	flush_rewrite_rules();
	        delete_option('sendpress_flush_rewrite_rules');
	    }
	}


}// End SP CLASS

add_action('init', array( 'SendPress', 'flush_rewrite_rules' ), 99);
add_filter( 'query_vars', array( 'SendPress', 'add_vars' ) );
add_action( 'admin_init', array( 'SendPress', 'add_cron' ) );
register_activation_hook( __FILE__, array( 'SendPress', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'SendPress', 'plugin_deactivation' ) );
add_action( 'wpmu_new_blog', array( 'SendPress', 'on_activate_blog' ) );
add_action( 'activate_blog', array( 'SendPress', 'on_activate_blog' ) );


//add_filter('spnl_delivery_sending','__return_false');
// Initialize!
function SPNL() {
	return SendPress::get_instance();
}

SPNL();

if ( defined( 'SENDPRESS_PRO_PATH' ) && ! defined( 'SENDPRESS_PRO_LOADED' ) && function_exists( 'SPPRO' ) ) {
	define( 'SENDPRESS_PRO_LOADED', true );
	SPPRO();
}
