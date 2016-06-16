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
class SendPress_View_Emails_Createauto extends SendPress_View_Emails {

	function save(){
		//$this->security_check();
		$_POST['post_type'] = SendPress_Data::email_post_type();
        // Update post 37 (37!)

        $my_post = _wp_translate_postdata(true);
        
        $my_post['post_status'] = 'sp-autoresponder';
        // Update the post into the database
        wp_update_post( $my_post );
        update_post_meta( $my_post['ID'], '_sendpress_subject', sanitize_text_field( SPNL()->validate->_string('post_subject') ) );
        update_post_meta( $my_post['ID'], '_sendpress_template', SPNL()->validate->_int('template') );
        update_post_meta( $my_post['ID'], '_sendpress_status', 'private');
 		
       	update_post_meta( $my_post['ID'], '_sendpress_system',  'new');

        SendPress_Email::set_default_style( $my_post['ID'] );
        //clear the cached file.
        delete_transient( 'sendpress_email_html_'. $my_post['ID'] );

        
        SendPress_Admin::redirect( 'Emails_Edit' , array('emailID' =>  $my_post['ID']  )   );
    	
        
        //$this->save_redirect( $_POST  );

	}
	
	function html() {
		do_action('sendpress_event','Create Email');
		$post = get_default_post_to_edit( SPNL()->_email_post_type, true );
		$post_ID = $post->ID;
	
		global $current_user;

		wp_enqueue_script('post');

		$post_type = SendPress_Data::email_post_type();
		$post_type_object = get_post_type_object( $post_type );

		?>
		<form method="POST" name="post" id="post">
		<div id="styler-menu">
			<div style="float:right;" class="btn-group">
				<input type="submit" value="<?php _e('Save & Next','sendpress'); ?>" class="btn btn-primary" />
			</div>
			<div id="sp-cancel-btn" style="float:right; ">
				<a href="<?php echo SendPress_Admin::link('Emails_Autoresponder'); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
			</div>
		</div>
		
		<h2><?php _e('Create Autoresponder','sendpress'); ?></h2>
		<br>
		<!--
		has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			
			<div class="clear"><br>
			<?php echo do_action('do_meta_boxes', SPNL()->_email_post_type, 'side', $post); 
			do_meta_boxes($post_type, 'side', $post);?>
			</div>
		</div>
		-->
		
		<input type="hidden" value="save-create" name="save-action" id="save-action" />
		<input type="hidden" value="save-email" name="action" />
		<input type="hidden" id="user-id" name="user_ID" value="<?php echo $current_user->ID; ?>" />
		<input type="hidden" value="default" name="target-location" id="target-location" />
		<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
		
			<!--
			<h2>Email Template Name</h2>
			-->
			<input type="hidden" name="post_title" size="30" tabindex="1" value="<?php  echo SendPress_Data::random_code();  //echo esc_attr( htmlspecialchars( $post->post_title ) ); ?>" id="title" autocomplete="off" />
		<!--<br><br>-->
			<!--<h2><?php _e('Subject','sendpress'); ?></h2>
			<input type="text" name="post_subject" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />-->
		<?php $this->panel_start('<span class="glyphicon glyphicon-envelope"></span> '.  __('Subject','sendpress') ); ?>
        <input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
        <?php $this->panel_end(  ); ?>
		

        
        	<div class="sp-row">
        		<div class="sp-50 sp-first">
			<?php $this->panel_start( __('Template','sendpress') ); ?>
			
			<select class="form-control" name="template">
			<?php
					$args = array(
					'post_type' => 'sp_template' ,
					'post_status' => array('sp-standard'),
					);

					$the_query = new WP_Query( $args );

					if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$temp_id = $the_query->post->ID;
						$s = '';
						if($temp_id == $template_id){
							$s = 'selected';
						}
						echo '<option value="'.$temp_id .'" '.$s.'>' . get_the_title() . '</option>';
					}
					
				}
			?>
			
			</select>
			<?php $this->panel_end(  ); ?>
			</div>
			<div class="sp-50">
			<?php $this->panel_start( __('Original Template','sendpress') ); ?>
			<?php $this->panel_end(  ); ?>
			</div>
			</div>

			
		<br><br>
		<?php //wp_editor($post->post_content,'textversion'); ?>

		 <?php wp_nonce_field($this->_nonce_value); ?><br><br>
		 </form>
		 
		<?php
	}

}
