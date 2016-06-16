<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Emails_Send_Cancel extends SendPress_View_Emails {
	
  function save($post, $sp){
    //$this->security_check();
    $value = SPNL()->validate->_string( 'submit');
    
    if($value == 'delete'){
        $p =SPNL()->validate->_int( 'post_ID');
        if($p > 0){
            SendPress_Data::remove_from_queue($p);
            update_post_meta( $p ,'_canceled' , true);
        }
    }
    SendPress_Admin::redirect('Reports');
  }




	function html() {
		global $post_ID, $post;
        $emailID = SPNL()->validate->_int('emailID');
        if($emailID > 0){
        	
        	$post = get_post( $emailID );
        	$post_ID = $post->ID;
        }


?>
<form method="post">
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<h2><?php _e('Cancel Scheduled Email','sendpress'); ?></h2>
<div class='well'>
    <?php
    $info = get_post_meta($post->ID, '_send_time', true);
    ?>
   <p><?php _e('Subject','sendpress'); ?>: <?php echo $post->post_title; ?></p>
   <p><?php _e('Date','sendpress'); ?>: <?php echo date_i18n('Y/m/d @ h:i A' , strtotime( $info ) ); ?></p>
    <?php SendPress_Data::nonce_field(); ?>
    <button class="btn" value="cancel" name="submit"><?php _e('Cancel','sendpress'); ?></button>
    <button class="btn btn-danger" value="delete" name="submit"><?php _e('Delete Scheduled Email','sendpress'); ?></button>
</div>
</form>
		<?php
	} 

}
SendPress_Admin::add_cap('Emails_Send_Cancel','sendpress_email_send');
