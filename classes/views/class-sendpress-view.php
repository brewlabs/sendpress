<?php
// SendPress Required Class: SendPress_View
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
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
	}
	

    /**
     * redirect redirects to the view called on.
     * 
     * @param array $params option query string parameters array('update'=>'true').
     *
     * @access public
     * @static
     *
     * @return mixed Value.
     */
	static function redirect( $params = array() ){
		$url = self::link( $params );
		if (headers_sent()) {
		echo "<script>document.location.href='".$url."';</script>"; }
		else {
		wp_redirect(  $url ); 
		}
		exit;
	}

    /**
     * link
     * 
     * @param array $params query string parameters array('update'=>'true').
     *
     * @access public
     * @static
     *
     * @return mixed Url to view.
     */
	static function link( $params = array() ){
		$x = get_called_class();
		$parts = explode("_", $x);
		$l = "?page=sp-".$parts[2];
		if(isset( $parts[3])){
			$l .= "&view=".$parts[3];
		}
		if(isset($parts[4])){
			$l .= "-".$parts[4];
		}
		$l = strtolower($l);
		$params = http_build_query($params, '', '&');
		if(strlen($params) > 0 ){
			$params .='&'. $params;
		}

		return  admin_url( 'admin.php'. $l . $params );

		//return ;

	}

	/**
	 * Initializes the view.
	 */

    
	function init() {}

	function page_start(){
		echo '<div class="wrap">';
		$this->tabs();
		echo '<div class="spwrap">';
	}

	function page_end(){
		echo '</div>';
		echo '</div>';
		//echo '<div class="clear"></div>';
	}

	static public function view_cap($current_class = null){
		if ( ! isset( $current_class ) ){
			$current_class = get_called_class();
		}
		//Check for set permissions
		if(isset(self::$_primary[$current_class]))
			return self::$_primary[$current_class];
		
		$p = self::getParents( $current_class );
		//Check Parent class for limit

		foreach ($p as $c) {
			if(isset(self::$_primary[$c]))
				return  self::$_primary[$c];
		}
		//Return Admin view Cap
		return self::$_admin_cap;
	}

	

	static public function access($class = null){
		if(is_object($class) ){
			$class = get_class($class);
		}
		
		//Admin
		if( current_user_can(self::$_admin_cap) || is_super_admin() || current_user_can('delete_users') ){
			return true;
		}
		
		//View Specific
		if( current_user_can( self::view_cap($class) ) ){
			return true;	
		}
		
		//You can't see me
		return false;
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
		if( self::access( $this ) ){
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

	/*
	* Page HTML
	*/

	public function getParents($class=null, $plist=array()) {
	    $class = $class ? $class : $this;
	    $parent = get_parent_class($class);
	    if($parent) {
	      $plist[] = $parent;
	      /*Do not use $this. Use 'self' here instead, or you
	       * will get an infinite loop. */
	      $plist = self::getParents($parent, $plist);
	    }
	    return $plist;
	  }

	function html($sp){
		echo "Page not built yet.";
	}
	function noaccess($sp){
		echo "<div class='well well-large'><h3>Sorry. You dont have the ability to view this page.</h3></div>";
	}
	//static  public function save(){}

	static public function n() {
        return get_called_class(); 
    }

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

	static public function cap( $cap=NULL ) {
		if ( ! isset( $cap ) )
			return self::$_primary;

		$class = get_called_class();
		self::$_primary[ $class ] = $cap;
		
	}

}

do_action('sendpress_view_class_loaded');



