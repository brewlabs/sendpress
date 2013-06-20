<?php 
/*
Plugin Name: SendPress: Email Marketing and Newsletters
Version: 0.9.4.1
Plugin URI: http://sendpress.com
Description: Easy to manage Email Markteing and Newsletter plugin for WordPress. 
Author: SendPress
Author URI: http://sendpress.com/
Push
*/

	if ( !defined('DB_NAME') ) {
		header('HTTP/1.0 403 Forbidden');
		die;
	}
	
	defined( 'SENDPRESS_API_BASE' ) or define( 'SENDPRESS_API_BASE', 'https://api.sendpres.com' );
	define( 'SENDPRESS_API_VERSION', 1 );
	define( 'SENDPRESS_MINIMUM_WP_VERSION', '3.2' );
	define( 'SENDPRESS_VERSION', '0.9.4.1' );
	define( 'SENDPRESS_URL', plugin_dir_url(__FILE__) );
	define( 'SENDPRESS_PATH', plugin_dir_path(__FILE__) );
	define( 'SENDPRESS_BASENAME', plugin_basename( __FILE__ ) );
	
	if(!defined('SENDPRESS_STORE_URL') ){
		define( 'SENDPRESS_STORE_URL', 'http://sendpress.com' );
	}
	if(!defined('SENDPRESS_PRO_NAME') ){
		define( 'SENDPRESS_PRO_NAME', 'SendPress Pro' );
	}
	
	/*
	*
	*	Supporting Classes they build out the WordPress table views.
	*
	*/
	if(!class_exists('WP_List_Table')){
	    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	
	// AutoLoad Classes
	spl_autoload_register(array('SendPress', 'autoload'));
	
	
	require_once( SENDPRESS_PATH . 'inc/functions.php' );
	/*
	require_once( SENDPRESS_PATH . 'classes/class-file-loader.php' );
	$sp_loader = new File_Loader('SendPress Required Class');
	*/
	//require_once( SENDPRESS_PATH . 'classes/selective-loader.php' );
	if( !defined('SENDPRESS_TRANSIENT_LENGTH') ){
		define( 'SENDPRESS_TRANSIENT_LENGTH', WEEK_IN_SECONDS );
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
	
		var $adminpages = array('sp','sp-overview','sp-reports','sp-emails','sp-templates','sp-subscribers','sp-settings','sp-queue','sp-pro','sp-help');
	
		var $_templates = array();
		var $_messages = array();
	
		var $_page = '';
	
		var $testmode = false;
	
		var $_posthelper = '';
	
		var $_debugAddress = 'josh@sendpress.com';
	
		var $_debugMode = false;
	
		private static $instance;

		/**
		 * [log description]
		 * @param  [type] $args [description]
		 * @return [type]       [description]
		 */
		function log($args) {
			return SendPress_Helper::log($args);
		}
	
		function append_log($msg, $queueid = -1) {
			return SendPress_Helper::append_log($msg, $queueid);
		}
		
		function nonce_value(){
			return 'sendpress-is-awesome';
		}
		
		
		function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'widgets_init', array( $this,'load_widgets') );
			add_action('plugins_loaded',array( $this, 'load_plugin_language'));
				
			do_action( 'sendpress_loaded' );
		}
	
		public static function autoload($className) {
		  	if( strpos($className, 'SendPress') !== 0 ){
		  		return;
		  	}
		  
		    $cls = str_replace('_', '-',	strtolower($className) );
		    if( substr($cls, -1) == '-'){
		    	//AutoLoad seems to get odd clasname sometimes that ends with _
	  			return;
		    }
		    if(class_exists($className)){
		    	return;
		    }
	
		    if( strpos($className, 'Public_View') != false ){
		    	if( defined('SENDPRESS_PRO_PATH') ) {
		    		$pro_file = SENDPRESS_PRO_PATH."classes/public-views/class-".$cls.".php";
		    		if( file_exists( $pro_file ) ){
		    			include SENDPRESS_PRO_PATH."classes/public-views/class-".$cls.".php";
		    			return;
		    		}
		    	}
		    	include SENDPRESS_PATH."classes/public-views/class-".$cls.".php";
		  		return;
		  	} 
	
		    if( strpos($className, 'View') != false ){
		    	if( defined('SENDPRESS_PRO_PATH') ) {
		    		$pro_file = SENDPRESS_PRO_PATH."classes/views/class-".$cls.".php";
		    		if( file_exists( $pro_file ) ){
		    			include SENDPRESS_PRO_PATH."classes/views/class-".$cls.".php";
		    			return;
		    		}
		    	}
		    	include SENDPRESS_PATH."classes/views/class-".$cls.".php";
		  		return;
		  	}
	
		  	if( strpos($className, 'Module') != false ){
		  		if( defined('SENDPRESS_PRO_PATH') ) {
		    		$pro_file = SENDPRESS_PRO_PATH."classes/modules/class-".$cls.".php";
		    		if( file_exists( $pro_file ) ){
		    			include SENDPRESS_PRO_PATH."classes/modules/class-".$cls.".php";
		    			return;
		    		}
		    	}
	
		    	include SENDPRESS_PATH."classes/modules/class-".$cls.".php";
		  		return;
		  	} 
	
		    if( defined('SENDPRESS_PRO_PATH') ) {
	    		$pro_file = SENDPRESS_PRO_PATH."classes/class-".$cls.".php";
	    		if( file_exists( $pro_file ) ){
	    			include SENDPRESS_PRO_PATH."classes/class-".$cls.".php";
	    			return;
	    		}
	    	}
		    include SENDPRESS_PATH."classes/class-".$cls.".php";
	    
	  }
	
		static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				$class_name = __CLASS__;
				self::$instance = new $class_name;
			}
			return self::$instance;
		}
	
		function init() {
			$this->maybe_upgrade();
			SendPress_Ajax_Loader::init();
			SendPress_Signup_Shortcode::init();
			SendPress_Sender::init();
			SendPress_Pro_Manager::init();
			SendPress_Cron::get_instance();
			SendPress_Notifications_Manager::init();
		
			sendpress_register_sender('SendPress_Sender_Website');
			sendpress_register_sender('SendPress_Sender_Gmail');
	
			SendPress_Admin::add_cap('Emails_Send','sendpress_email_send');
	
			    add_rewrite_rule(  
        "sendpress/([^/]+)/?",  
        'index.php?sendpress=$matches[1]',  
        "top"); 
			
			if(defined('WP_ADMIN') && WP_ADMIN == true){
				$sendpress_screen_options = new SendPress_Screen_Options();
			}
		
			$this->add_custom_post();
			
			//add_filter( 'cron_schedules', array($this,'cron_schedule' ));
			
			if( is_admin() ){
				if( isset($_GET['spv'])){
					SendPress_Option::set( 'version' , $_GET['spv'] );
				}
				
				$this->ready_for_sending();
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_notices', array( $this,'admin_notice') );
				add_action( 'admin_print_scripts', array($this,'editor_insidepopup') );
				add_filter( 'gettext', array($this, 'change_button_text'), null, 2 );
				add_action( 'sendpress_notices', array( $this,'sendpress_notices') );
				add_filter('user_has_cap',array( $this,'user_has_cap') , 10 , 3);
			}
	
			
			add_image_size( 'sendpress-max', 600, 600 );
			add_filter( 'template_include', array( $this, 'template_include' ) );
			add_action( 'sendpress_cron_action', array( $this,'sendpress_cron_action_run') );
			if ( !wp_next_scheduled( 'sendpress_cron_action' ) ) {
				wp_schedule_event( time(), 'hourly', 'sendpress_cron_action' );
				//wp_schedule_event( time(), 'hourly', 'my_hourly_event');
			}
			
			//using this for now, might find a different way to include things later
			// global $load_signup_js;
			// $load_signup_js = false;
			
			add_action( 'get_header', array( $this, 'add_front_end_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_front_end_styles' ) );
	
			add_action( 'wp_head', array( $this, 'handle_front_end_posts' ) );
			
		}
	
		function user_has_cap($all, $caps, $args){
	
			if(isset($args[2])){
				$post = get_post( $args[2] );
				if($post !== null && $post->post_type == 'sp_newsletters'){
					if( current_user_can('sendpress_email') ){
						foreach($caps as $cap){
							$all[$cap] = 1;
						}
						
	
					}
	
	
				}
	
			}
			return $all;
	
		}
	
	
		function load_plugin_language(){
			load_plugin_textdomain( 'sendpress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}
	
			/**
	 * Register our widget.
	 * 'SendPress_Signup_Widget' is the widget class used below.
	 *
	 * @since 1.0
	 */
			function load_widgets() {
				register_widget( 'SendPress_Widget_Signup' );
			}
	
	
		function admin_notice(){
			//This is the WordPress one shows above menu area.
			//echo 'wtf';
	
		}
		function sendpress_notices(){
			if( in_array('settings', $this->_messages) ){
			echo '<div class="alert alert-error">';
				echo "<b>";
				_e('Warning','sendpress');
				echo "</b>&nbsp;";
				printf(__('Before sending any emails please setup your <a href="%1s">information</a>.','sendpress'), SendPress_Admin::link('Settings') );
		    echo '</div>';
			}
		}
	
	    /**
	 * ready_for_sending
	 * 
	 * @access public
	 *
	 * @return mixed Value.
	 */
		function ready_for_sending(){
			
			$ready = true;
			$message = '';
	
			$from = SendPress_Option::get('fromname');
			if($from == false || $from == ''){
				$ready = false;	
				$this->show_message('settings');
			}
	
			$fromemail = SendPress_Option::get('fromemail');
			if( ( $from == false || $from == '' ) && !is_email( $fromemail ) ){
				$ready = false;	
				$this->show_message('settings');
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
	
		function show_message($item){
			if(!in_array($item,$this->_messages) ){
				array_push($this->_messages, $item);
			}	
		}
	 
		// Hook into that action that'll fire weekly
		function sendpress_cron_action_run() {
			$this->fetch_mail_from_queue();
		}
	 
		function cron_schedule( $schedules ) {
		    $schedules['tenminutes'] = array(
		        'interval' => 300, // 1 week in seconds
		        'display'  => __( 'Once Every Minute' ),
		    );
		 
		    return $schedules;
		}
	
		// Start of Presstrends Magic
		function presstrends_plugin() {
	
			// PressTrends Account API Key
			$api_key = 'eu1x95k67zut64gsjb5qozo7whqemtqiltzu';
			$auth = 'j0nc5cpqb2nlv8xgn0ouo7hxgac5evn0o';
	
			// Start of Metrics
			global $wpdb;
			$data = get_transient( 'presstrends_data' );
			if (!$data || $data == ''){
				$api_base = 'http://api.presstrends.io/index.php/api/pluginsites/update/auth/';
				$url = $api_base . $auth . '/api/' . $api_key . '/';
				$data = array();
				$count_posts = wp_count_posts();
				$count_pages = wp_count_posts('page');
				$comments_count = wp_count_comments();
				$theme_data = get_theme_data(get_stylesheet_directory() . '/style.css');
				$plugin_count = count(get_option('active_plugins'));
				$all_plugins = get_plugins();
				foreach($all_plugins as $plugin_file => $plugin_data) {
				$plugin_name .= $plugin_data['Name'];
				$plugin_name .= '&';}
				$plugin_data = get_plugin_data( __FILE__ );
				$plugin_version = $plugin_data['Version'];
				$posts_with_comments = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type='post' AND comment_count > 0");
				$comments_to_posts = number_format(($posts_with_comments / $count_posts->publish) * 100, 0, '.', '');
				$pingback_result = $wpdb->get_var('SELECT COUNT(comment_ID) FROM '.$wpdb->comments.' WHERE comment_type = "pingback"');
				$data['url'] = stripslashes(str_replace(array('http://', '/', ':' ), '', site_url()));
				$data['posts'] = $count_posts->publish;
				$data['pages'] = $count_pages->publish;
				$data['comments'] = $comments_count->total_comments;
				$data['approved'] = $comments_count->approved;
				$data['spam'] = $comments_count->spam;
				$data['pingbacks'] = $pingback_result;
				$data['post_conversion'] = $comments_to_posts;
				$data['theme_version'] = $plugin_version;
				$data['theme_name'] = urlencode($theme_data['Name']);
				$data['site_name'] = str_replace( ' ', '', get_bloginfo( 'name' ));
				$data['plugins'] = $plugin_count;
				$data['plugin'] = urlencode($plugin_name);
				$data['wpversion'] = get_bloginfo('version');
				foreach ( $data as $k => $v ) {
				$url .= $k . '/' . $v . '/';}
				$response = wp_remote_get( $url );
				set_transient('presstrends_data', $data, 60*60*24);
			}
		}
	
		function template_include( $template ) { 
		  	global $post; 
	
		  	if( (get_query_var( 'sendpress' )) || isset($_POST['sendpress']) ){
			  	
			  	$action = isset($_POST['sendpress']) ? $_POST['sendpress'] : get_query_var( 'sendpress' );
				//Look for encrypted data
		  		$data = SendPress_Data::decrypt( urldecode($action) );
				$view = false;
			 	if(is_object($data)){
			 		$view = isset($data->view) ? $data->view : false;
			 	} else {
			 		$view= $action;
			 	}
	
			 	$view_class = SendPress_Data::get_public_view_class($view);
			 	if(class_exists($view_class)){
			 		$view_class = NEW $view_class;
			 		$view_class->data($data);
					if( isset( $_POST['sp'] ) && wp_verify_nonce( $_POST['sp'],'sendpress-form-post') && method_exists($view_class, 'save') ){
						$view_class->save();
					} 
					$view_class->prerender();
					$view_class->render();
				}
			  	//$this->load_default_screen($action);
				die();
			}
	
		  	if(isset($post)){
	 			if($post->post_type == $this->_email_post_type || $post->post_type == $this->_report_post_type  ) {
	  				
	  				$inline = false;
					if(isset($_GET['inline']) ){
						$inline = true;
					}
					return SendPress_Template::get_instance()->render(false, true, $inline );
	  				//return SENDPRESS_PATH. '/template-loader.php';
	    		//return dirname(__FILE__) . '/my_special_template.php'; 
				}
			}
	  		return $template; 
		} 
		
	    /**
	 * has_identity_key
	 * 
	 * @access public
	 *
	 * @return mixed Value.
	 */
		function has_identity_key(){
			$key = (get_query_var('fxti')) ? get_query_var('fxti') : false;
			if(false == $key)
				return $key;
			$result =  $this->getSubscriberbyKey( $key );
			if(!empty( $result ) ) {
				return true;
			}
			return false;
		}
	
		function add_vars($public_query_vars) {
			$public_query_vars[] = 'fxti';
			$public_query_vars[] = 'sendpress';
			$public_query_vars[] = 'splist';
			$public_query_vars[] = 'spreport';
			$public_query_vars[] = 'spurl';
			$public_query_vars[] = 'spemail';
			return $public_query_vars;
		}
	
		function add_custom_post(){
			SendPress_Posts::email_post_type( $this->_email_post_type );
			SendPress_Posts::report_post_type( $this->_report_post_type );
			SendPress_Posts::template_post_type();
			SendPress_Posts::list_post_type();
		}
	
		
	
		function SendPress(){
			//$this->_templates = $this->get_templates();
		}
		
		function create_color_picker( $value ) { ?>	
		<input class="cpcontroller" data-id="<?php echo $value['id']; ?>" css-id="<?php echo $value['css']; ?>" link-id="<?php echo $value['link']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php  echo isset($value['value']) ? $value['value'] : $value['std'] ; ?>" />
		<input type='hidden' value='<?php echo $value['std'];?>' id='default_<?php echo $value['id']; ?>'/>
		<a href="#" class="btn btn-mini reset-line" data-type="cp" data-id="<?php echo $value['id']; ?>" >Reset</a>
		<div id="pickholder_<?php echo $value['id']; ?>" class="colorpick clearfix" style="display:none;">
			<a class="close-picker">x</a>
			<div id="<?php echo $value['id']; ?>_colorpicker" class="colorpicker_space"></div>
		</div>
		<?php
	}


	function create_color_picker_iframe( $value ) { ?>	
		<input class="cpcontroller" iframe="true" data-id="<?php echo $value['id']; ?>" css-id="<?php echo $value['css']; ?>" target="<?php echo $value['iframe']; ?>" link-id="<?php echo $value['link']; ?>" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php  echo isset($value['value']) ? $value['value'] : $value['std'] ; ?>" />
		<input type='hidden' value='<?php echo $value['std'];?>' id='default_<?php echo $value['id']; ?>'/>
		<a href="#" class="btn btn-mini reset-line" data-type="cp" data-id="<?php echo $value['id']; ?>" >Reset</a>
		<div id="pickholder_<?php echo $value['id']; ?>" class="colorpick clearfix" style="display:none;">
			<a class="close-picker">x</a>
			<div id="<?php echo $value['id']; ?>_colorpicker" class="colorpicker_space"></div>
		</div>
		<?php
	}

	function plugin_mce_css( $mce_css ) {
		if ( ! empty( $mce_css ) )
			$mce_css .= ',';

		$mce_css .= plugins_url( '/templates/simple.css', __FILE__ );
		$mce_css .= ',';
		$mce_css .= plugins_url( '/css/editor.css', __FILE__ );
		return $mce_css;
	}


	function add_caps() {
		global $wp_roles;

	    if ( ! isset( $wp_roles ) )
    		$wp_roles = new WP_Roles();
		$role = $wp_roles->get_role( 'administrator' ); // gets the author role
	 	$role->add_cap( 'manage_sendpress' ); // would allow the author to edit others' posts for current theme only
	}

	/* Display a notice that can be dismissed */
		
	function sendpress_ignore_087() {
	        /* Check that the user hasn't already clicked to ignore the message */
	    if ( ! SendPress_Option::get('sendpress_ignore_087') ) {
	        echo '<div class="updated"><p>';
	        printf(__('<b>SendPress</b>: We have upgraded your lists to a new format. Please check your <a href="%1$s">widget settings</a> to re-enable your list(s). | <a href="%2$s">Hide Notice</a>'), admin_url('widgets.php'), admin_url('widgets.php?sendpress_ignore_087=0') );
	        echo "</p></div>";
	    }
	}
		

	function myformatTinyMCE($in){
		$in['plugins']= str_replace('wpeditimage,', '', $in['plugins']);
		return $in;
	}



	function admin_init(){
		$this->set_template_default();
		$this->add_caps();




		if ( isset( $_REQUEST['post_id'] ) ){
   			$p = get_post($_REQUEST['post_id']);
   			if(  $p !==null && $p->post_type == 'sp_newsletters'){
   				add_filter('disable_captions', create_function('$a','return true;'));
   			}
   		}
		if ( isset($_GET['sendpress_ignore_087']) && '0' == $_GET['sendpress_ignore_087'] ) {
		    SendPress_Option::set('sendpress_ignore_087', 'true');
		}
		add_action('admin_notices', array($this,'sendpress_ignore_087'));

		if( SendPress_Option::get('sendmethod') == false ){
			SendPress_Option::set('sendmethod','website');
		}

		if( SendPress_Option::get('send_optin_email') == false ){
			SendPress_Option::set('send_optin_email','yes');
		}

		if( SendPress_Option::get('try-theme') == false ){
			SendPress_Option::set('try-theme','yes');
		}

		if( SendPress_Option::get('confirm-page') == false ){
			SendPress_Option::set('confirm-page','default');
		}

		if( SendPress_Option::get('cron_send_count') == false ){
			SendPress_Option::set('cron_send_count','100');
		}

		if( SendPress_Option::get('emails-per-day') == false ){
			SendPress_Option::set('emails-per-day','1000');
			SendPress_Option::set('emails-per-hour','100');
		}

		//Removed in 0.9.2
		//$this->create_initial_list();
		

		if( SendPress_Option::get('emails-today') == false ){
			$emails_today = array( date("z") => '0' );
			SendPress_Option::set('emails-today', $emails_today);
		}

		$emails_today = SendPress_Option::get('emails-today');
		$emails_today[date("z") + 1 ] = '0';

		SendPress_Option::set('emails-today', $emails_today);


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
		

		if( ( isset($_GET['page']) && $_GET['page'] == 'sp-templates' ) || (isset( $_GET['view'] ) && $_GET['view'] == 'style-email' )) {
			wp_register_script('sendpress_js_styler', SENDPRESS_URL .'js/styler.js' ,'', SENDPRESS_VERSION);
			wp_enqueue_script('sendpress_js_styler');
		}

		//MAKE SURE WE ARE ON AN ADMIN PAGE
		if(isset($_GET['page']) && in_array($_GET['page'], $this->adminpages)){

			if(SendPress_Option::get('whatsnew')){
				SendPress_Option::set('whatsnew',false);
				SendPress_Admin::redirect('Help_Whatsnew');
			}


			$this->_page = $_GET['page'];
			add_filter('tiny_mce_before_init',  array($this,'myformatTinyMCE') );
			

			if( isset($_GET['beta'])){
				SendPress_Option::set( 'beta' , absint( $_GET['beta'] ) );
			}

			remove_editor_styles();
			add_filter( 'mce_css', array($this,'plugin_mce_css') );
			//Stop Facebook Plugin from posting emails to Facebook.
			remove_action( 'transition_post_status', 'fb_publish_later', 10, 3);

			wp_enqueue_style( 'farbtastic' );
			$tiny = new SendPress_TinyMCE();
	   		$this->_current_view = isset( $_GET['view'] ) ? $_GET['view'] : '' ;

			wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload','dashboard'));
			wp_enqueue_style('thickbox');
			wp_register_script('spfarb', SENDPRESS_URL .'js/farbtastic.js' ,'', SENDPRESS_VERSION );
			wp_enqueue_script( 'spfarb' );
	
			wp_register_script('sendpress-flot', SENDPRESS_URL .'js/flot/jquery.flot.js' ,'', SENDPRESS_VERSION );
			wp_register_script('sendpress-flot-selection', SENDPRESS_URL .'js/flot/jquery.flot.selection.js' ,'', SENDPRESS_VERSION );
			wp_register_script('sendpress-flot-resize', SENDPRESS_URL .'js/flot/jquery.flot.resize.js' ,'', SENDPRESS_VERSION );
		

			wp_register_script('sendpress-admin-js', SENDPRESS_URL .'js/sendpress.js','', SENDPRESS_VERSION );
			wp_enqueue_script('sendpress-admin-js');
			wp_register_script('sendpress_bootstrap', SENDPRESS_URL .'bootstrap/js/bootstrap.min.js' ,'',SENDPRESS_VERSION);
			wp_enqueue_script('sendpress_bootstrap');
			wp_register_style( 'sendpress_bootstrap_css', SENDPRESS_URL . 'bootstrap/css/bootstrap.css', '', SENDPRESS_VERSION );
    		wp_enqueue_style( 'sendpress_bootstrap_css' );

    		wp_register_script('sendpress_ls', SENDPRESS_URL .'js/jquery.autocomplete.js' ,'', SENDPRESS_VERSION );
			wp_enqueue_script('sendpress_ls');
			//wp_localize_script( 'sendpress_js', 'sendpress', array( 'ajaxurl' => admin_url( 'admin-ajax.php', 'http' ) ) );

			wp_register_style( 'sendpress_jquery_ibutton_css', SENDPRESS_URL . 'css/jquery.ibutton.css', false, SENDPRESS_VERSION );
    		wp_enqueue_style( 'sendpress_jquery_ibutton_css' );
    		wp_register_script('sendpress_jquery_ibutton_js', SENDPRESS_URL .'js/jquery.ibutton.min.js' ,'',SENDPRESS_VERSION);
			wp_enqueue_script('sendpress_jquery_ibutton_js');

			wp_register_style( 'sendpress_css_base', SENDPRESS_URL . 'css/style.css', false, SENDPRESS_VERSION );
    		wp_enqueue_style( 'sendpress_css_base' );

	    	do_action('sendpress_admin_scripts');
	    	$view_class = $this->get_view_class($this->_page, $this->_current_view);
			$view_class = NEW $view_class;
			$view_class->admin_init();	
	    	$view_class = $this->get_view_class($this->_page, $this->_current_view);
	    		
	    	$this->_current_action = isset( $_GET['action'] ) ? $_GET['action'] : '' ;
		    $this->_current_action = isset( $_GET['action2'] ) ? $_GET['action2'] : $this->_current_action ;
		    $this->_current_action = isset( $_POST['action2'] ) ? $_POST['action2'] : $this->_current_action ;
		    $this->_current_action = isset( $_POST['action'] ) && $_POST['action'] !== '-1' ? $_POST['action'] : $this->_current_action ;
		    $method = str_replace("-","_", $this->_current_action);
	    	$method = str_replace(" ","_",$method);
	    		

	    	if ( !empty($_POST) &&  (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'],$this->_nonce_value) )   ){

	    		if( method_exists( $view_class , $method )  ){
	    			$save_class = new $view_class;
	    			$save_class->$method();
	    		} elseif( method_exists( $view_class , 'save' )  ) {
	    			//$view_class::save($this);
	    			$save_class = new $view_class;
	    			$save_class->save( $_POST, $this );
	    		} else {

		    	
		    	require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-post-actions.php' );
		    	
		    	}
	    	
	    	} else if ( isset( $_GET['action']) || isset( $_GET['action2'] )  ){

		    	$this->_current_action = $_GET['action'];
		    	$this->_current_action = ( isset( $_GET['action2'] ) && $_GET['action2'] !== '-1')  ? $_GET['action2'] : $this->_current_action ;
		    	$method = str_replace("-","_", $this->_current_action);
	    		$method = str_replace(" ","_",$method);
	    		if( method_exists( $view_class , $method ) ){
	    			$save_class = new $view_class;

	    			call_user_func(array($view_class, $method ),$_GET,$this);
	    		}

	    		

		    	require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-get-actions.php' );
	    	}
		}
   	}

   	

   	function add_front_end_scripts(){
		$widget_options = SendPress_Option::get('widget_options');

		wp_register_script('sendpress-signup-form-js', SENDPRESS_URL .'js/sendpress.signup.js', array('jquery'), SENDPRESS_VERSION, (bool)$widget_options['load_scripts_in_footer'] );
		wp_enqueue_script( 'sendpress-signup-form-js' );
		wp_localize_script( 'sendpress-signup-form-js', 'sendpress', array( 'invalidemail'=>__("Please enter your e-mail address","sendpress"),  'missingemail'=>__("Please enter your e-mail address","sendpress"), 'ajaxurl' => admin_url( 'admin-ajax.php', 'http' ) ) );
   	}

   	function handle_front_end_posts(){
   		if ( !empty($_POST) ){
	    	$this->_current_action = isset( $_POST['action'] ) ? $_POST['action'] : '';
	    	require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-fe-post-actions.php' );
    	}
   	}

   	function add_front_end_styles(){
   		$widget_options = SendPress_Option::get('widget_options');
   		if( !$widget_options['load_css'] ){
   			wp_enqueue_style( 'sendpress-fe-css', SENDPRESS_URL.'css/front-end.css' );
   		}
   	}

   	function change_button_text( $translation, $original ) {
		 // We don't pass "type" in our custom upload fields, yet WordPress does, so ignore our function when WordPress has triggered the upload popup.
	    if ( isset( $_REQUEST['type'] ) ) { return $translation; }
	    
	    if( $original == 'Insert into Post' ) {
	    	$translation = __( 'Use this Image', 'sendpress' );
			if ( isset( $_REQUEST['title'] ) && $_REQUEST['title'] != '' ) { $translation = sprintf( __( 'Use as %s', 'sendpress' ), esc_attr( $_REQUEST['title'] ) ); }
	    }
	
	    return $translation;
	} 

	function save_redirect(){
	    // echo $_POST['save-type'];

	    if( isset($_POST['save-action']) ){
		    switch ( $_POST['save-action'] ) {
		    	case 'save-confirm-send':
					 wp_redirect( '?page='.$_GET['page']. '&view=send-confirm&emailID='. $_POST['post_ID'] );
				break; 
				case 'save-style':
					 wp_redirect( '?page='.$_GET['page']. '&view=style&emailID='. $_POST['post_ID'] );
				break; 
				case 'save-create':
					 wp_redirect( '?page='.$_GET['page']. '&view=style&emailID='. $_POST['post_ID'] );
				break;
				case 'save-send':
					 wp_redirect( '?page='.$_GET['page']. '&view=send&emailID='. $_POST['post_ID'] );
				break;         
				default:
					wp_redirect( $_POST['_wp_http_referer'] );
				break;
		    }
		}

	}

	function styler_menu($active){
		?>
		<div id="styler-menu">
			<div style="float:right;" class="btn-group">
				<?php if($this->_current_view == 'edit-email'){ ?>
				<a href="#" id="save-update" class="btn btn-primary btn-large "><i class="icon-white icon-ok"></i> <?php echo __('Update','sendpress'); ?></a><a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-ok icon-white"></i> <?php echo __('Save & Next','sendpress'); ?></a>
				<?php } ?>
				<?php if($this->_current_view == 'style'){ ?>
				<a href="#" id="save-update" class="btn btn-primary btn-large "><i class="icon-white icon-ok"></i> <?php echo __('Update','sendpress'); ?></a>
				<?php if( SendPress_Admin::access('Emails_Send') ) { ?>
				<a href="#" id="save-send-email" class="btn btn-primary btn-large "><i class="icon-envelope icon-white"></i> <?php echo __('Send','sendpress'); ?></a>
				<?php } ?>	
				<?php } ?>
				<?php if($this->_current_view == 'styles'){ ?>
				<a href="#" id="save-update" class="btn btn-primary btn-large "><i class="icon-white icon-ok"></i> <?php echo __('Update','sendpress'); ?></a><a href="#" id="save-send-email" class="btn btn-primary btn-large "><i class="icon-envelope icon-white"></i> <?php echo __('Send','sendpress'); ?></a>
				<?php } ?>
				<?php if($this->_current_view == 'send'){ ?>
				<a href="?page=<?php echo $_GET['page']; ?>&view=style&emailID=<?php echo $_GET['emailID']; ?>" class="btn btn-primary btn-large "><i class="icon-white icon-pencil"></i> <?php echo __('Edit','sendpress'); ?></a><a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-envelope"></i> <?php echo __('Send','sendpress'); ?></a>	
				<?php } ?>
				<?php if($this->_current_view == 'create'){ ?>
				<a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-ok icon-white"></i> <?php echo __('Save & Next','sendpress'); ?></a>
				<?php } ?>
			</div>
			<div id="sp-cancel-btn" style="float:right; margin-top: 5px;">
				<a href="?page=<?php echo $_GET['page']; ?>" id="cancel-update" class="btn"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
			</div>
		</div>
		<?php
	}

   	function editor_insidepopup () {


   		if ( isset( $_REQUEST['is_sendpress'] ) && $_REQUEST['is_sendpress'] == 'yes' ) {
			add_action( 'admin_head', array(&$this,'js_popup') );

			//dd_filter( 'media_upload_tabs', 'woothemes_mlu_modify_tabs' );
		}
	}

	function js_popup () {
		$_title = 'file';

		if ( isset( $_REQUEST['sp_title'] ) ) { $_title = $_REQUEST['sp_title']; } // End IF Statement
	?>
	<script type="text/javascript">
		<!--
		jQuery(function($) {
			jQuery.noConflict();

			// Change the title of each tab to use the custom title text instead of "Media File".
			$( 'h3.media-title' ).each ( function () {
				var current_title = $( this ).html();

				var new_title = current_title.replace( 'media file', '<?php echo $_title; ?>' );

				$( this ).html( new_title )
			} );

			// Hide the "Insert Gallery" settings box on the "Gallery" tab.
			$( 'div#gallery-settings' ).hide();

			// Preserve the "is_woothemes" parameter on the "delete" confirmation button.
			$( '.savesend a.del-link' ).click ( function () {
				var continueButton = $( this ).next( '.del-attachment' ).children( 'a.button[id*="del"]' );

				var continueHref = continueButton.attr( 'href' );

				continueHref = continueHref + '&is_woothemes=yes';

				continueButton.attr( 'href', continueHref );
			} );
		});
		-->
	</script>
	<?php
	}

	function my_help_tabs_to_theme_page(){
		require_once( SENDPRESS_PATH . 'inc/helpers/sendpress-help-tabs.php' );
	}

	function admin_menu() {
		
		if( current_user_can('sendpress_view') ){
			$role = "sendpress_view";
		} else {
			$role = "manage_options";
		}
		$queue = SendPress_Data::emails_in_queue();
		add_menu_page(__('SendPress','sendpress'), __('SendPress','sendpress'), $role,'sp-overview',  array(&$this,'render_view') , SENDPRESS_URL.'img/icon.png');
	    add_submenu_page('sp-overview', __('Overview','sendpress'), __('Overview','sendpress'), $role, 'sp-overview', array(&$this,'render_view'));
	    $main = add_submenu_page('sp-overview', __('Emails','sendpress'), __('Emails','sendpress'), $role, 'sp-emails', array(&$this,'render_view'));
	    
	    add_submenu_page('sp-overview', __('Reports','sendpress'), __('Reports','sendpress'), $role, 'sp-reports', array(&$this,'render_view'));
	   	add_submenu_page('sp-overview', __('Subscribers','sendpress'), __('Subscribers','sendpress'), $role, 'sp-subscribers', array(&$this,'render_view'));
	    add_submenu_page('sp-overview', __('Queue','sendpress') ." (". $queue.")", __('Queue','sendpress')." (". $queue.")", $role, 'sp-queue', array(&$this,'render_view'));
	   	add_submenu_page('sp-overview', __('Settings','sendpress'), __('Settings','sendpress'), $role, 'sp-settings', array(&$this,'render_view'));
	  
	   	

	   	add_submenu_page('sp-overview', __('Help','sendpress'), __('Help','sendpress'), $role, 'sp-help', array(&$this,'render_view'));
	   
	   add_submenu_page('sp-overview', __('Pro','sendpress'), __('Pro','sendpress'), $role, 'sp-pro', array(&$this,'render_view'));
	   

	   	if(SendPress_Option::get('feedback') == 'yes'){
			$this->presstrends_plugin();
		}
	}

	function render_view(){
		
		$view_class = $this->get_view_class($this->_page, $this->_current_view);
		//echo "About to render: $view_class, $this->_page";
		$view_class = NEW $view_class;

		$queue = SendPress_Data::emails_in_queue();

		//add tabs
		$view_class->add_tab( __('Overview','sendpress'), 'sp-overview', ($this->_page === 'sp-overview') );
		$view_class->add_tab( __('Emails','sendpress'), 'sp-emails', ($this->_page === 'sp-emails') );
		$view_class->add_tab( __('Reports','sendpress'), 'sp-reports', ($this->_page === 'sp-reports') );
		$view_class->add_tab( __('Subscribers','sendpress'), 'sp-subscribers', ($this->_page === 'sp-subscribers') );
		$view_class->add_tab( __('Queue','sendpress') . ' <small>('. $queue .')</small>', 'sp-queue', ($this->_page === 'sp-queue') );
		$view_class->add_tab( __('Settings','sendpress'), 'sp-settings', ($this->_page === 'sp-settings') );
		
		$view_class->add_tab( __('Help','sendpress'), 'sp-help', ($this->_page === 'sp-help') );
		$view_class->add_tab( __('Pro','sendpress'), 'sp-pro', ($this->_page === 'sp-pro') );
		

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
	static function get_view_class( $main , $view = false){
		$page = explode('-', $main);
		$classname = '';
		foreach($page as $p){
			if($p != 'sp'){
				if($classname !== ''){
					$classname = '_';
				}
				$classname .= ucwords( $p );
				
			}
		}

		if($view !== false){
			$view = str_replace('-',' ',$view);
			$view  = ucwords( $view );
			$view = str_replace(' ','_',$view);
			$class = "SendPress_View_{$classname}_{$view}";

			if ( class_exists( $class ) )
				return $class;
		}

		$class = "SendPress_View_{$classname}";
		
		if ( class_exists( $class ) )
			return $class;
		
		return "SendPress_View";
	}

	
	
	function upgrade_lists_to_custom_postype(){
		$table = $this->lists_table();

		$list = $this->getData($table);

		//print_r($list);

		foreach($list as $newlist){
			// Create post object
			  $my_post = array(
			     'post_title' => $newlist->name,
			     'post_content' => '',
			     'post_status' => 'publish',
			     'post_type'=>'sendpress_list'
			  );

			// Insert the post into the database
	  		$new_id = wp_insert_post( $my_post );
	  		update_post_meta($new_id,'public',$newlist->public);
			update_post_meta($new_id,'last_send_date',$newlist->last_send_date);
			update_post_meta($new_id,'legacy_id',$newlist->listID);
			$this->upgrade_lists_new_id( $newlist->listID, $new_id);

		}

	}

	function upgrade_lists_new_id( $old , $new ){
		global $wpdb;

		$table = $this->list_subcribers_table();
		$values = array('listID'=> $new);
		$result = $wpdb->update($table,$values, array('listID'=> $old) );
		//return $result;
	}

	function maybe_upgrade() {

		$current_version = SendPress_Option::get('version', '0' );
		//$current_version = '0.9.3';
		//echo $current_version;
		//error_log($current_version);

		if ( version_compare( $current_version, SENDPRESS_VERSION, '==' ) )
			return;

		SendPress_Option::set('whatsnew',true);

		if(version_compare( $current_version, '0.6.2', '==' )){
			require_once(SENDPRESS_PATH . 'inc/db/status.table.php');
		}

		if(version_compare( $current_version, '0.6.5', '<' )){
			$this->update_option( 'install_date' , time() );
		}

		if(version_compare( $current_version, '0.7', '<' )){
			$this->update_option( 'sendmethod' , 'website' );
		}

		if(version_compare( $current_version, '0.7.5', '<' )){
			require_once(SENDPRESS_PATH . 'inc/db/open.click.table.php');
		}
		
		if(version_compare( $current_version, '0.8.1', '<' )){
			require_once(SENDPRESS_PATH . 'inc/db/alter-lists-subs.php');
		}
		if(version_compare( $current_version, '0.8.3', '<' )){
			$this->update_option( 'feedback' , 'no' );
		}
		if(version_compare( $current_version, '0.8.4', '<' )){
			require_once(SENDPRESS_PATH . 'inc/db/alter-list.php');
		}
		if(version_compare( $current_version, '0.8.6', '<' )){
			$widget_options =  array();

        	$widget_options['widget_options']['load_css'] = 0;
        	$widget_options['widget_options']['load_ajax'] = 0;
        	$widget_options['widget_options']['load_scripts_in_footer'] = 0;

        	$this->update_options($widget_options);   
		}
	
		if(version_compare( $current_version, '0.8.6.5', '<' )){
			$this->upgrade_lists_to_custom_postype();
		}

		if(version_compare( $current_version, '0.8.6.7', '<' )){
			require_once(SENDPRESS_PATH . 'inc/db/alter-tracking.php');
			SendPress_Option::set('sendpress_ignore_087', 'false');
		}
		
		if(version_compare( $current_version, '0.8.7.5', '<' )){
			SendPress_Data::set_double_optin_content();
		}


		if(version_compare( $current_version, '0.8.8', '<' )){
			$pro_plugins = array();
			$pro_plugins['pro_plugins']['setup_value'] = false;
			SendPress_Option::set($pro_plugins);
		}

		if(version_compare( $current_version, '0.9.3', '<' )){

			SendPress_Data::update_tables_093();
			
			$options = SendPress_Option::get('notification_options');

			$new_options = array(
		        	'email' => '',
		        	'name' => '',
		        	'notifications-enable' => false,
		        	'notifications-subscribed-instant' => false,
		        	'notifications-subscribed-daily' => false,
		        	'notifications-subscribed-weekly' => false,
		        	'notifications-subscribed-monthly' => false,
		        	'notifications-unsubscribed-instant' => false,
		        	'notifications-unsubscribed-daily' => false,
		        	'notifications-unsubscribed-weekly' => false,
		        	'notifications-unsubscribed-monthly' => false
		        );

			if($options === false || $options === ''){
				
		        SendPress_Option::set('notification_options', $new_options );
			} else if( is_array($options) ){
				$result = array_merge($options, $new_options);
				SendPress_Option::set('notification_options', $result );
			}
			
		}




		SendPress_Option::set( 'version' , SENDPRESS_VERSION );
	}	
	
	function set_template_default(){
		$default_style_post = SendPress_Data::get_template_id_by_slug('default-style');
		update_post_meta($default_style_post ,'body_bg', '#E8E8E8' );
		update_post_meta($default_style_post ,'body_text', '#231f20' );
		update_post_meta($default_style_post ,'body_link', '#21759B' );
		update_post_meta($default_style_post ,'header_bg', '#DDDDDD' );
		update_post_meta($default_style_post ,'header_text_color', '#333333' );

		update_post_meta($default_style_post ,'content_bg', '#FFFFFF' );
		update_post_meta($default_style_post ,'content_text', '#222222' );
		update_post_meta($default_style_post ,'sp_content_link_color', '#21759B' );
		update_post_meta($default_style_post ,'content_border', '#E3E3E3' );

		$optin = SendPress_Data::get_template_by_slug('double-optin');
		$update_optin = false;

		if($optin->post_content == ""){
			$optin->post_content = SendPress_Data::optin_content();
			$update_optin = true;
		}

        //clear the cached file.
    	if($optin->post_title == ""){
			$optin->post_title = SendPress_Data::optin_title();
			$update_optin = true;
		}

		if($update_optin == true){
			wp_update_post($optin);
		}

		delete_transient( 'sendpress_email_html_'. $optin->ID );
	}

	function wpdbQuery($query, $type) {
		global $wpdb;
		// eliminate warnings with debug mode
		if($type == 'prepare'){
			$result = $wpdb->$type( $query, array() );
		} else {
			$result = $wpdb->$type( $query );
		}
		return $result;
	}

	function wpdbQueryArray($query) {
		global $wpdb;
		$result = $wpdb->get_results( $query , ARRAY_N);
		return $result;
	}

	// GET DATA
	function getData($table) {
		$result = $this->wpdbQuery("SELECT * FROM $table", 'get_results');
		return $result;		
	}

	// GET DETAIL (RETURN X WHERE Y = Z)
	function getDetail($table, $entry, $value) {
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE $entry = '$value'", 'get_results');
		return $result;	
	}

	function getUrl($report, $url) {
		$table = $this->report_url_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$report' AND url = '$url'", 'get_results');
		return $result;	
	}


	
	function get_opens_unique_count($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT COUNT( DISTINCT subscriberID ) FROM $table WHERE reportID = '$rid' AND type = 'open';", 'get_var');
		return $result;
	}
	function get_opens_unique($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$rid' AND type = 'open' GROUP BY subscriberID ORDER BY eventID DESC; ", 'get_results');
		return $result;
	}
	function get_opens($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$rid' AND type = 'open'  ORDER BY eventID DESC;", 'get_results');
		return $result;
	}
	function get_opens_count($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT COUNT(1) as count FROM $table WHERE reportID = '$rid' AND type = 'open';", 'get_var');
		return $result;
	}
	function get_clicks_unique_count($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT COUNT( DISTINCT subscriberID )  FROM $table WHERE reportID = '$rid' AND type = 'click';", 'get_var');
		return $result;
	}
	function get_clicks_unique($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$rid' AND type = 'click' GROUP BY subscriberID ORDER BY eventID DESC;", 'get_results');
		return $result;
	}
	function get_clicks($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$rid' AND type = 'click'  ORDER BY eventID DESC;", 'get_results');
		return $result;
	}
	function get_clicks_count($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT COUNT(1) FROM $table WHERE reportID = '$rid' AND type = 'click';", 'get_var');
		return $result;
	}
	function get_clicks_and_opens($rid){
		$table = $this->subscriber_event_table();
		$result = $this->wpdbQuery("SELECT * FROM $table WHERE reportID = '$rid' ORDER BY eventID DESC;", 'get_results');
		return $result;
	}

	// GET DETAIL (RETURN X WHERE Y = Z)
	function deleteList($listID) {
		//$table = $this->lists_table();
		//$result = $this->wpdbQuery("DELETE FROM $table WHERE listID = '$listID'", 'query');
		wp_delete_post( $listID, true);
		$table = $this->list_subcribers_table();
		$result = $this->wpdbQuery("DELETE FROM $table WHERE listID = '$listID'", 'query');

		return $result;	
	}

	// GET DETAIL (RETURN X WHERE Y = Z)
	function delete_queue_email( $emailID ) {
		$table = $this->queue_table();
		$result = $this->wpdbQuery("DELETE FROM $table WHERE id = '$emailID'", 'query');

		return $result;	
	}

	// GET DETAIL (RETURN X WHERE Y = Z)
	function createList($values) {
		// Create post object
		  $my_post = array(
		     'post_title' => $values['name'],
		     'post_content' => '',
		     'post_status' => 'publish',
		     'post_type'=>'sendpress_list'
		  );

		// Insert the post into the database
  		$new_id = wp_insert_post( $my_post );
  		update_post_meta($new_id,'public',$values['public']);
		//add_post_meta($new_id,'last_send_date',$newlist->last_send_date);
		//add_post_meta($new_id,'legacy_id',$newlist->listID);
		//$this->upgrade_lists_new_id( $newlist->listID, $new_id);
		//	$table = $this->lists_table();

		
		//$result = $this->wpdbQuery("INSERT INTO $table (name, created, public) VALUES( '" .$values['name'] . "', '" . date('Y-m-d H:i:s') . "','" .$values['public'] . "')", 'query');

		return $new_id;	
	}

	function updateList($listID, $values){
		global $wpdb;

		//$table = $this->lists_table();

		//$result = $wpdb->update($table,$values, array('listID'=> $listID) );
		
		$my_post = array(
		    'post_title' => $values['name'],
		    'ID'=> $listID,     
		);

		// Insert the post into the database
  		$new_id = wp_update_post( $my_post );
  		update_post_meta($new_id,'public',$values['public']);

		return $new_id;
	}

	function requeue_email($emailid){
		global $wpdb;

		$table = SendPress_Data::queue_table();

		$result = $wpdb->update($table,array('attempts'=>0 ,'inprocess'=>0), array('id'=> $emailid) );
	
	}

	// GET DETAIL (RETURN X WHERE Y = Z)
	function unlink_list_subscriber($listID, $subscriberID) {
		$table = SendPress_Data::list_subcribers_table();
		$result = $this->wpdbQuery("DELETE FROM $table WHERE listID = '$listID' AND subscriberID = '$subscriberID' ", 'query');
		return $result;	
	}

	
	// COUNT DATA
	function countData($table) {
		$count = $this->wpdbQuery("SELECT COUNT(*) FROM $table", 'get_var');
		return $count;
	}

	// COUNT DATA
	function countSubscribers($listID, $status = 2) {
		global $wpdb;
		$table = SendPress_Data::list_subcribers_table();

		$query = "SELECT COUNT(*) FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3";

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND (t2.status = t3.statusid ) AND(t2.status = %d) AND (t2.listID =  %d)";
          //  "SELECT COUNT(*) FROM $table WHERE listID = $listID AND status = $status"
		$count = $this->wpdbQuery( $wpdb->prepare( $query, $status, $listID) , 'get_var');
		return $count;
	}

	// COUNT DATA
	function countQueue() {
		global $wpdb;
		$table = SendPress_Data::queue_table();
		$count = $this->wpdbQuery("SELECT COUNT(1) FROM $table WHERE success = 0 AND max_attempts != attempts", 'get_var');
		return $count;
	}

	function add_subscriber_with_optin(){
		$table = SendPress_Data::subscriber_table();
		$email = $values['email'];

		if(!isset($values['join_date'])){
			$values['join_date'] =  date('Y-m-d H:i:s');
		}
		if(!isset($values['identity_key'])){
			$values['identity_key'] =  SendPress_Data::random_code();
		}

		if( !filter_var($email, FILTER_VALIDATE_EMAIL) ){
			return false;
		}

		$result = $this->get_subscriber_by_email($email);
		if(	$result ){ return $result; }

		global $wpdb;
		$result = $wpdb->insert($table, $values);
		//$result = $this->wpdbQuery("SELECT @lastid2 := LAST_INSERT_ID()",'query');
		return $wpdb->insert_id;
	}

	

	function updateSubscriber($subscriberID, $values){
		$table = SendPress_Data::subscriber_table();
		$email = $values['email'];
		global $wpdb;
		$result = $this->wpdbQuery("SELECT subscriberID FROM $table WHERE email = '$email' ", 'get_var');
		if($result == false || $result == $subscriberID){ 

		$result = $wpdb->update($table,$values, array('subscriberID'=> $subscriberID) );
		//$result = $this->wpdbQuery("SELECT @lastid2 := LAST_INSERT_ID()",'query');
		 }
		//return $wpdb->insert_id;
	}

	

	function getSubscriberLists( $value ) {
		$table = SendPress_Data::list_subcribers_table();
		$result = $this->wpdbQueryArray("SELECT listID FROM $table WHERE subscriberID = '$value'", 'get_results');
		return $result;	
	}

	function getSubscriberListsStatus( $listID,$subscriberID ) {
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::get_subscriber_list_status($listID, $subscriberID)' );
		return SendPress_Data::get_subscriber_list_status($listID, $subscriberID);
	}

	function getSubscribers($listID = false){
		$query = "SELECT t1.*, t3.status FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID .")";
        
        return $this->wpdbQuery($query, 'get_results');
	}
	function get_active_subscribers($listID = false){
		$query = "SELECT t1.*, t3.status FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID .") AND (t2.status = 2)";
        
        return $this->wpdbQuery($query, 'get_results');
	}

	function getSubscriberbyKey($key){
		return $this->getDetail(SendPress_Data::subscriber_table(), 'identity_key', $key );
	}

	function getSubscriberKey($id){
		$subscriber = $this->getSubscriber( $id );
		if($subscriber){
			return $subscriber->identity_key;
		}
		return md5('testemailsentfromsendpress');
	}

	function exportList($listID = false){
		if($listID){
        $query = "SELECT t1.*, t3.status FROM " .  SendPress_Data::subscriber_table() ." as t1,". SendPress_Data::list_subcribers_table()." as t2,". SendPress_Data::subscriber_status_table()." as t3 " ;

        
            $query .= " WHERE (t1.subscriberID = t2.subscriberID) AND ( t3.statusid = t2.status ) AND (t2.listID =  ". $listID .")";
        } else {
            $query = "SELECT * FROM " .  SendPress_Data::subscriber_table();
        }

        return $this->wpdbQuery($query, 'get_results');
	}

	


	function the_content( $content ) {
		
		global $post;
		$optin = SendPress_Data::get_template_id_by_slug('double-optin');	
		if ( $post->post_type == 'sptemplates' && $post->ID == $optin ) {
			$content .= "";
		}
		
		return $content;
		
	}

	function create_initial_list(){

		//check if you have lists
		$lists = wp_count_posts('sendpress_list');		

		if( intval($lists->publish) === 0 ){
			//add in a fresh list
			$id = $this->createList( array('name'=> 'My First SendPress Newsletter', 'public'=>1 ) );
		}
		
	}
	


    /**
     * plugin_activation
     * 
     * @access public
     *
     * @return mixed Value.
     */
	function plugin_activation(){
		if ( version_compare( $GLOBALS['wp_version'], SENDPRESS_MINIMUM_WP_VERSION, '<' ) ) {
			deactivate_plugins( __FILE__ );
	    	wp_die( sprintf( __('SendPress requires WordPress version %s or later.', 'sendpress'), SENDPRESS_MINIMUM_WP_VERSION) );
		} else {
		    //if( SendPress_Option::get('version','0') == '0' ){
		    	SendPress_Option::set('sendpress_ignore_087', 'true');
		    	require_once(SENDPRESS_PATH .'inc/db/tables.php');
		    	require_once(SENDPRESS_PATH .'inc/db/status.table.php');
		    	require_once(SENDPRESS_PATH .'inc/db/open.click.table.php');
		    	SendPress_Option::set( 'version' , SENDPRESS_VERSION );

			//}	
		}
		//Make sure we stop the old action from running
		wp_clear_scheduled_hook('sendpress_cron_action_run');
		flush_rewrite_rules();
				 
		SendPress_Option::set( 'install_date' , time() );

	}

	/**
	*
	*	Nothing going on here yet
	*	@static
	*/
	function plugin_deactivation(){
		flush_rewrite_rules( );
		wp_clear_scheduled_hook( 'sendpress_cron_action' );
	} 

	

	

	function csv2array($input,$delimiter=',',$enclosure='"',$escape='\\'){ 
    	$fields=explode($enclosure.$delimiter.$enclosure,substr($input,1,-1)); 
    	foreach ($fields as $key=>$value) 
        	$fields[$key]=str_replace($escape.$enclosure,$enclosure,$value); 
    	return($fields); 
	} 

	function array2csv($input,$delimiter=',',$enclosure='"',$escape='\\'){ 
	    foreach ($input as $key=>$value) 
	        $input[$key]=str_replace($enclosure,$escape.$enclosure,$value); 
	    return $enclosure.implode($enclosure.$delimiter.$enclosure,$input).$enclosure; 
	} 

	/*
	*
	*	Creates an array from a posted textarea
	*	
	*	expects 3 fields or less: @sendpress.me, fname, lname
	*
	*/
	function subscriber_csv_post_to_array($csv, $delimiter = ',', $enclosure = '"', $escape = '\\', $terminator = "\n") { 
	    $r = array(); 
	    $rows = explode($terminator,trim($csv)); 
	    $names = array_shift($rows); 
	    $names = explode(',', $names);
		$nc = count($names);
 
	    foreach ($rows as $row) { 
	        if (trim($row)) { 
	        	$needle = substr_count($row, ',');
	        	if($needle == false){
	        		$row .=',,';
	        	} 
	        	if($needle == 1){
	        		$row .=',';
	        	} 

	            $values = explode(',' , $row);
	            if (!$values) $values = array_fill(0,$nc,null); 
	            $r[] = array_combine($names,$values); 
	        } 
	    } 
	    return $r; 
	} 

	

	

	/**
	 * Returns an array of all PHP files in the specified absolute path.
	 * Equivalent to glob( "$absolute_path/*.php" ).
	 *
	 * @param string $absolute_path The absolute path of the directory to search.
	 * @return array Array of absolute paths to the PHP files.
	 */
	function glob_php( $absolute_path ) {
		$absolute_path = untrailingslashit( $absolute_path );
		$files = array();
		if(is_dir($absolute_path)){
		if (!$dir = @opendir( $absolute_path ) ) {
			return $files;
		}
		
		while ( false !== $file = readdir( $dir ) ) {
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, -4 ) ) {
				continue;
			}

			$file2 = "$absolute_path/$file";

			if ( !is_file( $file2 ) ) {
				continue;
			}
			$basename = str_replace($absolute_path, '', $file);
			$files[] = array($file2, $basename);
		}

		closedir( $dir );
		}

		return $files;
	}

	

	function get_lists(){
		return	$this->getData( $this->lists_table() );
	}

	function get_list_details($id){
		return get_post( $id  );
	}


	function email_template_dropdown( $default = '' ) {
		$templates = SendPress_Template::get_instance()->info();
		ksort( $templates );
		foreach ( $templates as $key => $template )
			: if ( $default == $key )
				$selected = " selected='selected'";
			else
				$selected = '';
				
		echo "\n\t<option value='".$key."' $selected>".$template['name'] ."</option>";
		endforeach;
	}

	/*
		Funtion to be removed.	
	*/
   


	

	function unique_message_id() {
		if ( isset($_SERVER['SERVER_NAME'] ) ) {
	      	$servername = $_SERVER['SERVER_NAME'];
	    } else {
	      	$servername = 'localhost.localdomain';
	    }
	    $uniq_id = md5(uniqid(time()));
	    $result = sprintf('%s@%s', $uniq_id, $servername);
	    return $result;
	}

	

	function cron_stop(){
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'].'/sendpress.pause';
		if (file_exists($filename)) {
			return true;
		}
		return false;
	}

	function cron_start(){
		$upload_dir = wp_upload_dir();
		$filename = $upload_dir['basedir'].'/sendpress.pause';
		if (file_exists($filename)) {
			unlink($filename);
		} 
	}

	function fetch_mail_from_queue(){
		@ini_set('max_execution_time',0);
		global $wpdb;
		$count = SendPress_Option::get('emails-per-hour');
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		if($emails_per_hour != 0){
			$rate = 3600 / $emails_per_hour;
		}
		if($rate > 8){
			$rate = 8;
		}
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;
		$attempts = 0;
		if( SendPress_Manager::send_limit_reached()  ){
			return;
		}


		for ($i=0; $i < $count ; $i++) { 
			if($this->cron_stop() == false ){
				$email = $this->wpdbQuery("SELECT * FROM ". SendPress_Data::queue_table() ." WHERE success = 0 AND max_attempts != attempts AND inprocess = 0 ORDER BY id LIMIT 1","get_row");
				if($email != null){

					if( SendPress_Manager::send_limit_reached() ){
						break;
					}
					SendPress_Manager::increase_email_count( 1 );
					$attempts++;
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email );
					$email_count++;
					if ($result) {
						$table = SendPress_Data::queue_table();
						$wpdb->query( 
							$wpdb->prepare( 
								"DELETE FROM $table WHERE id = %d",
							    $email->id  
						    )
						);
						$senddata = array(
							'sendat' => date('Y-m-d H:i:s'),
							'reportID' => $email->emailID,
							'subscriberID' => $email->subscriberID
						);

						//$wpdb->insert( $this->subscriber_open_table(),  $senddata);
						SendPress_Data::update_report_sent_count( $email->emailID );
					} else {
						$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
					}
				} else{//We ran out of emails to process.
					break;
				}
			} else{ //Stop was set.
				break;
			}
			
		}


		
		

		
	}

	function send_single_from_queue(){
		
		global $wpdb;
		$limit =  1; //wp_rand(1, 10);
		$emails_per_hour = SendPress_Option::get('emails-per-hour');
		if($emails_per_hour != 0){
			$rate = 3600 / $emails_per_hour;
		}
		if($rate > 8){
			$rate = 8;
		}
		$emails_today =  SendPress_Option::get('emails-today');
		$emails_per_day = SendPress_Option::get('emails-per-day');
		$email_count = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0 ;

		//$emails = $this->wpdbQuery("SELECT * FROM ".$this->queue_table()." WHERE success = 0 AND max_attempts != attempts LIMIT ".$limit,"get_results");
		$count = 0;
		$attempts = 0;

		if( SendPress_Manager::send_limit_reached()  ){
			return array('attempted'=> $attempts,'sent'=>$count);
		}

		for ($i=0; $i < $limit ; $i++) { 
				$email = $this->wpdbQuery("SELECT * FROM ".SendPress_Data::queue_table()." WHERE (success = 0) AND (max_attempts != attempts) AND (inprocess = 0) ORDER BY id ASC LIMIT 1","get_results");
				if( !empty($email) ){
					$email = $email[0];
					

					if( SendPress_Manager::send_limit_reached() ){
						return array('attempted'=> $attempts,'sent'=>$count);
					}

					$attempts++;
					SendPress_Data::queue_email_process( $email->id );
					$result = SendPress_Manager::send_email_from_queue( $email );
					$email_count++;
					
					if ($result) {
						$table = SendPress_Data::queue_table();
						$wpdb->query( 
							$wpdb->prepare( 
								"DELETE FROM $table WHERE id = %d",
							    $email->id  
						    )
						);
						$senddata = array(
							'sendat' => date('Y-m-d H:i:s'),
							'reportID' => $email->emailID,
							'subscriberID' => $email->subscriberID
						);

						//$wpdb->insert( $this->subscriber_open_table(),  $senddata);
						$count++;
						SendPress_Data::update_report_sent_count( $email->emailID );
					} else {
						$wpdb->update( SendPress_Data::queue_table() , array('attempts'=>$email->attempts+1,'inprocess'=>0,'last_attempt'=> date('Y-m-d H:i:s') ) , array('id'=> $email->id ));
					}
				} else{//We ran out of emails to process.
					break;
				}
				
		}

		SendPress_Manager::increase_email_count( $attempts );
		return array('attempted'=> $attempts,'sent'=>$count);
	}



	function add_email_to_queue($values){
		global $wpdb;
		$table = SendPress_Data::queue_table();
		$messageid = $this->unique_message_id();
		$values["messageID"] = $messageid;
		$values["date_published"] = date('Y-m-d H:i:s');
		//$q = $wpdb->prepare("INSERT INTO $table (subscriberID, from_name,from_email,to_email, subject, messageID, date_published, emailID, listID) VALUES( '" .$values['subscriberID'] . "','" .$values['from_name'] . "', '" .$values['from_email'] .  "', '" .$values['to_email'] . "', '" .$values['subject'] . "', '" .$messageid . "', '". date('Y-m-d H:i:s') . "', '" .$values['emailID']. "', '" .$values['listID'] ."' )");
		//error_log($q);
		//$result = $this->wpdbQuery($q, 'query');

		$wpdb->insert( $table, $values);
	}

	
	function set_default_email_style( $id ){
		SendPress_Email::set_default_style( $id ); 
	}

	function register_open( $subscriberKey, $report ){
		global $wpdb;
		$stat = get_post_meta($report, '_open_count', true );
		$stat++;
		update_post_meta($report, '_open_count', $stat );
		$subscriber = $this->getSubscriberbyKey($subscriberKey);
		if( isset($subscriber[0]) ) {
			$wpdb->update( $this->subscriber_open_table() , array('openat'=>date('Y-m-d H:i:s') ) , array('reportID'=> $report,'subscriberID'=>$subscriber[0]->subscriberID ));
		}
	}

	function register_unsubscribe($subscriber_key, $report_id, $list_id){
		global $wpdb;
		$stat = get_post_meta($report_id, '_unsubscribe_count', true );
		$stat++;
		update_post_meta($report_id, '_unsubscribe_count', $stat );
		$subscriber = $this->getSubscriberbyKey($subscriber_key);
		if( isset($subscriber[0]) ) {
			$wpdb->update( $this->list_subcribers_table() , array('status'=> 3) , array('listID'=> $list_id,'subscriberID'=>$subscriber[0]->subscriberID ));
		}
	}

	function register_unsubscribed( $sid, $rid, $lid ) {
		global $wpdb;

		$stat = get_post_meta($rid, '_unsubscribe_count', true );
		$stat++;
		update_post_meta($rid, '_unsubscribe_count', $stat );
		$wpdb->update( $this->list_subcribers_table() , array('status'=> 3) , array('listID'=> $lid,'subscriberID'=>$sid ));
	}

	
	
	
	

	function get_ip_info($ip){
		if($ip != false && false == get_transient('sp-'.$ip) ){
			$geo = wp_remote_get('http://api.hostip.info/get_json.php?ip='.$ip.'&position=true');
			//set_transient('sp-'.$ip, $geo['body'], 60*60*24*7);
		} else {
			return get_transient('sp-'.$ip);
		}
		return $geo['body'];
	}



	function register_click($subscriberKey, $report, $url){
		global $wpdb;
		$stat = get_post_meta($report, '_click_count', true );
		$stat++;
		update_post_meta($report, '_click_count', $stat );

		$urlinDB = $this->getUrl($report, $url);
		$subscriber = $this->getSubscriberbyKey($subscriberKey);

		if(!isset($urlinDB[0])){
			$urlData = array(
				'url' => trim($url),
				'reportID' => $report,
			);
			$wpdb->insert( $this->report_url_table(),  $urlData);
			$urlID = $wpdb->insert_id;

		} else {
			$urlID  = $urlinDB[0]->urlID;
		}

		if(isset($subscriber[0]) && isset($urlID) ){
			$clickData = array(
				'urlID' => $urlID,
				'reportID' => $report,
				'subscriberID'=> $subscriber[0]->subscriberID,
				'clickedat'=>date('Y-m-d H:i:s')
			);
			$result = $wpdb->insert($this->subscriber_click_table() ,$clickData);
		}
	}

	/*
	*
	*	FUNCTIONS TO BE REMOVED PLEASE DO NOT USE
	* 
	*/
	function get_templates(){
		_deprecated_function( __FUNCTION__, '0.8.7', 'SendPress_Template::get_instance()->info()' );
		return SendPress_Template::get_instance()->info();
	}

	function get_key(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::get_key()' );
		return SendPress_Data::get_key();
	}

	function encrypt_data( $message ) {
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::encrypt()' );
		return SendPress_Data::encrypt($message);		
	}

	function decrypt_data($message) {
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::decrypt()' );
		return SendPress_Data::decrypt($message);
	}

	function sp_mail_it( $queue_row ) {
    	_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Manager::send_email_from_queue()' );
		SendPress_Manager::send_email_from_queue($queue_row);
	}

	function send_email($to, $subject, $html, $text, $istest = false ){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Sender_(type)::send_email()' );
		return SendPress_Manager::old_send_email($to, $subject, $html, $text, $istest );
	}

	function subscriber_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscriber_table()' );
		return SendPress_Data::subscriber_table();
	}

	function list_subcribers_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::list_subcribers_table()' );
		return SendPress_Data::list_subcribers_table();
	}

	function lists_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::lists_table()' );
		return SendPress_Data::lists_table();
	}

	function subscriber_status_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscriber_status_table()' );
		return SendPress_Data::subscriber_status_table();
	}

	function subscriber_event_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscriber_event_table()' );
		return SendPress_Data::subscriber_event_table();
	}

	function subscriber_click_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscriber_click_table()' );
		return SendPress_Data::subscriber_click_table();
	}

	function subscriber_open_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscriber_open_table()' );
		return SendPress_Data::subscriber_open_table();
	}
	
	function report_url_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::report_url_table()' );
		return SendPress_Data::report_url_table();
	}
	
	function queue_table(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::queue_table()' );
		return SendPress_Data::queue_table();
	}

	function random_code() {
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::random_code()' );
	    return SendPress_Data::random_code();
	}

	function send_optin($subscriberID, $listids, $lists){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Manager::send_optin()' );
		SendPress_Manager::send_optin($subscriberID, $listids, $lists);	
	}

	function subscribe_user($listid, $email, $first, $last){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::subscribe_user()' );
		SendPress_Data::subscribe_user($listid, $email, $first, $last);
	}

	function get_option( $name, $default = false ) {
		_deprecated_function( __FUNCTION__, '0.8.7', 'SendPress_Option::get()' );
		return SendPress_Option::get( $name, $default);
	}	
	
	function update_option( $name, $value ) {
		_deprecated_function( __FUNCTION__, '0.8.7', 'SendPress_Option::set()' );
		return SendPress_Option::set( $name, $value  );
	}
	
	function update_options( $array ) {
		_deprecated_function( __FUNCTION__, '0.8.7', 'SendPress_Option::set()' );
		return  SendPress_Option::set( $array );
	}

	function is_double_optin(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Option::is_double_optin()' );
		return SendPress_Option::is_double_optin();
	}

	function get_subscriber_by_email( $email ){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::get_subscriber_by_email()' );
		return SendPress_Data::get_subscriber_by_email( $email );
	}

	function addSubscriber($values){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::add_subscriber()' );
		return SendPress_Data::add_subscriber( $values );
	}

	function updateStatus($listID,$subscriberID,$status){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::update_subscriber_status()' );
		return SendPress_Data::update_subscriber_status($listID, $subscriberID , $status);
	}

	function getSubscriber($subscriberID, $listID = false){
		_deprecated_function( __FUNCTION__, '0.8.7', 'SendPress_Data::get_subscriber($subscriberID, $listID)' );
		return SendPress_Data::get_subscriber($subscriberID, $listID);
	}

	function linkListSubscriber($listID, $subscriberID, $status = 0) {
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Data::update_subscriber_status()' );
		return SendPress_Data::update_subscriber_status($listID, $subscriberID, $status );	
	}

	function send_test(){
		_deprecated_function( __FUNCTION__, '0.8.9', 'SendPress_Manager::send_test()' );
		SendPress_Manager::send_test();
	}
	/*
	*
	*	END FUNCTIONS TO BE REMOVED PLEASE DO NOT USE
	* 
	*/

}// End SP CLASS

add_filter( 'query_vars', array( 'SendPress', 'add_vars' ) );
			
	
register_activation_hook( __FILE__, array( 'SendPress', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'SendPress', 'plugin_deactivation' ) );

// Initialize!
SendPress::get_instance();


