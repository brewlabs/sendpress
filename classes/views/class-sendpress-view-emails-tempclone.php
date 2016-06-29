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
class SendPress_View_Emails_Tempclone extends SendPress_View_Emails {

	function save(){
		//$this->security_check();
		$t_id = SPNL()->validate->_int('templateID');
  		$postdata = get_post( $t_id );
  		$new_post = SendPress_Posts::copy($postdata, SPNL()->validate->_string('post_title'), '' , '');
  		SendPress_Posts::copy_meta_info($new_post, $t_id  );
  		update_post_meta($new_post, '_template_type','clone');
  		update_post_meta($new_post, '_guid','');
  		SendPress_Admin::redirect('Emails_Tempstyle', array('templateID' => $new_post ) );
	}
	
	function html() {
		  global $sendpress_html_templates;

$t_id = SPNL()->validate->_int('templateID');
    $postdata = get_post( $t_id  );
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
		
		<h2><?php _e('Clone Template','sendpress'); ?> - <?php echo $postdata->post_title; ?></h2>
		<br>
	
			<?php $this->panel_start('<span class="glyphicon glyphicon-list-alt"></span> '. __('Template Name','sendpress')); ?>
			<input type="text" class="form-control" name="post_title" size="30" tabindex="1" value="" id="post_title" autocomplete="off" />
<?php
$this->panel_end();
?>
		<!--
			
<div class="sp-row">
	<div class="sp-25 sp-first">
		<?php $this->panel_start('<input type="radio" name="starter" value="orginal" checked /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="blank"  /> Blank Template' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="default"  /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	<div class="sp-25">
		<?php $this->panel_start('<input type="radio" name="starter" value="default"  /> SP Original' ); ?>
		<?php $this->panel_end(); ?>
	</div>
	
</div>-->


		
		
		<?php //wp_editor($post->post_content,'textversion'); ?>

		 <?php wp_nonce_field($this->_nonce_value); ?><br><br>
		 </form>
		 
		<?php
	}

}
