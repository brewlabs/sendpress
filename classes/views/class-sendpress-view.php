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


	function __construct( $title='' ) {
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
			<a href="<?php echo SendPress_Admin::link('Help_Whatsnew'); ?>"><?php _e("What's New","sendpress"); ?></a> | <a href="http://docs.sendpress.com/" target="_blank"><?php _e("Knowledge Base","sendpress"); ?></a> | <a href="http://sendpress.uservoice.com/" target="_blank"><?php _e("Feedback","sendpress"); ?></a> | <?php _e("SendPress Version","sendpress"); ?>: <?php echo SENDPRESS_VERSION; ?> | <?php _e("System","sendpress"); ?> <span id="sendpress-system-icon" class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>
		</div>
		<?php
			$url = home_url( '/' );
		?>	
		<script>
			jQuery(function() {
				jQuery.getJSON( "<?php echo $url ; ?>spnl-api/system-check", function( data ) {
					if(data.status){
						if(data.status === "active"){
							jQuery("#sendpress-system-icon").addClass("glyphicon-ok-sign").removeClass("glyphicon-remove-sign").css("color","#5cb85c");
						} else {
							jQuery("#sendpress-system-icon").removeClass("glyphicon-ok-sign").addClass("glyphicon-remove-sign").css("color","#d9534f");
						}
					}
				});
			});
		</script>
		<!-- src/templates/metabox.templ.php  -->
 
<!-- Template -->
<script  id="tmpl-my-awesome-template" type="text/template">
 <div class="spnl-modal">
 	test info here....
 </div>
</script>
<!-- End template -->
			<?php


	}

	function view_buttons(){
		?>
		<button class="btn btn-default" id="save-menu-cancel"><?php _e("Cancel","sendpress"); ?></button><button class="btn btn-primary" id="save-menu-post"><?php _e("Save","sendpress"); ?></button>
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


	static function let_to_num( $v ) {
		$l   = substr( $v, -1 );
		$ret = substr( $v, 0, -1 );

		switch ( strtoupper( $l ) ) {
			case 'P': // fall-through
			case 'T': // fall-through
			case 'G': // fall-through
			case 'M': // fall-through
			case 'K': // fall-through
				$ret *= 1024;
				break;
			default:
				break;
		}

		return $ret;
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
		$user = wp_get_current_user();
		
	}

	function page_end(){ 
		?>
		</div>
		</div>
		
		<?php

		
		//delete_transient( 'current_sp_pro_version' );

		//call api to get current version of pro if pro installed
		/*
		if( defined('SENDPRESS_PRO_VERSION')){
			if(SendPress_Pro_Manager::get_pro_state() !== 'valid'){
				if ( false === ( $current_sp_pro_version = get_transient( 'current_sp_pro_version' ) ) ) {
				    // It wasn't there, so regenerate the data and save the transient
				    $remote = wp_remote_get( 'http://api.sendpress.com/pro/getversion' );
				    $current_sp_pro_version = json_decode(wp_remote_retrieve_body( $remote ));
				   
				  	if( is_array( $current_sp_pro_version ) ){
				  		$current_sp_pro_version = $current_sp_pro_version[0]->version;


				  	} else {
				  		$current_sp_pro_version = 0;
				  	}
				  	set_transient( 'current_sp_pro_version', $current_sp_pro_version, 86400 );
				 
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
		*/
	}


	//Might not need anymore?
	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}


	function prerender( )  {}

	function security_check(){
		if( ! SendPress_Admin::access( $this ) ){
			die('You are not allowed to perform this action');
		}
	}

	/**
	 * Renders the view.
	 */
	function render() {
		
		$this->page_start();
		$this->sub_menu();
		if( SendPress_Admin::access( $this ) ){
			$this->html();
		} else {
			$this->noaccess();
		}
		$this->page_end();
	}

	function sub_menu(){}

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

	

	function html(){
		echo "Page not built yet.";
	}
	function noaccess(){
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
			
			if(is_array($item)){
				if($value == $item[0] ){
				$active = 'selected';
			}
				echo "<option value='$item[0]' $active> $item[1]</option>";
			} else {
				if($value == $item ){
				$active = 'selected';
			}
			echo "<option value='$item' $active> $item</option>";
			}
			# code...
		}
		echo "</select>";

	}


}
do_action('sendpress_view_class_loaded');



