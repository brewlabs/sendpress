<?php 
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

global $post_ID, $post;
if(isset($_GET['emailID'])){
			$emailID = $_GET['emailID'];
			$post = get_post( $_GET['emailID'] );
			$post_ID = $post->ID;
} else {
	if(!defined('SENDPRESS_STYLER_PAGE')){
		SendPress_Admin::redirect('Emails');
	}
}
	


$default_styles_id = SendPress_Data::get_template_id_by_slug('user-style');

if( isset($emailID) ){
	if(false == get_post_meta( $default_styles_id , 'body_bg', true) ){
		$default_styles_id = SendPress_Data::get_template_id_by_slug('default-style');

	}

	$display_content = $post->post_content;
	//$display_content = apply_filters('the_content', $display_content);
	//$display_content = str_replace(']]>', ']]>', $display_content);

} else {
	
	$post =  get_post( $default_styles_id );
	$post_id = $post->ID;
	$default_styles_id =  SendPress_Data::get_template_id_by_slug('default-style');

	$display_content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas eget libero nisi. Donec pretium pellentesque rutrum. Fusce viverra dapibus nisi in aliquet. Aenean quis convallis quam. Praesent eu lorem mi, in congue augue. Fusce vitae elementum sapien. Vivamus non nisi velit, interdum auctor nulla. Morbi at sem nec ligula gravida elementum. Morbi ornare est et nunc tristique posuere.

Morbi iaculis fermentum magna, <a href="#">nec laoreet erat commodo vel</a>. Donec arcu justo, varius at porta eget, aliquet varius ipsum. Aliquam at lacus magna. Curabitur ullamcorper viverra turpis, vitae egestas mi tincidunt sed. Quisque fringilla adipiscing feugiat. In magna lectus, lacinia in suscipit sit amet, varius sed justo. Suspendisse vehicula, magna vitae porta pretium, massa ipsum commodo metus, eget feugiat massa leo in elit.';

						
	
}


$default_post = get_post( $default_styles_id );


/*
if(!isset($post)){

	$post = $default_post;
}
*/
if(isset($post) && false == get_post_meta( $post->ID , 'body_bg', true) ) {

	update_post_meta( $post->ID , 'body_bg',  get_post_meta( $default_post->ID , 'body_bg', true) );
	update_post_meta( $post->ID , 'body_text',  get_post_meta( $default_post->ID , 'body_text', true) );
	update_post_meta( $post->ID , 'body_link',  get_post_meta( $default_post->ID , 'body_link', true) );
	
	update_post_meta( $post->ID , 'header_bg',  get_post_meta( $default_post->ID , 'header_bg', true) );
	update_post_meta( $post->ID , 'header_text_color',  get_post_meta( $default_post->ID , 'header_text_color', true) );

	update_post_meta( $post->ID , 'content_bg',  get_post_meta( $default_post->ID , 'content_bg', true) );
	update_post_meta( $post->ID , 'content_text',  get_post_meta( $default_post->ID , 'content_text', true) );
	update_post_meta( $post->ID , 'sp_content_link_color',  get_post_meta( $default_post->ID , 'sp_content_link_color', true) );
	update_post_meta( $post->ID , 'content_border',  get_post_meta( $default_post->ID , 'content_border', true) );
	update_post_meta( $post->ID , 'upload_image',  get_post_meta( $default_post->ID , 'upload_image', true) );
	update_post_meta( $post->ID , 'image_header_url',  get_post_meta( $default_post->ID , 'image_header_url', true) );

	update_post_meta( $post->ID , 'header_text',  get_post_meta( $default_post->ID , 'header_text', true) );
	update_post_meta( $post->ID , 'header_link',  get_post_meta( $default_post->ID , 'header_link', true) );
	update_post_meta( $post->ID , 'sub_header_text',  get_post_meta( $default_post->ID , 'sub_header_text', true) );
	
	update_post_meta( $post->ID , 'active_header',  get_post_meta( $default_post->ID , 'active_header', true) );

} 

$body_bg = array(
	'value' => get_post_meta( $post->ID , 'body_bg', true),
	'std' => get_post_meta( $default_post->ID , 'body_bg', true),
);

