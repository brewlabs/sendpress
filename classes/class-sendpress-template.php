<?php
// SendPress Required Class: SendPress_Template

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(class_exists('SendPress_Template')){ return; }


/**
* SendPress_Options
*
* @uses     
*
* 
* @package  SendPRess
* @author   Josh Lyford
* @license  See SENPRESS
* @since 	0.8.7     
*/
class SendPress_Template {

	private static $instance;
	static $cache_key_templates = 'sendpress:email_template_cache';
	var $_info = array();

	function __construct() {
		$this->init();
	}

	function init() {
		if ( false === ( $sp_templates = get_transient ( self::$cache_key_templates ) ) ) {
		$mainfiles = $this->glob_php( SENDPRESS_PATH . 'templates' );
		$themmefiles = $this->glob_php( TEMPLATEPATH . '/sendpress' );
		$wordpressfiles = $this->glob_php( WP_CONTENT_DIR . '/sendpress' );
		
		$childfiles = array();
			if( is_child_theme() ){
				$childfiles = $this->glob_php( STYLESHEETPATH . '/sendpress' );
			}
		$temps =array_merge($mainfiles, $themmefiles, $childfiles, $wordpressfiles);
		$sp_templates =  array();
		foreach ($temps as $temp) {
			if($info = $this->get_template_info($temp[0]) ){
				$sp_templates[$temp[1]] = $info;
			} 
		}
		set_transient( self::$cache_key_templates, $sp_templates , 60*60 );
		}	
		$this->info( $sp_templates );
		do_action( 'sendpress_template_loaded' );
	}


	function info( $info=NULL ) {
		if ( ! isset( $info ) )
			return $this->_info;
		$this->_info = $info;
	}

