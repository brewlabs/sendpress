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
			<a href="<?php echo SendPress_Admin::link('Help_Whatsnew'); ?>">What's New</a> | <a href="http://sendpress.com/support/knowledgebase/" target="_blank">Knowledge Base</a> | <a href="http://sendpress.uservoice.com/" target="_blank">Feedback</a> | SendPress Version: <?php echo SENDPRESS_VERSION; ?> 
		</div>
		<?php
	}

	function view_buttons(){
		?>
		<button class="btn btn-default" id="save-menu-cancel">Cancel</button><button class="btn btn-primary" id="save-menu-post">Save</button>
		<?php
	}


	function panel_start($title = false, $body=true){ ?>
		<div class="panel panel-default">
			<?php if($title !== false ){ ?>
		  <div class="panel-heading">
				<h3 class="panel-title"><?php echo $title; ?></h3>
		  </div>
		  <?php } 
		  	if($body == true){
		  ?>

		<div class="panel-body">
		<?php }
	}

	
	function panel_end($footer = false , $body = true){ 
		if($body == true){
			?>
		</div>
		<?php 
		}
		if($footer !== false) { ?>
		<div class="panel-footer"><?php echo $footer; ?></div>
		<?php } ?>
		</div>
	<?php
	}


	function admin_init(){
		
	}


	

	/**
	 * Initializes the view.
	 */

    
	function init() {
		
	}

	function page_start(){
		echo '<div class="wrap">';
		$this->tabs();
		echo '<div class="spwrap">';
	}

	function page_end(){ 
		?>
		</div>
		</div>
		
		<?php

		//delete_transient( 'current_sp_pro_version' );

		//call api to get current version of pro if pro installed
		if( defined('SENDPRESS_PRO_VERSION')){
			if(SendPress_Pro_Manager::get_pro_state() !== 'valid'){
				if ( false === ( $current_sp_pro_version = get_transient( 'current_sp_pro_version' ) ) ) {
				    // It wasn't there, so regenerate the data and save the transient
				    $remote = wp_remote_get( 'http://api.sendpress.com/pro/getversion' );
				    $current_sp_pro_version = json_decode(wp_remote_retrieve_body( $remote ));
				  	if( is_array( $current_sp_pro_version ) ){
				  		$current_sp_pro_version = $current_sp_pro_version[0]->version;


				  	} else {
				  		$current_sp_pro_version = 100;
				  	}
				  	set_transient( 'current_sp_pro_version', $current_sp_pro_version, 24 * HOUR_IN_SECONDS );
				 
				}

				if( $current_sp_pro_version > SENDPRESS_PRO_VERSION ){
					?><br>
					<div style="padding: 0 15px 0 0">
					<div class="sp-error sp-clear-top"><p><strong>SendPress Pro is out of date!</strong>&nbsp;&nbsp;Upgrade to get the latest updates, features, and bug fixes.  If your key has expired visit <a href="https://sendpress.com" target="_blank">SendPress.com</a> to renew.</p></div>
					</div>
					<?php
				}
			}
		}
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

	function admin_scripts_load(){}

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
		echo "Page not built yet.";
	}
	function noaccess($sp){
		echo "<div class='well well-large'><h3>Sorry. You dont have the ability to view this page.</h3></div>";
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

	function select($id='sp-select', $value = 25 , $values=array(25,50,100,250,500,1000) ){
		echo " <select id='$id' name='$id' >";
		foreach ($values as $item) {
			$active = '';
			if($value == $item ){
				$active = 'selected';
			}
			echo "<option value='$item' $active> $item</option>";
			# code...
		}
		echo "</select>";

	}


}
do_action('sendpress_view_class_loaded');