$body_text = array(
	'value' => get_post_meta( $post->ID , 'body_text', true),
	'std' => get_post_meta( $default_post->ID , 'body_text', true),
);

$body_link = array(
	'value' => get_post_meta( $post->ID , 'body_link', true),
	'std' => get_post_meta( $default_post->ID , 'body_link', true),
);

$content_bg = array(
	'value' => get_post_meta( $post->ID , 'content_bg', true),
	'std' => get_post_meta( $default_post->ID , 'content_bg', true),
);

$content_border = array(
	'value' => get_post_meta( $post->ID , 'content_border', true),
	'std' => get_post_meta( $default_post->ID , 'content_border', true),
);

$content_text = array(
	'value' => get_post_meta( $post->ID , 'content_text', true),
	'std' => get_post_meta( $default_post->ID , 'content_text', true),
);

$content_link = array(
	'value' => get_post_meta( $post->ID , 'sp_content_link_color', true),
	'std' => get_post_meta( $default_post->ID , 'sp_content_link_color', true),
);

$upload_image = array(
	'value' => get_post_meta( $post->ID , 'upload_image', true),
	'std' => get_post_meta( $default_post->ID , 'upload_image', true),
);

$header_bg = array(
	'value' => get_post_meta( $post->ID , 'header_bg', true),
	'std' => get_post_meta( $default_post->ID , 'header_bg', true),
);

$header_text_color = array(
	'value' => get_post_meta( $post->ID , 'header_text_color', true),
	'std' => get_post_meta( $default_post->ID , 'header_text_color', true),
);

$header_text = array(
	'value' => get_post_meta( $post->ID , 'header_text', true),
	'std' => get_post_meta( $default_post->ID , 'header_text', true),
);

$header_link = array(
	'value' => get_post_meta( $post->ID , 'header_link', true),
	'std' => get_post_meta( $default_post->ID , 'header_link', true),
);

$image_header_url = array(
	'value' => get_post_meta( $post->ID , 'image_header_url', true),
	'std' => get_post_meta( $default_post->ID , 'image_header_url', true),
);

$sub_header_text = array(
	'value' => get_post_meta( $post->ID , 'sub_header_text', true),
	'std' => get_post_meta( $default_post->ID , 'sub_header_text', true),
);

$active_header = array(
	'value' => get_post_meta( $post->ID , 'active_header', true),
	'std' => get_post_meta( $default_post->ID , 'active_header', true),
);

if(strlen($upload_image['value']) > 0){
	$myimage= $upload_image['value'];
} else {
	$myimage= $upload_image['std'];
}

if(strlen($header_text['value']) > 0){
	$my_header_text = $header_text['value'];
} else {
	$my_header_text = $header_text['std'];
}

if(strlen($header_link['value']) > 0){
	$my_header_link = $header_link['value'];
} else {
	$my_header_link = $header_link['std'];
}

if(strlen($image_header_url['value']) > 0 ){
	$my_image_header_url = $image_header_url['value'];
} else {
	$my_image_header_url = $image_header_url['std'];
}

if(strlen($sub_header_text['value']) > 0){
	$my_sub_header_text = $sub_header_text['value'];
} else {
	$my_sub_header_text = $sub_header_text['std'];
}

if(strlen($active_header['value']) > 0){
	$my_active_header = $active_header['value'];
} else {
	$my_active_header = $active_header['std'];
}

?>
<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
<input type="hidden" value="save-style" name="save-action" id="save-action" />
<input type="hidden" value="save-style" name="action" />

