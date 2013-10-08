<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Emails_Send_Queue extends SendPress_View_Emails {
	
  function save($post, $sp){
       

  }




	function html($sp) {
		global $post_ID, $post;

        $view = isset($_GET['view']) ? $_GET['view'] : '' ;
        if(isset($_GET['finished']) ){
             SendPress_Admin::redirect('Queue');
        }


        $list ='';

        if(isset($_GET['emailID'])){
        	$emailID = $_GET['emailID'];
        	$post = get_post( $_GET['emailID'] );
        	$post_ID = $post->ID;
        }

		?><?php
 update_post_meta($post->ID,'_send_last',0);
$info = get_post_meta($post->ID, '_send_data', true);
$lists = get_post_meta($post->ID, '_send_lists', true);
$subject =$post->post_title;

$list = explode(",",$lists );

?>
        <div id="taskbar" class="lists-dashboard rounded group"> 

    <div id="button-area">  
    <a id="send-now" class="btn btn-primary btn-large " data-toggle="modal" href="#sendpress-sending"   ><i class="icon-white icon-refresh"></i> <?php _e('Send Emails Now','sendpress');?></a>
    </div>
</div><input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" /><input type="hidden" id="reporttoqueue" name="reporttoqueue" value="<?php echo $lists; ?>" />
<div class='well' id="confirm-queue-add">
    <h2><strong><?php _e('Adding Subscribers to Queue','sendpress'); ?></strong></h2><br>
   <!-- <p>email:  <?php echo stripslashes(esc_attr( htmlspecialchars( $subject ) )); ?></p>-->
    <div class="progress progress-striped active">
        <div class="bar sp-queueit" style="width: 0%;"></div>
    </div>
    <span id="queue-total">0</span> <?php _e('of', 'sendpres'); ?> <span id="list-total"><?php print_r( SendPress_Data::get_active_subscribers_count(  $list ) );?></span>
</div>
<script>
jQuery()
</script>
		<?php
	} 

}
SendPress_Admin::add_cap('Emails_Send_Queue','sendpress_email_send');
