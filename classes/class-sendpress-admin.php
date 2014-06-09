<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Admin')){


class SendPress_Admin {

	static $_admin_cap = 'manage_options';
	static $_cap = array();
		static $_primary = array();

	static function link( $classname = false, $params = array()){
		if($classname == false){
			return admin_url( 'admin.php?page=sp-overview');
		}
		$parts = explode("_", $classname);
		$l = "?page=sp-".$parts[0];
		if(isset( $parts[1])){
			$l .= "&view=".$parts[1];
		}
		if(isset($parts[2])){
			$l .= "-".$parts[2];
		}
		if(isset($parts[3])){
			$l .= "-".$parts[3];
		}
		$l = strtolower($l);
		if(!empty($params) && (is_array($params) || is_object($params)) ){
			$params = http_build_query($params, '', '&');
			if(strlen($params) > 0 ){
				$params = '&'. $params;
			}
		} else {
			$params = '';
		}

		return  admin_url( 'admin.php'. $l . $params );
	}


	static function redirect( $classname =false , $params = array() ){

		$url = self::link(  $classname , $params );
		if ( headers_sent() ) {
			echo "<script>document.location.href='".$url."';</script>";
		}
		else {
			wp_redirect(  $url );
		}
		exit;
	}



	static public function access($class = null){
		if(is_object($class) ){
			$class = get_class($class);
		} else {
			$class = strtolower( 'sendpress_view_'.$class );
		}
		/*
		$x = wp_get_current_user();
		echo "<pre>";
		print_r($x);
		echo "</pre>";
		*/
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

	static public function add_cap( $classname = false , $cap = 'manage_options' ){
		if($classname !== false){
			self::$_primary[ strtolower( 'sendpress_view_'.$classname ) ] = strtolower( $cap );
		}
	}


	static public function view_cap($current_class = null){
		$current_class = strtolower( $current_class );

		//echo $current_class;
		//Check for set permissions
		if(isset(self::$_primary[$current_class]))
			return self::$_primary[$current_class];

		$p = self::getParents( $current_class );
		//Check Parent class for limit

		foreach ($p as $c) {
			if(isset(self::$_primary[ strtolower($c) ]))
				return  self::$_primary[strtolower($c) ];
		}
		//Return Admin view Cap
		return self::$_admin_cap;
	}



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


}

}