<input type="hidden" value="<?php echo $my_active_header; ?>" name="active_header" id="active_header" />
<?php if( isset($emailID) ){ ?>
<h2>Edit & Style Email</h2>
<input value="simple.php" name="template" type="hidden" />
<?php } ?>
<div class="boxer">
<div class="boxer-inner">
	<?php if( isset($emailID) ){ ?><br>
	<?php $this->panel_start('<span class="glyphicon glyphicon-envelope"></span> '.  __('Subject','sendpress') ); ?>
	<input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
	<?php $this->panel_end(  ); ?>
<?php } ?>


	<div class="leftcol">
		<?php $this->panel_start( '<span class=" glyphicon glyphicon-tint"></span> '. __('Body Styles','sendpress') ); ?>
		
		Background<br>
		<?php $sp->create_color_picker( array('id'=>'body_bg','value'=>$body_bg['value'],'std'=>$body_bg['std'], 'link'=>'#html-view' ,'css'=>'background-color' ) ); ?>
		<br><br>
		Body Text Color<br>
		<?php $sp->create_color_picker( array('id'=>'body_text','value'=>$body_text['value'],'std'=>$body_text['std'], 'link'=>'.html-view-outer-text' ,'css'=>'color' ) ); ?>
		<br><br>
		Body Link Color<br>
		<?php $sp->create_color_picker( array('id'=>'body_link','value'=>$body_link['value'],'std'=>$body_link['std'], 'link'=>'.html-view-outer-text a' ,'css'=>'color' ) ); ?>
		<?php $this->panel_end(); ?>
		
		<?php $this->panel_start('<span class=" glyphicon glyphicon-tint"></span> '.  __('Header Styles','sendpress') ); ?>
			
		 Background<br>
		<?php $sp->create_color_picker( array('id'=>'header_bg','value'=>$header_bg['value'],'std'=>$header_bg['std'], 'link'=>'#html-header' ,'css'=>'background-color' ) ); ?>
		<br><br>
		
		 Text Color<br>
		<?php $sp->create_color_picker( array('id'=>'header_text_color','value'=>$header_text_color['value'],'std'=>$header_text_color['std'], 'link'=>'#html-header' ,'css'=>'color' ) ); ?>

		<?php $this->panel_end(); ?>
		
		<?php $this->panel_start('<span class=" glyphicon glyphicon-tint"></span> '.  __('Content Styles','sendpress') ); ?>
			
		 Background<br>
		<?php $sp->create_color_picker( array('id'=>'content_bg','value'=>$content_bg['value'],'std'=>$content_bg['std'], 'link'=>'#html-content','css'=>'background-color' ) ); ?>
		<br><br>
		Border<br>
		<?php $sp->create_color_picker( array('id'=>'content_border','value'=>$content_border['value'],'std'=>$content_border['std'], 'link'=>'.html-wrapper','css'=>'border-color' ) ); ?>
		<br><br>
		Text Color<br>
		<?php $sp->create_color_picker_iframe( array('id'=>'content_text','value'=>$content_text['value'],'std'=>$content_text['std'], 'link'=>'#html-content' ,'css'=>'color', 'iframe'=>'body' ) ); ?>
		<br><br>
		Link Color<br>
		<?php $sp->create_color_picker_iframe( array('id'=>'sp_content_link_color','value'=>$content_link['value'],'std'=>$content_link['std'],'link'=>'#html-content a' ,'css'=>'color' ,'iframe'=>'a') ); ?>
		
		<?php $this->panel_end(); ?>
		</div>


	<div style="margin-left: 250px;">
	<div class="widerightcol">
		<div id="imageaddbox" class="inputbox">
			
			<label for="upload_image">Enter an URL or upload an image for the banner.<br>
				<input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo $myimage; ?>"  /><br><a href="#" id="addimageupload" rel="<?php echo $post->ID; ?>" class="btn">Upload Image</a><span class="error">Image path required to activate image</span>
			</label>
			<br>
			<small>Width: 600px or less.</small><br>
			<small>Height: 200px recommmended but any height will work.</small>
			<br><br>
			<label for="image_header_url">Link:</label><input value="<?php echo $my_image_header_url; ?>" type="text" name="image_header_url" style="width: 100%;"><br><br>
			<a href="" id="activate-image" class="btn btn-primary"><?php if( $my_active_header === 'image' ){ echo 'Update'; }else{ echo 'Activate'; } ?></a>
			<a href="" id="close-image" class="btn">Close</a>
		</div>
		<div id="textaddbox" class="inputbox">
			<strong>Header Text:</strong><br><input type="text" name="header_text" value="<?php echo $my_header_text; ?>" style="width: 100%;"><br><br>
			<strong>Sub Header Text:</strong><br><input type="text" name="sub_header_text" value="<?php echo $my_sub_header_text; ?>" style="width: 100%;"><br><br>
			<strong>Header Link:</strong><br><input type="text" name="header_link" value="<?php echo $my_header_link; ?>" style="width: 100%;"><br><br>
			
			<a href="" id="activate-text" class="btn btn-primary">Save and Activate</a> <a href="" id="save-text" class="btn">Save</a>
		</div>

		<div id="html-view" class="html-view">
			<?php 
			$display_correct = __("Is this email not displaying correctly?","sendpress");
			$view = __("View it in your browser","sendpress");
			$start_text = __("Not interested anymore?","sendpress");
			$unsubscribe = __("Unsubscribe","sendpress");
			$instantly = __("Instantly","sendpress");
			?>
			<div class="html-view-holder">
				<div class="html-view-outer-text"><?php echo $display_correct; ?> <a href="#"><?php echo $view; ?></a>.
				</div>
				<div class="html-wrapper" class="html-wrapper">
					<div id='html-header' class='header-holder empty'>
						<div id="header-image"<?php if($my_active_header !== 'image'){ echo ' class="hide"'; } ?>>
							<?php if( strlen($my_image_header_url) > 0 ){ echo '<a href="'.$my_image_header_url.'">'; } ?>
								<img id="header-image-preview" src="<?php echo $myimage; ?>" />
							<?php if( strlen($my_image_header_url) > 0 ){ echo '</a>'; } ?>
						</div>
						<div id="header-text"<?php if($my_active_header !== 'text'){ echo ' class="hide"'; } ?>>
							<?php if( strlen($my_header_link) > 0 ){ echo '<a href="'.$my_header_link.'">'; } ?>
							<div id="header-text-title"><?php echo $my_header_text; ?></div>
							<?php if( strlen($my_header_link) > 0 ){ echo '</a>'; } ?>
							<div id="header-text-tagline"><?php echo $my_sub_header_text; ?></div>
						</div>
						<div id="header-controls">
							<div class="btn-group">
								<a href="" id="addimage" class="btn"><i class="icon-picture"></i> Image</a> <a href="" id="addtext" class="btn"><i class="icon-pencil	"></i> Text</a>
							</div>
						</div>
					</div>
					<div id='html-content' class="html-wrapper-inner">
				
						<div>

							<?php
							if( isset($emailID) ){ 
								if(function_exists('wp_editor')){ //Added Check for 3.2.1
									wp_editor($post->post_content,'content');
								} else {
									the_editor($post->post_content,'content');
								}
							} else {
								echo $display_content; 
							}
							?>
						</div>

					</div>
				</div>
				<div  class="html-view-outer-text">
					<div>
						<?php
						if( isset($emailID) ){
							$social = '';
							$bg = 	$body_link['value'];

							if($twit =  SendPress_Option::get('twitter') ){
								$social .= "<a href='$twit' style='color: $bg;'>Twitter</a>";
							}

							if($fb =  SendPress_Option::get('facebook') ){
								if($social != ''){
									$social .= " | ";
								}
								$social .= "<a href='$fb'  style='color: $bg;'>Facebook</a>";
							}
							if($ld =  SendPress_Option::get('linkedin') ){
								if($social != ''){
									$social .= " | ";
								}
								$social .= "<a href='$ld'  style='color: $bg;'>LinkedIn</a>";
							}
							//echo $social;
							echo SendPress_Data::build_social();
						} else {
						
echo SendPress_Data::build_social();
						?>
						
						<?php } ?>
					 <div id="can-spam-template">
					 	<?php
					 	if ( false !==  SendPress_Option::get('canspam') ){
					 		echo wpautop(  SendPress_Option::get('canspam') );

					 	} else { ?>	
					 	Blog/Company Name<br>
                                Street Address<br>
                                Anywhere, USA 01234<br>
                               <?php } ?>
                            </div><br>
                            <?php if( SendPress_Option::get('old_unsubscribe_link', false) === true ){ ?>
                                <?php echo $start_text ?> <a href="#" ><?php echo $unsubscribe ?></a> <?php echo $instantly ?>.
                               <?php } else { 
$manage = __("Manage Subscription","sendpress");
                               	?> 
<a href="#" ><?php echo $unsubscribe ?></a> | <a href="#" ><?php echo $manage ?></a>

                               <?php } ?>
				</div>
		  </div>
