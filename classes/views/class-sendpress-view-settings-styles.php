<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Settings_Styles extends SendPress_View_Settings {
	
	function html($sp) {
		global $post_ID, $post;

		$view = isset($_GET['view']) ? $_GET['view'] : '' ;

		$list ='';

		if(isset($_GET['emailID'])){
			$emailID = $_GET['emailID'];
			$post = get_post( $_GET['emailID'] );
			$post_ID = $post->ID;
		}
	


		?>
		<form action="admin.php?page=<?php echo $sp->_page; ?>" method="POST" name="post" id="post">
		<form method="post" id="post">
	<br class="clear">
<div style="float:right;" >
	<a href="?page=sp-templates" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<br class="clear">
		<?php require_once( SENDPRESS_PATH. 'inc/forms/email-style.2.0.php' ); ?>
		<input type="hidden" name="action" value="template-default-style" />
<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>
	<?php
	}

}