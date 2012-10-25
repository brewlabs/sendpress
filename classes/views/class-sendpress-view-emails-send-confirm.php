<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Emails_Send_Confirm extends SendPress_View_Emails {
	
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
<?php
$info = $sp->get_option('current_send_'.$post->ID );
$subject = $sp->get_option('current_send_subject_'.$post->ID ,true);
?>
<div id="styler-menu">
    <div style="float:right;" class="btn-group">
<a class="btn btn-primary btn-large " id="confirm-send" href="#"><i class="icon-white  icon-thumbs-up"></i> <?php _e('Confirm Send','sendpress'); ?></a>
  </div>
</div>
<div id="sp-cancel-btn" style="float:right; margin-top: 5px;">
<a class="btn" href="<?php echo '?page='.$_GET['page']. '&view=send-email&emailID='. $_GET['emailID']; ?>"><?php _e('Cancel Send','sendpress'); ?></a>&nbsp;
</div>
<h2><?php _e('Confirm Send','sendpress'); ?></h2>

<input type="hidden" value="save-send-confirm" name="action" />
<input type="hidden" id="user-id" name="user_ID" value="<?php //echo $current_user->ID; ?>" />
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<div class="boxer">
<div class="boxer-inner">
<h2><strong><?php _e('Subject','sendpress'); ?></strong>: <?php echo stripslashes(esc_attr( htmlspecialchars( $subject ) )); ?></h2><br>
<div class="leftcol">
    
    <div class="style-unit">
<h4><?php _e('Lists','sendpress'); ?></h4>

<?php

if( !empty($info['listIDS']) ){
    foreach($info['listIDS'] as $list_id){
        $list = $sp->get_list_details( $list_id );
        echo $list->post_title.'<br>';      

    } 
} else {
   	_e('No Lists Selected','sendpress');
   	echo "<br>";
}


?>
</div>
<div class="style-unit">
<h4><?php _e('Test Emails','sendpress') ?></h4>
<?php


if( !empty($info['testemails']) ){
    foreach($info['testemails'] as $test){
       echo $test['email'] .'<br>';   

    } 
} else {
    _e('No Test Emails added','sendpress');
    echo "<br>";
}


?>
</div>
</div>
<div class="widerightcol">
<?php
$link =  get_permalink( $post->ID ); 
$sep = strstr($link, '?') ? '&' : '?';
$link = $link.$sep.'inline=true';
?>
<iframe src="<?php echo $link; ?>" width="100%" height="600px"></iframe>
</div>
<?php wp_nonce_field($sp->_nonce_value); ?><br><br>
</div>
</div>
	
	</form>
	<?php	
	} 

}
SendPress_View_Emails_Send::cap('sendpress_email_send');