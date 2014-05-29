<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
/**
 * Unsubscribe Form Shortcode
 *
 * 
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Recent_Posts extends SendPress_SC_Base {

	public static function title(){
		return __('Get Recent Posts', 'sendpress');
	}

	public static function options(){
		return 	array(
			 'posts' => 1,
			);
	}

	public static function html(){
		return __('You can provide a Title. This is added before the post loop begins.','sendpress');
	}
	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts , $content = null ) {
		global $post , $wp;
		$old_post = $post;
		extract( shortcode_atts( self::options() , $atts ) );
		
	   	if($content){
	      	$return_string = $content;
	  	}
	  	
		$return_string .= '<div>';
	   	query_posts(array('orderby' => 'date', 'order' => 'DESC' , 'showposts' => $posts));
	   	if (have_posts()) :
	    	while (have_posts()) : the_post();
	        	$return_string .= '<div><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
	          	$return_string .= '<div>'.get_the_excerpt().'</div>';
	          	$return_string .= '<br>';
	      	endwhile;
	   	endif;
	   	$return_string .= '</div>';

	   	wp_reset_query();
	   	$post = $old_post;
	   	return $return_string;

	}

	public static function docs(){
		return __('This shortcode creates a listing of Posts in emails or on pages.', 'sendpress');
	}


}
