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
class SendPress_SC_Recent_Posts_By_User extends SendPress_SC_Base {

	public static function display_docs(){
		return false;
	}

	public static function title(){
		return __('Get Recent Posts By User', 'sendpress');
	}

	public static function options(){
		return 	array(
			 'posts' => 1,
			 'uid' => 0,
			 'imgalign' => 'left',
			 'alternate' => false
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
		extract( shortcode_atts( self::options() , $atts ) );

		if($uid > 0){
			$user = "uid='".$uid."'";
		}

		$shortcode = "[sp-recent-posts-by-user posts='".$posts."' ".$user." imgalign='".$imgalign."' alternate='".$alternate."' readmoretext='".$readmoretext."' ]";

		//return $shortcode;
		return $shortcode . " <br><br> " . do_shortcode($shortcode);

	}

	public static function docs(){
		return __('This shortcode creates a listing of Posts in emails or on pages.', 'sendpress');
	}


}
