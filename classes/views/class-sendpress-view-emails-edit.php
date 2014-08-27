<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Edit extends SendPress_View_Emails {
	
	

	function save(){
	   //print_r($_POST['content-1']);
//content-area-one-edit
	//$template = get_post();
	//$_POST['post_type'] = 'sp_newsletters';
 	$my_post = _wp_translate_postdata(true);
 	//print_r($my_post);
 	//$template['post_content'] = $my_post->content_area_one_edit;
 	$post_update = array(
 		'ID'           => $my_post['post_ID'],
      	'post_content' => $my_post['content_area_one_edit']
      );
update_post_meta( $my_post['ID'], '_sendpress_template', $_POST['template'] );

 	//	print_r($template);
	wp_update_post( $post_update );
	SendPress_Admin::redirect('Emails_Edit', array('emailID'=>$_GET['emailID'] ));
	}

	function admin_init(){
		global $is_IE;
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
		if (  ! wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
	}

	function html($sp) {
		global $is_IE;
		global $post_ID, $post;
		if (  wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		$view = isset($_GET['view']) ? $_GET['view'] : '' ;

		if(isset($_GET['emailID'])){
			$emailID = $_GET['emailID'];
			$post = get_post( $_GET['emailID'] );
			$post_ID = $post->ID;
		}
	
        if($post->post_type !== 'sp_newsletters'){
            SendPress_Admin::redirect('Emails');
        }
        $template_id = get_post_meta( $post->ID , '_sendpress_template' , true);

		?>
        <form method="POST" name="post" id="post">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
       <div  >
	<div id="button-area">  
		<input type="submit" value="<?php _e('Save','sendpress'); ?>" class="btn btn-large btn-primary"/>
	</div>

</div>
        <h2>Edit Email Content</h2>
        <br>
        <?php $this->panel_start('<span class="glyphicon glyphicon-envelope"></span> '.  __('Subject','sendpress') ); ?>
        <input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
        <?php $this->panel_end(  ); ?>
        <div class="sp-row">
<div class="sp-75 sp-first">
<!-- Nav tabs -->
<ul class="nav nav-tabs">
  <li class="active"><a href="#content-area-one-tab" data-toggle="tab">Main Content</a></li>
  <!--
  <li><a href="#profile" data-toggle="tab">Profile</a></li>
  <li><a href="#messages" data-toggle="tab">Messages</a></li>
  <li><a href="#settings" data-toggle="tab">Settings</a></li>
  -->
</ul>

<div class="tab-content">
  <div class="tab-pane fade in active" id="content-area-one-tab">
  <?php wp_editor( $post->post_content, 'content_area_one_edit', array(
	'dfw' => true,
	'drag_drop_upload' => true,
	'tabfocus_elements' => 'insert-media-button-1,save-post',
	'editor_height' => 360,
	'tinymce' => array(
		'resize' => false,
		'wp_autoresize_on' => ( ! empty( $_wp_autoresize_on ) && get_user_setting( 'editor_expand', 'on' ) === 'on' ),
		'add_unload_trigger' => false,
	),
) ); ?><?php //the_editor($post->post_content,'content_area_one_edit'); ?></div>
  <!--
  <div class="tab-pane fade" id="profile"><?php the_editor($post->post_content,'content-2'); ?></div>
  <div class="tab-pane fade" id="messages"><?php the_editor($post->post_content,'content-3'); ?></div>
  <div class="tab-pane fade" id="settings"><?php the_editor($post->post_content,'content-4'); ?></div>
  -->
</div>

</div>
<div class="sp-25">
<br><br>

	<?php $this->panel_start( __('Template','sendpress') ); ?>
	<select name="template" class="form-control">
	<?php
			$args = array(
			'post_type' => 'sp_template' ,
			'post_status' => array('sp-standard'),
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
			echo  '<optgroup label="SendPress Templates">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$temp_id = $the_query->post->ID;
				$s = '';
				if($temp_id == $template_id){
					$s = 'selected';
				}
				echo '<option value="'.$temp_id .'" '.$s.'>' . get_the_title() . '</option>';
			}
			echo  '</optgroup>';
			
		}

		$args = array(
			'post_type' => 'sp_template' ,
			'post_status' => array('sp-custom'),
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				echo  '<optgroup label="Custom Templates">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$temp_id = $the_query->post->ID;
				$s = '';
				if($temp_id == $template_id){
					$s = 'selected';
				}
				echo '<option value="'.$temp_id .'" '.$s.'>' . get_the_title() . '</option>';
			}
			echo  '</optgroup>';
			
		}
	?>
	
	</select>
	<?php $this->panel_end(  ); ?>
</div>
</div>


	<?php SendPress_Data::nonce_field(); ?>
        </form>
	<?php
	}

}