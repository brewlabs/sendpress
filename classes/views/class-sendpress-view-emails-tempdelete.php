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

	
	
	function html($sp) {
		
		?>
		<?php 
		$template = get_post($_GET['templateID']);
		
	?>

		
		
		<h2>You are about to delete template: <?php echo $template->post_title; ?></h2>
		<br>
				<a class="btn btn-danger" href="<?php echo SendPress_Admin::link('Emails_Temp',array('templateID'=>$_GET['templateID'] , 'action'=>'delete' )); ?>">Delete Template</a>
			
				<a class="btn btn-default" href="<?php echo SendPress_Admin::link('Emails_Temp'); ?>">Cancel</a>
			

		
		
		<?php //wp_editor($post->post_content,'textversion'); ?>

		 <?php wp_nonce_field($sp->_nonce_value); ?><br><br>
		 </form>
		 
		<?php
	}

}
