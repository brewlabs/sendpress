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
			 'uid' => 0,
			 'imgalign' => 'left',
			 'alternate' => false,
			 'imgcolwidth' => '30%',
			 'textcolwidth' => '60%',
			 'stylereadmore' => '',
			 'readmoretext'=>'',
			 'styletitle' => '',
			 'featuredimg' => 'thumbnail',
			 'cat' => '',
			 'tag' => '',
			 'category_name' => '',
			 'columns' => 1,
			 'datespan' => '',
			 'displayimages' => true,
			 'display_author' => false
			);
	}

	 //'show_title'=>true,
	 //'show_text'=>true,
	 //'show_readmore'=>true,
	 //'show_photo'=>true,

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
		$swap = false;
		extract( shortcode_atts( SendPress_SC_Recent_Posts::options() , $atts ) );

		$args = array('orderby' => 'date', 'order' => 'DESC' , 'showposts' => $posts, 'post_status' => 'publish');

		if($uid > 0){
			$args['author'] = $uid;
		}

		if(strlen($readmoretext) === 0){
			$readmoretext = 'Read More';
		}

		if($alternate === 1 || $alternate === '1' || $alternate === 'true' || $alternate === true){
			$swap = true;
		}

		if(strlen($imgalign) === 0){
			$imgalign = 'left';
		}

		if(strlen($imgcolwidth) === 0){
			$imgcolwidth = '30%';
		}

		if(strlen($textcolwidth) === 0){
			$textcolwidth = '65%';
		}

		if(strlen($styletitle) === 0){
			$styletitle = '';
		}

		if(strlen($stylereadmore) === 0){
			$stylereadmore = '';
		}

		if(strlen($featuredimg) === 0){
			$featuredimg = 'thumbnail';
		}

		if(strlen($cat) > 0){
			$args['cat'] = $cat;
		}
		if(strlen($category_name) > 0){
			$args['category_name'] = $category_name;
		}
		if(strlen($tag) > 0){
			$args['tag'] = $tag;
		}

		if( strlen($datespan) > 0 ){
			$after = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('-1 day')));
			$before = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('now')));

			if(is_numeric($datespan)){
				$after = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('-'.$datespan.' day')));
				$before = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('now')));
			}

			if($datespan === 'weekly'){
				$after = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('-1 week')));
				$before = get_date_from_gmt(date('Y-m-d H:i:s',strtotime('now')));
			}

			$args['date_query'] = array(
				array(
					'after'     => $after,
					'before'    => $before,
					'inclusive' => true
				)
			);
		}

		if($columns < 1){
			$columns = 1;
		}

		$return_string = '';
	   	if($content){
	      	$return_string = $content;
	  	}

	   	$query = new WP_Query($args);
		if($query->have_posts()){

			$number_of_columns = 1;
			if($query->found_posts > 1){
				$number_of_columns = $columns;
			}

			if($number_of_columns > 3){
				$number_of_columns = 3;
			}

			$column_template = "";
			$col1 = "";
			$col2 = "";
			$col3 = "";
			switch($number_of_columns){
				case 2:
					$column_template = SendPress_Data::two_column();
					break;
				case 3:
					$column_template = SendPress_Data::three_column();
					break;
			}

			$idx = 0;
			$current_column = 0;

			while($query->have_posts()){
				$query->the_post();

				if(has_post_thumbnail() && $displayimages){
					//reset the template because we have an image
					$template = (strtolower($imgalign) === 'left') ? SendPress_Data::post_img_left() : SendPress_Data::post_img_right();
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $featuredimg);
					$template = str_replace('{sp-post-image}',$img[0],$template);
				} else {
					$template = SendPress_Data::post_text_only();
				}

				$template = str_replace( '{sp-post-link}' , get_permalink() ,$template);
				$template = str_replace( '{sp-post-title}',  get_the_title() ,$template);
				$template = str_replace( '{sp-post-excerpt}', get_the_excerpt() ,$template);
				$template = str_replace( '{sp-post-readmore}', $readmoretext ,$template);
				$template = str_replace( '{sp-post-img-col-width}' , $imgcolwidth ,$template);
				$template = str_replace( '{sp-post-text-col-width}' , $textcolwidth ,$template);
				$template = str_replace( '{sp-post-style-title}' , $styletitle ,$template);
				$template = str_replace( '{sp-post-style-readmore}' , $stylereadmore ,$template);

				if( $display_author ){
					$template = str_replace( '{sp-author-template}' , '<a href="{sp-post-author-link}">{sp-post-author}</a>' ,$template);

					$template = str_replace( '{sp-post-author}' , get_the_author() ,$template);
					$template = str_replace( '{sp-post-author-link}' , get_author_posts_url(get_the_author_meta( 'id' ),get_the_author_meta( 'user_nicename' )) ,$template);
				}else{
					$template = str_replace( '{sp-author-template}' , '' ,$template);
				}

				//get_author_posts_url
	          	$imgalign = ($swap && strtolower($imgalign) === 'left') ? 'right' : 'left';

	          	//put things into columns always
	          	if($number_of_columns < 2){
	          		$col1 .= $template;
	          	}else{

	          		switch($current_column){
	          			case 0:
	          				$col1 .= $template;
	          				break;
	          			case 1:
	          				$col2 .= $template;
	          				break;
	          			case 2:
	          				$col3 .= $template;
	          				break;
	          		}

	          	}
	          	$template = '';
	          	$idx++;
	          	$current_column++;

	          	if( $current_column == $number_of_columns){
	          		$current_column = 0;
	          	}
			}
		}

		if(strlen($column_template) === 0){
			$return_string = $col1;
		}else{

			//add columns to column template
			$column_template = str_replace( '{sp-col-1}' , $col1 ,$column_template);
			$column_template = str_replace( '{sp-col-2}' , $col2 ,$column_template);
			$column_template = str_replace( '{sp-col-3}' , $col3 ,$column_template);

			$return_string = $column_template;
		}

		wp_reset_postdata();

	   	//$return_string .= '</div>';
	   	wp_reset_query();
	   	$post = $old_post;
	   	return $return_string;

	}

	public static function post_text_only(){
		return '<table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row"><tr><td class="col" valign="top" style="width:100%"><div><a href="{sp-post-link}">{sp-post-title}</a></div><div>{sp-author-template}</div><div>{sp-post-excerpt}</div><div><a href="{sp-post-link}">{sp-post-readmore}</a></div><br></td></tr></table>';
	}

	public static function post_img_left(){
		return '

<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="100%">

		<table width="{sp-post-img-col-width}" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
			<tr>
				<td class="col" valign="top">
					<img style="margin-bottom:10px;" class="image_fix" width="100%" src="{sp-post-image}"/>
				</td>
			</tr>
		</table>

		<table width="{sp-post-text-col-width}" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
			<tr>
				<td class="col" valign="top">
					<div class="post-title"><a href="{sp-post-link}" style="{sp-post-style-title}"><span style="{sp-post-style-title}">{sp-post-title}</span></a></div>
					<div>{sp-author-template}</div>
					<div class="post-excerpt">{sp-post-excerpt}</div>
					<div class="post-readmore"><a href="{sp-post-link}" style="{sp-post-style-readmore}"><span style="{sp-post-style-readmore}">{sp-post-readmore}</span></a></div>
					<br>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<br>
		';
	}

	public static function post_img_right(){
		return '
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td width="100%">

		<table width="{sp-post-text-col-width}" border="0" cellpadding="0" cellspacing="0" align="left" class="force-row">
			<tr>
				<td class="col" valign="top">
					<div class="post-title"><a href="{sp-post-link}" style="{sp-post-style-title}"><span style="{sp-post-style-title}">{sp-post-title}</span></a></div>
					<div>{sp-author-template}</div>
					<div class="post-excerpt">{sp-post-excerpt}</div>
					<div class="post-readmore"><a href="{sp-post-link}" style="{sp-post-style-readmore}"><span style="{sp-post-style-readmore}">{sp-post-readmore}</span></a></div>
					<br>
				</td>
			</tr>
		</table>

		<table width="{sp-post-img-col-width}" border="0" cellpadding="0" cellspacing="0" align="right" class="force-row">
			<tr>
				<td class="col" valign="top"><img style="margin-bottom:10px;" class="image_fix" width="100%" src="{sp-post-image}"/></td>
			</tr>
		</table>
	</td>
</tr>
</table>
<br>
';
	}

	public static function docs(){
		return __('This shortcode creates a listing of Posts in emails or on pages.  Use the following options to customize the output: <br><br><b>posts</b> - number of posts to display. (defaults to 1)<br><b>uid</b> - the user id of the author you would like to see.<br><b>imgalign</b> - Align images left or right. (defaults to left)<br><b>alternate</b> - when writing posts, alternate the thumbnail images. (defaults to false)<br><b>readmoretext</b> - the text for the readmore link (defaults to Read More)<br><b>columns</b> - the number of columns your posts should display in.  Max number of columns is 3.<br><b>datespan</b> - number of days of posts to render.  Can be set to "daily", "weekly", or a number of days (datespan=3 for the last three days for exmaple) (defaults to daily.)<br><b>displayimages</b> - Set to true by default, set to false if you want no images to display.<br><b>display_author</b> - Set to false by default, set to true the author link will display.', 'sendpress');
	}


}
