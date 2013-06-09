<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Style extends SendPress_View_Emails {
	
	function admin_init(){
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
	}

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
		<!--
		<div style="float:left">
			<a href="?page=sp-emails" class="spbutton supersize" >Edit Content</a>
		</div>
		-->
		<?php $sp->styler_menu('style'); ?>	
		<?php require_once( SENDPRESS_PATH. 'inc/forms/email-style.2.0.php' ); ?>
		</form>
	<?php
	}

}