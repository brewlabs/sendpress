<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Emails_Send_Cancel extends SendPress_View_Emails {
	
  function save($post, $sp){
    $value = $_POST['submit'];
    
    if($value == 'delete'){
        SendPress_Data::remove_from_queue($_POST['post_ID']);
        update_post_meta( $_POST['post_ID'] ,'_canceled' , true);
    }
    SendPress_Admin::redirect('Reports');
  }




	function html($sp) {
		global $post_ID, $post;

        $view = isset($_GET['view']) ? $_GET['view'] : '' ;
       



        if(isset($_GET['emailID'])){
        	$emailID = $_GET['emailID'];
        	$post = get_post( $_GET['emailID'] );
        	$post_ID = $post->ID;
        }


?>
<form method="post">
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<h2>Cancel Scheduled Email</h2>
<div class='well'>
    <?php
    $info = get_post_meta($post->ID, '_send_time', true);
    ?>
   <p>Subject: <?php echo $post->post_title; ?></p>
   <p>Date: <?php echo date_i18n('Y/m/d @ h:i A' , strtotime( $info ) ); ?></p>
    <?php SendPress_Data::nonce_field(); ?>
    <button class="btn" value="cancel" name="submit">Cancel</button>
    <button class="btn btn-danger" value="delete" name="submit">Delete Scheduled Email</button>
</div>
</form>
		<?php
	} 

}
SendPress_Admin::add_cap('Emails_Send_Cancel','sendpress_email_send');
