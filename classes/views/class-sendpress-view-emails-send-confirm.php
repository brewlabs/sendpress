<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Emails_Send_Confirm extends SendPress_View_Emails {
	
  function save($post, $sp){
        $saveid = $_POST['post_ID'];

        update_post_meta( $saveid, 'send_date', date('Y-m-d H:i:s') );

        $email_post = get_post( $saveid );

        $subject = SendPress_Option::get('current_send_subject_'. $saveid);

        $info = SendPress_Option::get('current_send_'.$saveid);
        $slug = SendPress_Data::random_code();

        $new_id = SendPress_Posts::copy($email_post, $subject, $slug, $sp->_report_post_type );
        SendPress_Posts::copy_meta_info($new_id, $saveid);


        $count = 0;    

        if(isset($info['listIDS'])){
            foreach($info['listIDS'] as $list_id){
                $_email = $sp->get_active_subscribers( $list_id );

                foreach($_email as $email){
                   
                     $go = array(
                        'from_name' => 'Josh',
                        'from_email' => 'joshlyford@gmail.com',
                        'to_email' => $email->email,
                        'emailID'=> $new_id,
                        'subscriberID'=> $email->subscriberID,
                        //'to_name' => $email->fistname .' '. $email->lastname,
                        'subject' => $subject,
                        'listID'=> $list_id
                        );
                   
                    $sp->add_email_to_queue($go);
                    $count++;

                }


            }
        }


        if(isset($info['testemails'])){
            foreach($info['testemails'] as $email){
                   
                     $go = array(
                        'from_name' => 'Josh',
                        'from_email' => 'joshlyford@gmail.com',
                        'to_email' => $email['email'],
                        'emailID'=> $new_id,
                        'subscriberID'=> 0,
                        'subject' => $subject,
                        'listID' => 0
                        );
                   
                    $sp->add_email_to_queue($go);
                    $count++;

                


            }
        }

        update_post_meta($new_id,'_send_count', $count );
        update_post_meta($new_id,'_send_data', $info );

   
        SendPress_Admin::redirect('Queue');
        //wp_redirect( '?page=sp-queue' );

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
		<form  method="POST" name="post" id="post">
<?php
$info = SendPress_Option::get('current_send_'.$post->ID );
$subject = SendPress_Option::get('current_send_subject_'.$post->ID ,true);
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
        echo $list->post_title. " <small>(".SendPress_Data::get_count_subscribers($list_id). ")</small><br>";      

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
SendPress_Admin::add_cap('Emails_Send_Confirm','sendpress_email_send');
