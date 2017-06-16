<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

// Plugin paths, for including files
if ( ! defined( 'SENDPRESS_CLASSES' ) )
	define( 'SENDPRESS_CLASSES', plugin_dir_path( __FILE__ ) );

define( 'SENDPRESS_CLASSES_MODULES', trailingslashit( SENDPRESS_CLASSES . 'modules' ) );

class SendPress_Module {
	var $_title = '';
	var $_visible = true;
	var $_nonce_value = 'sendpress-is-awesome';
	var $_index = 0;
	var $_pro_version = 0;
	//var $_active = false;


	/**
	 * Initializes the view.
	 */
	function init() {}

	function module_start(){
		$class ='';
		if($this->index() % 3 == 1){
			$class= " mod-first";
		}

		echo '<div class="sendpress-module '. $class .'">';
		echo '<div class="inner-module">';
	}

	function module_end(){
		echo '</div>';
		echo '</div>';
	}

	function prerender() {}

	/**
	 * Renders the view.
	 */
	function render() {
		$this->module_start();
		$this->html();
		//$this->buttons('sendpress-pro/sendpress-pro.php');
		$this->module_end();
	}

	/*
	* Page HTML
	*/
	function html(){
		echo "Page not built yet.";
	}

	function buttons($plugin_path){
			
		switch( $this->pro_plugin_state() ){
			case 'installable':
			case 'not-installed':
			case 'installed':
				$button = array(
					'class' => 'btn disabled btn-default btn-activate', 
					'href' => '#',
					'target' => '', 'text' => __('Activate','sendpress')
				);
				$btn = $this->build_button($button);
				break;
			default:
				$button = array(
					'class' => 'module-activate-plugin btn-success btn-activate btn', 
					'href' => '#',
					'target' => '', 'text' => __('Activate','sendpress')
				);
				//pro is active, check the option to see what the deal is
				$pro_options = SendPress_Option::get('pro_plugins');

				if( !empty($pro_options) ){

					if( !array_key_exists($plugin_path, $pro_options)){
						$pro_options[$plugin_path] = false;
						SendPress_Option::set('pro_plugins',$pro_options);
						$pro_options = SendPress_Option::get('pro_plugins');
					}
					if( $pro_options[$plugin_path] ){
						$button['class'] = 'btn btn-default module-deactivate-plugin';
						$button['text'] = __('Deactivate','sendpress');

					}

				}
				
				$btn = $this->build_button($button);
				break;
		}
		if( !defined('SENDPRESS_PRO_VERSION') || version_compare( SENDPRESS_PRO_VERSION, $this->_pro_version, '>=' )){
				echo '<div class="inline-buttons">'.$btn.'</div>';
			
			} else {
				echo "<br><div class='btn disabled btn-activate btn-default inline-buttons' style='margin: 0px;'>Requires Pro v".$this->_pro_version."</div>"; 
			

			}

		
	}

	function get_button($path, $from_pro = false){
		_deprecated_function( __FUNCTION__, '0.9', 'SendPress_Module::buttons()' );
		
		$button = array('class' => 'btn btn-default module-deactivate-plugin', 'href' => '#', 'target' => '', 'text' => __('Deactivate','sendpress') );
		if( $from_pro ){
			$pro_options = SendPress_Option::get('pro_plugins');
			$reg_plugin = substr($path, 14, strlen($path));
			
			if( file_exists(WP_PLUGIN_DIR.'/'.$reg_plugin) && is_plugin_active($reg_plugin) ){
				deactivate_plugins($reg_plugin); //deactivate seperate plugin
				$pro_options[$path] = true; //activate the pro version
				SendPress_Option::set($pro_options);
				$pro_options = SendPress_Option::get('pro_plugins');
			}

			if(!array_key_exists($path, $pro_options)){
				!$pro_options[$path] = false;
			}

			if( !$pro_options[$path] ){
				$button['class'] = 'module-activate-plugin btn-success btn-activate btn';
				//$button['id'] = 'module-activate-plugin';
				$button['text'] = 'Activate';
			}else{
				$button['class'] = ' btn-default btn';
				$button['text'] = __('Deactivate','sendpress');
			}

		}else{
			if( !file_exists(WP_PLUGIN_DIR.'/'.$path) ){
				$button['class'] = 'module-deactivate-plugin btn-primary btn-buy btn';
				$button['href'] = 'http://www.sendpress.com/pricing/';
				$button['target'] = '_blank';
				$button['text'] = 'Buy Now';
				//$button['id'] = '';
			}elseif( !is_plugin_active($path) ){
				$button['class'] = 'module-activate-plugin btn-success btn-activate btn';
				//$button['id'] = 'module-activate-plugin';
				$button['text'] = 'Activate';
			}else{
					$button['class'] = ' btn-default btn';
				$button['text'] = 'Deactivate';
			}
		}

		return $this->build_button($button);

	}

	function build_button($btn){
			$button = '<a ';
			foreach( $btn as $key => $item ){
				if( strlen($btn[$key]) > 0 && $key !== 'text' ){
					$button .= $key.'="'.$item.'" ';
				}
			}
			$button .= '>'.$btn['text'].'</a>';


		return $button;
	}

	function is_pro($plugin){
		if( $plugin === 'sendpress-pro/sendpress-pro.php' ){
			return true;
		}
		return false;
	}

	function is_pro_active(){
		if( file_exists(WP_PLUGIN_DIR.'/sendpress-pro/sendpress-pro.php') && is_plugin_active('sendpress-pro/sendpress-pro.php') ){
			return true;
		}
		return false;
	}

	function pro_plugin_state(){
		$ret = 'not-installed';
		if( file_exists(WP_PLUGIN_DIR.'/sendpress-pro/sendpress-pro.php') ){
			$ret = 'installed';
			if( is_plugin_active('sendpress-pro/sendpress-pro.php') ){
				$ret = 'activated';
			}
		}
		
		if( $ret === 'not-installed' && get_transient( 'sendpress_key_state' ) === 'valid'  ){
			$ret = 'installable';
		}

		return $ret;
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
	function index( $index=NULL ) {
		if ( ! isset( $index ) )
			return $this->_index;
		$this->_index = $index;
	}

}

class SdndPress_Plugin_State{

	const Active = 0;
	const Inactive = 1;
	const Active_Pro = 2;
	const Inactive_Pro = 3;

}