	static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}

	/**
	 * Load module data from module file. Headers differ from WordPress
	 * plugin headers to avoid them being identified as standalone
	 * plugins on the WordPress plugins page.
	 */
	function get_template_info( $file ) {
		$headers = array(
			'name' => 'SendPress',
			'regions' => 'Regions',
			'description' => 'Description',
			'sort' => 'Sort Order',
		);
		$mod = get_file_data( $file, $headers );
		$mod['file'] = $file;

		if ( empty( $mod['sort'] ) )
			$mod['sort'] = 10;
		if ( !empty( $mod['name'] ) || !empty( $mod['regions'] ) )
			return $mod;
		return false;
	}

	/**
	 * Returns an array of all PHP files in the specified absolute path.
	 * Equivalent to glob( "$absolute_path/*.php" ).
	 *
	 * @param string $absolute_path The absolute path of the directory to search.
	 * @return array Array of absolute paths to the PHP files.
	 */
	function glob_php( $absolute_path ) {
		$absolute_path = untrailingslashit( $absolute_path );
		$files = array();
		if(is_dir($absolute_path)){
		if (!$dir = @opendir( $absolute_path ) ) {
			return $files;
		}
		
		while ( false !== $file = readdir( $dir ) ) {
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, -4 ) ) {
				continue;
			}

			$file2 = "$absolute_path/$file";

			if ( !is_file( $file2 ) ) {
				continue;
			}
			$basename = str_replace($absolute_path, '', $file);
			$files[] = array($file2, $basename);
		}

		closedir( $dir );
		}

		return $files;
	}

	function get_template($post_id){
		$temp = get_post_meta($post_id,'_sendpress_template', true);
		if($temp == false )
		{
			return 'simple.php';
		}
		return $temp;
	}

	function render($post_id = false, $render = true, $inline = false, $no_links = false){
		global $post;

		$saved = false;
		if($post_id !== false){
			$post = get_post( $post_id );
			$saved = $post;
		}
		$saved = $post;
		if(!isset($post)){
			echo __('Sorry we could not find your email.','sendpress');
			return;
		}
		$selected_template = $this->get_template( $post_id );
		$template_list = $this->info();
		if( isset($template_list[$selected_template]) ){
			
			ob_start();
			require_once( $template_list[$selected_template]['file'] );
			$HtmlCode= ob_get_clean(); 
			
			$post = $saved;
			
			$HtmlCode =str_replace("*|SP:SUBJECT|*",$post->post_title ,$HtmlCode);

			$body_bg			=	get_post_meta( $post->ID , 'body_bg', true );
			$body_text			= 	get_post_meta( $post->ID , 'body_text', true );
			$body_link			=	get_post_meta( $post->ID , 'body_link', true );
			$header_bg			=	get_post_meta( $post->ID , 'header_bg', true );
			$active_header		=	get_post_meta( $post->ID , 'active_header', true );
			$upload_image		=	get_post_meta( $post->ID , 'upload_image', true );
			$header_text_color	=	get_post_meta( $post->ID , 'header_text_color', true );
			$header_text		=	get_post_meta( $post->ID , 'header_text', true ); //needs adding to the template
			$header_link		=	get_post_meta( $post->ID , 'header_link', true ); //needs adding to the template
			$sub_header_text	=	get_post_meta( $post->ID , 'sub_header_text', true ); //needs adding to the template
			$image_header_url	=	get_post_meta( $post->ID , 'image_header_url', true ); //needs adding to the template
			$content_bg			=	get_post_meta( $post->ID , 'content_bg', true );
			$content_text		=	get_post_meta( $post->ID , 'content_text', true );
			$content_link		=	get_post_meta( $post->ID , 'sp_content_link_color', true );
			$content_border		=	get_post_meta( $post->ID , 'content_border', true );

			$header_link_open = '';
			$header_link_close = '';
		
			if($active_header == 'image'){
				if(!empty($image_header_url)){
					$header_link_open = "<a style='color:".$header_text_color."' href='".$image_header_url."'>";
					$header_link_close = "</a>";

				}
				$headercontent = $header_link_open. "<img style='display:block;' src='".$upload_image."' border='0' />". $header_link_close;
				$HtmlCode =str_replace("*|SP:HEADERCONTENT|*",$headercontent ,$HtmlCode);
			} else {
				if(!empty($header_link)){
					$header_link_open = "<a style='color:".$header_text_color."' href='".$header_link."'>";
					$header_link_close = "</a>";

				}
				$headercontent =  "<div style='padding: 10px; text-align:center;'><h1 style='text-align:center; color: ".$header_text_color." !important;'>".$header_link_open. $header_text . $header_link_close."</h1>".$sub_header_text."</div>";
				$HtmlCode =str_replace("*|SP:HEADERCONTENT|*",$headercontent ,$HtmlCode);
			}

			$HtmlCode =str_replace("*|SP:HEADERBG|*",$header_bg ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:HEADERTEXT|*",$header_text_color ,$HtmlCode);

			$HtmlCode =str_replace("*|SP:BODYBG|*",$body_bg ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:BODYTEXT|*",$body_text ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:BODYLINK|*",$body_link ,$HtmlCode);

			$HtmlCode =str_replace("*|SP:CONTENTBG|*",$content_bg ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:CONTENTTEXT|*",$content_text ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:CONTENTLINK|*",$content_link ,$HtmlCode);
			$HtmlCode =str_replace("*|SP:CONTENTBORDER|*",$content_border ,$HtmlCode);
			
			$HtmlCode = $this->tag_replace($HtmlCode);
			
			// Date processing
	        
			$canspam = wpautop( SendPress_Option::get('canspam') );

			$HtmlCode =str_replace("*|SP:CANSPAM|*",$canspam ,$HtmlCode);

			$social = '';
			if($twit = SendPress_Option::get('twitter') ){
				$social .= "<a href='$twit' style='color: $body_link;'>Twitter</a>";
			}

			if($fb = SendPress_Option::get('facebook') ){
				if($social != ''){
					$social .= " | ";
				}
				$social .= "<a href='$fb'  style='color: $body_link;'>Facebook</a>";
			}
			if($ld = SendPress_Option::get('linkedin') ){
				if($social != ''){
					$social .= " | ";
				}
				$social .= "<a href='$ld'  style='color: $body_link;'>LinkedIn</a>";
			}
			$social = SendPress_Data::build_social( $body_link );
			$HtmlCode = str_replace("*|SP:SOCIAL|*",$social ,$HtmlCode);

			$dom = new DomDocument();
				$dom->strictErrorChecking = false;
				@$dom->loadHtml($HtmlCode);
				$iTags = $dom->getElementsByTagName('img');
				foreach ($iTags as $iElement) {
					$class = $iElement->getAttribute('class');
				}
				$body_html = $dom->saveHtml();

			/*
			$simplecss = file_get_contents(SENDPRESS_PATH.'/templates/simple.css');
				
			// create instance
			$cssToInlineStyles = new CSSToInlineStyles($HtmlCode, $simplecss);

			// grab the processed HTML
			$HtmlCode = $cssToInlineStyles->convert();

			*/
			$display_correct = __("Is this email not displaying correctly?","sendpress");
			$view = __("View it in your browser","sendpress");
			$start_text = __("Not interested anymore?","sendpress");
			$unsubscribe = __("Unsubscribe","sendpress");
			$instantly = __("Instantly","sendpress");
			$manage = __("Manage Subscription","sendpress");
			if($render){
				//RENDER IN BROWSER

				if($inline){
					$link = get_permalink(  $post->ID );
					$browser = $display_correct.' <a style="color: '.$body_link.';" href="'.$link.'">'.$view.'</a>.';
					$HtmlCode =str_replace("*|SP:BROWSER|*",$browser ,$HtmlCode);
					$remove_me = ' <a href="#"  style="color: '.$body_link.';" >'.$unsubscribe.'</a> | ';
				$manage = ' <a href="#"  style="color: '.$body_link.';" >'.$manage.'</a> ';

					$HtmlCode =str_replace("*|SP:MANAGE|*",$manage,$HtmlCode);
					$HtmlCode =str_replace("*|SP:UNSUBSCRIBE|*",$remove_me ,$HtmlCode);
			
				} else {
					$HtmlCode =str_replace("*|SP:BROWSER|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|SP:UNSUBSCRIBE|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|SP:MANAGE|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|ID|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|FNAME|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|LNAME|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|EMAIL|*",'' ,$HtmlCode);
				}
				echo $HtmlCode;
			} else {
				//PREP FOR SENDING
				if($no_links == false){
				$link = get_permalink(  $post->ID );
				$open_info = array(
					"id"=>$post->ID,

					"view"=>"email"
				);
				$code = SendPress_Data::encrypt( $open_info );

				$xlink = SendPress_Manager::public_url($code);
				$browser = $display_correct.' <a style="color: '.$body_link.';" href="'.$xlink.'">'.$view.'</a>.';
				$HtmlCode =str_replace("*|SP:BROWSER|*",$browser ,$HtmlCode);
				
				}else {
					$HtmlCode =str_replace("*|SP:BROWSER|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|SP:UNSUBSCRIBE|*",'' ,$HtmlCode);
					$HtmlCode =str_replace("*|SP:MANAGE|*",'' ,$HtmlCode);
				}
				return $HtmlCode;
			}

		} else {
			echo __('Sorry we could not find your email template.','sendpress');
			return;	
		}
	}

	static function tag_replace($HtmlCode){
			$HtmlCode = str_replace('*|SITE:URL|*', get_option('home'), $HtmlCode);
	        $HtmlCode = str_replace('*|SITE:TITLE|*', get_option('blogname'), $HtmlCode);
	        $HtmlCode = str_replace('*|DATE|*', date_i18n(get_option('date_format')), $HtmlCode);
	        $HtmlCode = str_replace('*|SITE:DESCRIPTION|*', get_option('blogdescription'), $HtmlCode);
	        $x = 0;
	        while (($x = strpos($HtmlCode, '*|DATE:', $x)) !== false) {
	            $y = strpos($HtmlCode, '|*', $x);
	            if ($y === false) continue;
	            $f = substr($HtmlCode, $x+7, $y-$x-7);
	            $HtmlCode = substr($HtmlCode, 0, $x) . date_i18n($f) . substr($HtmlCode, $y+2);
	        }

	        return $HtmlCode;
	}


}

// Initialize!
SendPress_Template::get_instance();
