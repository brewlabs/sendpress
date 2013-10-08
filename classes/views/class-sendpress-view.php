<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

// Plugin paths, for including files
if ( ! defined( 'SENDPRESS_CLASSES' ) )
	define( 'SENDPRESS_CLASSES', plugin_dir_path( __FILE__ ) );
define( 'SENDPRESS_CLASSES_VIEWS', trailingslashit( SENDPRESS_CLASSES . 'views' ) );


// Field classes
class SendPress_View {
	var $_title = '';
	var $_visible = true;
	var $_tabs = "";
	var $_nonce_value = 'sendpress-is-awesome';
	var $_name = "SendPress_View";
	static $_admin_cap = 'manage_options';
	static $_cap = array();
	static $instance;
	static $_primary = array();


	function SendPress_View( $title='' ) {
		$this->title( $title );
		if ( $this->init() === false ) {
			$this->set_visible( false );
			return;
		}
		add_action('in_admin_footer',array('SendPress_View','footer'));

	}
	
	static function footer(){ 
		
		?>
		<div class="sp-footer">
			<a href="<?php echo SendPress_Admin::link('Help_Whatsnew'); ?>"><?php _e('What&rsquo;s New' , 'sendpress'); ?></a> | <a href="http://sendpress.com/support/knowledgebase/" target="_blank"><?php _e('Knowledge Base' , 'sendpress'); ?></a> | <a href="http://sendpress.uservoice.com/" target="_blank"><?php _e('Feedback' , 'sendpress'); ?></a> | <?php _e('SendPress Version:' , 'sendpress'); ?> <?php echo SENDPRESS_VERSION; ?> 
		</div>
		<?php
	}

	function admin_init(){}


	

	/**
	 * Initializes the view.
	 */

    
	function init() {
		//Remove this only on get methods
		if ( !empty($_REQUEST['_wp_http_referer']) && !$_SERVER['REQUEST_METHOD'] === 'POST') {
	 		wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI']) ) );
	 		exit;
		}
	}

	function page_start(){
		echo '<div class="wrap">';
		$this->tabs();
		echo '<div class="spwrap">';
	}

	function page_end(){ ?>
		
		<?php
		echo '</div>';
		?>


		<?php
		echo '</div>'; ?>

		<?php
		//echo '<div class="clear"></div>';
	}


	//Might not need anymore?
	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}


	function prerender( $sp = false )  {}

	/**
	 * Renders the view.
	 */
	function render($sp = false) {
		$this->page_start();
		$this->sub_menu($sp);
		if( SendPress_Admin::access( $this ) ){
			$this->html($sp);
		} else {
			$this->noaccess($sp);
		}
		$this->page_end();
	}

	function sub_menu($sp = false){}

	function tabs(){
		?>

		<div class="nav-sp">
			<div class="sp-icons icon32"><br /></div>
			<h2 class="nav-tab-wrapper"><?php echo $this->_tabs; ?></h2>
		</div>

		<?php
	
		do_action('sendpress_notices');
	}

	function add_tab($title, $link, $make_active = false){
		$class = '';
		if( $make_active ){
			$class = ' nav-tab-active';
		}

		$this->_tabs .= '<a class="nav-tab'.$class.'" href="?page='.$link.'">'.$title.'</a>';
	}

	

	function html($sp){
		_e('Page not built yet.', 'sendpress');
	}
	function noaccess($sp){
		echo "<div class='well well-large'><h3>" . __('Sorry. You dont have the ability to view this page.', 'sendpress') . "</h3></div>";
	}
	//static  public function save(){}


	function is_visible() {
		return $this->_visible;
	}

	function set_visible( $visible ) {
		$this->_visible = $visible;
	}

	function title( $title=NULL ) {
		if ( ! isset( $title ) )
			return $this->_title;
		$this->_title = $title;
	}

	


}
do_action('sendpress_view_class_loaded');



