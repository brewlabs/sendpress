<?php
// SendPress Required Class: SendPress_Signup_Shortcode

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Shortcodes{

	static function init(){
		  add_shortcode('sendpress-posts', array('SendPress_Shortcodes','recent_posts_function'));
	}

	

	static function recent_posts_function($atts, $content = null) {
		global $post;
		$old_post = $post;
   extract(shortcode_atts(array(
      'posts' => 1,
   ), $atts));
   	if($content){
      	$return_string = '<h3>'.$content.'</h3>';
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

}

