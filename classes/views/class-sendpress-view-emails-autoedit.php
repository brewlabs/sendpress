<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Autoedit extends SendPress_View_Emails {
	
	

	function save_email(){
		//$this->security_check();
 		$post_id =  SPNL()->validate->_int('post_ID');
 		if($post_id > 0){
	 		
	 		$myData = array(
	 			'action_type' => SPNL()->validate->_string('sp-autoresponder-type'),
	 			'when_to_send' => SPNL()->validate->_string('when-to-send'),
	 			'delay_time' => SPNL()->validate->_int('sp-delay'),
	 			'post_id' => $post_id,
	 			'list_id' => SPNL()->validate->_int('sp-autoresponser-list')
	 		);
	 		SPNL()->load('Autoresponder')->add($myData);
	 		//SendPress_Option::email_set( 'autoresponder_' . $post_id  ,  $myData );
 		}
	}

	function admin_init(){
		global $is_IE;
		remove_filter('the_editor','qtrans_modifyRichEditor');
		/*
		if (  ! wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
	}

	function html() {
		global $is_IE;
		global $post_ID, $post;
		/*
		if (  wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
		$emailID = SPNL()->validate->_int('emailID');
		if( $emailID > 0 ){
			$post = get_post( $emailID );
			$post_ID = $post->ID;
		}

		$auto = SPNL()->load('Autoresponder')->get( $emailID  );
		if($post->post_type !== 'sp_newsletters'){
            SendPress_Admin::redirect('Emails');
        }
      
        $options = array();// = SendPress_Option::email_get( 'autoresponder_' . $post_ID  );
        
		?>
     <form method="post" id="post" role="form">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="action" id="action" value="save-email" />
       <div>
       <div style="float:right;" class="btn-toolbar">
            <div id="sp-cancel-btn" class="btn-group">
            <?php if($post->post_status != 'sp-autoresponder'  ) { ?>
                <a href="?page=<?php echo SPNL()->validate->page(); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
            
            <?php 
            } else { ?>
     		<a href="<?php echo SendPress_Admin::link('Emails_Autoresponder'); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
           
            <?php } ?>
            </div>
            <div class="btn-group">
            
            <button class="btn btn-primary " type="submit" value="save-next" name="submit"><i class="icon-envelope icon-white"></i> <?php echo __('Save','sendpress'); ?></button>
            </div>
        </div>
	

</div>
        <h2><?php _e('Autoresponder Settings','sendpress'); ?></h2>
        <br>
        <h4><?php _e('Subject','sendpress'); ?>: <?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?></h4>
        
        <div class="sp-row">
<div class="sp-75 sp-first">
<br>
<select name="sp-autoresponder-type" id="params-auto" >
<option value="subscribe"  <?php if($options['type']== 'subscribe' ){ echo "selected"; } ?> ><?php _e('When someone subscribes to the list','sendpress'); ?>...</option>
<option value="user-new" <?php if($options['type']== 'user-new' ){ echo "selected"; } ?> ><?php _e('When a new WordPress user is added to your site','sendpres'); ?>...</option>
<option value="user-click" <?php if($options['type']== 'user-click' ){ echo "selected"; } ?> ><?php _e('When a subscriber clicks a link','sendpres'); ?>...</option>
</select>
<select name="sp-autoresponser-list" id="params-list">
<?php
$post_args = array( 'post_type' => 'sendpress_list','numberposts'     => -1,
    	'offset'          => 0,
    	'orderby'         => 'post_title',
    	'order'           => 'DESC');
		
$current_lists = get_posts( $post_args );
foreach($current_lists as $list){

     $t = '';
     $selected = '';
     $tlist = '';
     if($auto->list_id == $list->ID )
     { 
     	$selected = " selected "; 
 	} 
	echo "<option $selected value=" . $list->ID. "> ".$list->post_title . " <small>(".SendPress_Data::get_count_subscribers($list->ID). ")</small></option>";
}


?>
</select>
<input type="text"  name="sp-delay" style="width:30px; <?php if($auto->when_to_send == 'immediate' ){ echo "display:none;"; } ?>"   class="text" id="timer" value="<?php echo $auto->delay_time; ?>" />
<?php
$opts = array(
	array ('immediate','immediately.'),
	array ('hours','hour(s) after.'),
	array ('days','day(s) after.'),
	array ('weeks','week(s) after.')
);
?>
<?php $this->select('when-to-send',$auto->when_to_send, $opts  ); ?> 




<br><br>
</div>
<div class="sp-25">
<br><br>

</div>
</div>
<script>
(function($){
	$(document).ready(function($){
		$('#params-auto').change(function(){
			var it = $(this);
			var v = it.val();
			
		});


		$('#when-to-send').change(function(){
			var it = $(this);
			var v = it.val();
			if(v !== 'immediate'){
				$('#timer').show();
			} else {
				$('#timer').hide();
			}


		});
	});


}(jQuery));


</script>




	<?php SendPress_Data::nonce_field(); ?>
        </form>
	<?php
	}

}