</div>
		</div>
		<?php if( method_exists($this,'text_settings')){
			$this->text_settings();
		}; ?>
	</div>

	<br class='clear'>


</div>
</div></div>



<div class="modal fade bs-modal-lg" id="sendpress-helper" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<ul class="nav nav-tabs" id="myTab">
			<li class="active tabs-first"><a href="#posts">Single Post</a></li>
		  	<li ><a href="#merge">Personalize</a></li>
		 
		  	<!--
		  <li><a href="#messages">Messages</a></li>
		  <li><a href="#settings">Settings</a></li>
			-->
		</ul>
	</div>
	<div class="modal-body">

 
<div class="tab-content">
	 <div class="tab-pane active" id="posts">

  	<div id="search-header">Search Posts: <input type="text" name="q" id="sp-single-query"></div>
  	<div  id="sp-post-preview" class="well">
  		No Post Selected
  	</div>

  	<p>Header HTML:&nbsp;
	  	<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios1" value="h1" >
		  H1
		</label>
		<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios2" value="h2">
		  H2
		</label>
		<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios2" value="h3" checked>
		  H3
		</label>
		<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios2" value="h4">
		  H4
		</label>
		<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios2" value="h5">
		  H5
		</label>
		<label class="radio">
		  <input type="radio" name="headerOptions" id="optionsRadios2" value="h6">
		  H6
		</label>
	</p>
	<p>Header Link:&nbsp;
	  	<label class="radio">
		  <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="link" checked>
		  Link Header to Post
		</label>
		<label class="radio">
		  <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="nolink">
		  Don't Link Header to Post
		</label>
	</p>
  	<p>Post Content:&nbsp;
	  	<label class="radio">
		  <input type="radio" name="optionsRadios" id="optionsRadios1" value="excerpt" checked>
		  Excerpt
		</label>
		<label class="radio">
		  <input type="radio" name="optionsRadios" id="optionsRadios2" value="full">
		  Full Post
		</label>
	</p>
  	<button class="btn btn-mini btn-success sp-insert-code" id="sp-post-preview-insert" data-code="">Insert</button>
  </div>
 	<div class="tab-pane " id="merge">
 		<h3>Subscriber specific content</h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td>First Name</td>
      	<td>*|FNAME|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|FNAME|*">Insert</button></td>
    </tr>
    <tr>
    	<td>Last Name</td>
      	<td>*|LNAME|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|LNAME|*">Insert</button></td>
    </tr>
    <tr>
    	<td>Email</td>
      	<td>*|EMAIL|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|EMAIL|*">Insert</button></td>
    </tr>

  </tbody>
