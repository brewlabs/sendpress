<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Send extends SendPress_View_Emails {
	
	function html($sp) {

global $current_user;
global $post_ID, $post;

$view = isset($_GET['view']) ? $_GET['view'] : '' ;

$list ='';

if(isset($_GET['emailID'])){
	$emailID = $_GET['emailID'];
	$post = get_post( $_GET['emailID'] );
	$post_ID = $post->ID;
}


$post_type = $sp->_email_post_type;
$post_type_object = get_post_type_object($sp->_email_post_type);

?>
<form action="admin.php?page=<?php echo $sp->_page; ?>" method="POST" name="post" id="post">
<?php $sp->styler_menu('send'); ?>	
<input type="hidden" value="save-confirm-send" name="save-action" id="save-action" />
<input type="hidden" value="save-send" name="action" />
<input type="hidden" id="user-id" name="user_ID" value="<?php echo $current_user->ID; ?>" />
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<h2><?php _e('Send Email','sendpress'); ?></h2>
<div class="boxer">
<div class="boxer-inner">

<h2><?php _e('Subject','sendpress'); ?></h2>
<p><input type="text" name="post_subject" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" /></p>
<br>
<div class="leftcol">
		
		<div class="style-unit">
<h4><?php _e('Lists','sendpress'); ?></h4>
<?php
$post_args = array( 'post_type' => 'sendpress_list','numberposts'     => -1,
    	'offset'          => 0,
    	'orderby'         => 'post_title',
    	'order'           => 'DESC', );
		
$current_lists = get_posts( $post_args );
foreach($current_lists as $list){
	echo "<input name='listIDS[]' type='checkbox' id='listIDS' value=" . $list->ID. "> ".$list->post_title . " <small>(".SendPress_Data::get_count_subscribers($list->ID). ")</small><br>";
}
?>
</div>
<div class="style-unit">
<h4><?php _e('Test Emails','sendpress'); ?></h4>
<textarea name="test-add" cols='26' rows='10'></textarea>
<?php wp_nonce_field($sp->_nonce_value); ?><br><br>



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
</div>
</form>
<?php
	}

}
SendPress_Admin::add_cap('Emails_Send','sendpress_email_send');