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
class SendPress_View_Emails_Tempdelete extends SendPress_View_Emails {

	
	
	function html() {
		
		?>
		<?php 
		$t_id = SPNL()->validate->_int('templateID');
		$template = get_post( $t_id );
		
	?>

		
		
		<h2><?php _e('You are about to delete template','sendpress'); ?>: <?php echo $template->post_title; ?></h2>
		<br>
				<a class="btn btn-danger" href="<?php echo SendPress_Admin::link('Emails_Temp',array('templateID'=>$t_id , 'action'=>'delete' )); ?>"><?php _e('Delete Template','sendpress'); ?></a>
			
				<a class="btn btn-default" href="<?php echo SendPress_Admin::link('Emails_Temp'); ?>"><?php _e('Cancel','sendpress'); ?></a>
			

		
		
		<?php //wp_editor($post->post_content,'textversion'); ?>

		 <?php wp_nonce_field($this->_nonce_value); ?><br><br>
		 </form>
		 
		<?php
	}

}