</table>
	<h3>Site specific content</h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td>Website URL</td>
      	<td>*|SITE:URL|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:URL|*">Insert</button></td>
    </tr>
    <tr>
    	<td>Website Title</td>
      	<td>*|SITE:TITLE|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:TITLE|*">Insert</button></td>
    </tr>
    <tr>
    	<td>Website Description</td>
      	<td>*|SITE:DECRIPTION|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|SITE:DESCRIPTION|*">Insert</button></td>
    </tr>
    
  </tbody>
</table>
<h3>Date and Time</h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td>Current Date<br><small>Format based on WordPress settings.</small></td>
      	<td>*|DATE|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE|*">Insert</button></td>
    </tr>
     <tr>
    	<td>Current Time<br><small>5:16 pm</small></td>
      	<td>*|DATE:g:i a|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:g:i a|*">Insert</button></td>
    </tr>
    <tr>
    	<td>Custom Date<br><small>March 10, 2001, 5:16 pm</small></td>
      	<td>*|DATE:F j, Y, g:i a|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:F j, Y, g:i a|*">Insert</button></td>
    </tr>
  
    
  </tbody>
</table>

  </div>
 
  <div class="tab-pane" id="messages">...</div>
  <div class="tab-pane" id="settings">...</div>
</div>
		
	</div>
	<div class="modal-footer">
	 	<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
	</div>
</div>
</div>
</div>




<?php wp_nonce_field($sp->_nonce_value); 