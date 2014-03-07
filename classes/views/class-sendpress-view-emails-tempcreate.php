<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_View_Emails_Create
*
* @uses     SendPress_View
*
* @package  SendPress
* @since 0.8.7
*
*/
class SendPress_View_Emails_Tempcreate extends SendPress_View_Emails {

	function save(){

		$post = get_default_post_to_edit( SendPress_Data::template_post_type() , true );
		$post_ID = $post->ID;
	
		global $current_user;
		$content = '';
		switch($_POST['starter']){
			case 'blank':
				$content = '';
			break;
			default:
				$content = 'Default HTML';
			break;



		}

        /*            
        $my_post['ID'] = $_POST['post_ID'];
        $my_post['post_content'] = $_POST['content'];
        $my_post['post_title'] = $_POST['post_title'];
        */
      	$post->post_title = $_POST['post_title'];
        $post->post_status = 'publish';
        $post->post_content = $content;
        // Update the post into the database
        wp_update_post( $post );
       
        //SendPress_Email::set_default_style( $my_post['ID'] );
        //clear the cached file.
       
        
        SendPress_Admin::redirect( 'Emails_Tempedit' , array('templateID' =>  $post->ID  )   );
        //$this->save_redirect( $_POST  );

	}
	
	function html($sp) {
		
		?>
		<form method="POST" name="post" id="post">
		<div id="styler-menu">
			<div style="float:right;" class="btn-group">
				<input type="submit" value="<?php _e('Save & Next','sendpress'); ?>" class="btn btn-primary" />
			</div>
			<div id="sp-cancel-btn" style="float:right; ">
				<a href="<?php echo SendPress_Admin::link('Emails_Templates'); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
			</div>
		</div>
		
		<h2>Create Template</h2>
		<br>
		<!--
		has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			
			<div class="clear"><br>
			<?php echo do_action('do_meta_boxes', $sp->_email_post_type, 'side', $post); 
			do_meta_boxes($post_type, 'side', $post);?>
			</div>
		</div>
		-->
	
			<?php $this->panel_start('<span class="glyphicon glyphicon-list-alt"></span> '. __('Template Name','sendpress')); ?>
			<input type="text" class="form-control" name="post_title" size="30" tabindex="1" value="" id="post_title" autocomplete="off" />
<?php
$this->panel_end();
?>
		
			
<div class="sp-row">
	<div class="sp-25 sp-first">
		<?php $this->panel_start('<input type="radio" name="starter" value="orginal" checked /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="blank"  /> Blank Template' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	<!--
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="default"  /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="default"  /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	-->
</div>


		
		
		<?php //wp_editor($post->post_content,'textversion'); ?>

		 <?php wp_nonce_field($sp->_nonce_value); ?><br><br>
		 </form>
		 
		<?php
	}

}
