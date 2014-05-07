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
        $lists = implode(',', $info['listIDS']);
        update_post_meta($new_id,'_send_time',  $info['send_at'] );
        update_post_meta($new_id,'_send_lists', $lists );
        $count = 0;    
        if(get_post_meta($saveid ,'istest',true) == true ){
            update_post_meta($new_id,'_report_type', 'test' );
        }

         update_post_meta($new_id ,'_sendpress_subject', $subject );

        /*

        if(isset($info['listIDS'])){
           // foreach($info['listIDS'] as $list_id){
                $_email = SendPress_Data::get_active_subscribers_lists($info['listIDS']); //$sp->get_active_subscribers( $list_id );

                foreach($_email as $email){
                   
                     $go = array(
                        'from_name' => '',
                        'from_email' => '',
                        'to_email' => $email->email,
                        'emailID'=> $new_id,
                        'subscriberID'=> $email->subscriberID,
                        //'to_name' => $email->fistname .' '. $email->lastname,
                        'subject' => '',
                        'listID'=> $email->listid
                        );
                   
                    $sp->add_email_to_queue($go);
                    $count++;

                }


          //  }
        }
            */

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
       // update_post_meta($new_id,'_send_data', $info );

   
        SendPress_Admin::redirect('Emails_Send_Queue',array('emailID'=> $new_id));
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
<div id="sp-cancel-btn" style="float:right; ">
<a class="btn btn-default" href="<?php echo '?page='.$_GET['page']. '&view=send&emailID='. $_GET['emailID']; ?>"><?php _e('Cancel Send','sendpress'); ?></a>&nbsp;
</div>
<h2><?php _e('Confirm Send','sendpress'); ?></h2>
<br>

<input type="hidden" id="user-id" name="user_ID" value="<?php //echo $current_user->ID; ?>" />
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<div class="boxer">
<div class="boxer-inner">
<?php $this->panel_start('<span class="glyphicon glyphicon-inbox"></span> '. __('Subject','sendpress')); ?>
<input type="text" class="form-control" value="<?php echo stripslashes(esc_attr( htmlspecialchars( $subject ) )); ?>" disabled />
<?php $this->panel_end(); ?>
<div class="leftcol">
<?php $this->panel_start( '<span class="glyphicon glyphicon-calendar"></span> '. __('Date & Time','sendpress')); ?>
<?php if($info['send_at'] == '0000-00-00 00:00:00') {
    echo "Your email will start sending right away!";
} else {
    echo "Your email will start sending on " .date('Y/m/d',strtotime($info['send_at'])) . " at " .date('h:i A',strtotime($info['send_at']))  ;
}?>
<?php $this->panel_end(); 
$this->panel_start('<span class="glyphicon glyphicon-list"></span> '. __('Lists','sendpress'));
?>



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
<?php $this->panel_end(); ?>
<?php
$this->panel_start('<span class="glyphicon glyphicon-tag"></span> '. __('Mark as Test','sendpress'));
    $sel = '';
    if(get_post_meta($post_ID ,'istest',true) == true ){
        $sel = 'checked';
    }
    echo "<input $sel name='test_report' type='checkbox' id='test_report' value='1' disabled> Test<br>";
    echo "<small class='text-muted'>This puts the report into the Test tab on the Reports screen.</small>";

$this->panel_end();
?>

</div>
<div style="margin-left: 250px;">
<div class="widerightcol">
<?php
$link =  get_permalink( $post->ID ); 
$sep = strstr($link, '?') ? '&' : '?';
$link = $link.$sep.'inline=true';

$open_info = array(
    "id"=>$post->ID,

    "view"=>"email"
);
$code = SendPress_Data::encrypt( $open_info );

$url =  SendPress_Manager::public_url($code);

$sep = strstr($url, '?') ? '&' : '?';
$link = $url.$sep.'inline=true';
?>
<iframe src="<?php echo $link; ?>" width="100%" height="600px"></iframe>

<small>Displaying a 404? Please try saving your permalinks <a href="<?php echo admin_url('options-permalink.php'); ?>">here</a>.</small>
</div>
<?php wp_nonce_field($sp->_nonce_value); ?><br><br>
</div>
</div>
<br class="clear" />
	</div>
	</form>
	<?php	
	} 

}
SendPress_Admin::add_cap('Emails_Send_Confirm','sendpress_email_send');
