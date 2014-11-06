<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Autoedit extends SendPress_View_Emails {
	
	

	function save_email(){
 		$post_id =  $_POST['post_ID'];

 		$myData = array(
 			'type' => $_POST['sp-autoresponder-type'],
 			'when' => $_POST['sp-timing'],
 			'delay' => $_POST['sp-delay']
 		);



 		SendPress_Option::email_set( 'autoresponder_' . $post_id  ,  $myData );
	}

	function admin_init(){
		global $is_IE;
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
		/*
		if (  ! wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
	}

	function html($sp) {
		global $is_IE;
		global $post_ID, $post;
		/*
		if (  wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
		$view = isset($_GET['view']) ? $_GET['view'] : '' ;

		if(isset($_GET['emailID'])){
			$emailID = $_GET['emailID'];
			$post = get_post( $_GET['emailID'] );
			$post_ID = $post->ID;
		}
	
        if($post->post_type !== 'sp_newsletters'){
            SendPress_Admin::redirect('Emails');
        }
      
        $options = SendPress_Option::email_get( 'autoresponder_' . $post_ID  );


		?>
     <form method="post" id="post" role="form">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="action" id="action" value="save-email" />
       <div  >
       <div style="float:right;" class="btn-toolbar">
            <div id="sp-cancel-btn" class="btn-group">
               <?php if($post->post_status != 'sp-autoresponder'  ) { ?>
                <a href="?page=<?php echo $_GET['page']; ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
            
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
        <h2>Autoresponder Settings</h2>
        <br>
        <h4>Subject: <?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?></h4>
        
        <div class="sp-row">
<div class="sp-75 sp-first">
<br>
<select name="sp-autoresponder-type" id="params-auto" >
<option value="subscribe"  <?php if($options['type']== 'subscribe' ){ echo "selected"; } ?> >When someone subscribes to the list...</option>
<!--<option value="user-new" <?php if($options['type']== 'user-new' ){ echo "selected"; } ?> >When a new WordPress user is added to your site...</option>-->
</select>
<select name="" id="params-list">
<?php
$post_args = array( 'post_type' => 'sendpress_list','numberposts'     => -1,
    	'offset'          => 0,
    	'orderby'         => 'post_title',
    	'order'           => 'DESC', );
		
$current_lists = get_posts( $post_args );
foreach($current_lists as $list){

     $t = '';
     $tlist = '';
       
	echo "<option value=" . $list->ID. "> ".$list->post_title . " <small>(".SendPress_Data::get_count_subscribers($list->ID). ")</small></option>";
}


?>
</select>
<input type="text"  name="sp-delay" style="width:30px; <?php if($options['when']== 'immediate' ){ echo "display:none;"; } ?>"   class="text" id="timer" value="<?php echo $options['delay']; ?>" />
<select name="sp-timing"  id="when-to-send" >
<option value="immediate" <?php if($options['when']== 'immediate' ){ echo "selected"; } ?>  >immediately.</option>
<option value="hours" <?php if($options['when']== 'hours' ){ echo "selected"; } ?>  >hour(s) after.</option>
<option value="days" <?php if($options['when']== 'days' ){ echo "selected"; } ?>  >day(s) after.</option>
<option value="weeks" <?php if($options['when']== 'weeks' ){ echo "selected"; } ?>  >week(s) after.</option>
</select>

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