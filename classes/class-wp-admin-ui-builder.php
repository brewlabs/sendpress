<?php
// SendPress Required Class: WP_Admin_UI_Builder

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class WP_Admin_UI_Builder{
	private $wrap_class;
	private $tabs;

	function __construct($class, $tabs) {
		$this->wrap_class = strtolower($class);
		$this->tabs = $tabs;

		$this->menu_page($this->tabs['main_page']);		

	}

	function menu_page($page){
		add_menu_page(__($page['name'],$this->wrap_class), __($page['name'],$this->wrap_class), $page['access'],$page['id'],  array(&$this,'page_dashboard') , $page['icon']);
	}

	function sub_menu_page($pageid, $page){
		add_submenu_page($pageid, __($page['name'],$this->wrap_class), __($page['name'],$this->wrap_class), $page['access'], $pageid, array(&$this,'page_dashboard'));
	}

	function add_child_tab(){

	}

	function add_tab($title, $link, $make_active = false){
		$class = '';
		if( $make_active ){
			$class = ' nav-tab-active';
		}

		return '<a class="nav-tab'.$class.'" href="'.$link.'">'.$title.'</a>';
	}

	function page_start($page){
		echo '<div class="wrap">';
		$this->tabs($page);
		echo '<div class="stripe-wrap">';
	}

	function page_end(){
		echo '</div>';
		echo '</div>';
	}	
	

}