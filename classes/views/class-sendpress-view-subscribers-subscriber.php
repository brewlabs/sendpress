<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Subscriber extends SendPress_View_Subscribers {


	function status_select($status, $listid){
        $info = SendPress_Data::get_statuses();
        echo '<select name="'.$listid.'-status">';
        echo "<option cls value='-1' >No Status</option>";
        foreach ($info as $list) {
            $cls = '';
            if($status == $list->statusid){
                $cls = " selected='selected' ";
            }

           echo "<option $cls value='".$list->statusid."'>".$list->status."</option>";
        }

        
        echo '</select> ';
    }	

    function save(){
    	if($_POST['delete-this-user'] == 'yes'){
    		SendPress_Data::delete_subscriber( $_POST['subscriberID'] );
    		if($_GET['listID']){
    			SendPress_Admin::redirect( 'Subscribers_Subscribers',array('listID'=>$_GET['listID']) );
    		}else {
    		SendPress_Admin::redirect( 'Subscribers_All' );
    		}
    	}else {

			global $post;

			$subscriber_info=array(
				'email' => $_POST['email'],
				'firstname' => $_POST['firstname'],
				'lastname' => $_POST['lastname'],

				);
			SendPress_Data::update_subscriber($_POST['subscriberID'], $subscriber_info);

	    	$args = array( 'post_type' => 'sendpress_list','post_status' => array('publish','draft'),'posts_per_page' => 100, 'order'=> 'ASC', 'orderby' => 'title' );
			$postslist = get_posts( $args );
			foreach ( $postslist as $post ) :
			  setup_postdata( $post ); 

				if(isset($_POST[$post->ID."-status"]) && $_POST[$post->ID."-status"] > 0 ){
					SendPress_Data::update_subscriber_status( $post->ID,$_POST['subscriberID'],$_POST[$post->ID."-status"]  );
				} else {
					SendPress_Data::remove_subscriber_status($post->ID,$_POST['subscriberID']);
				}


			endforeach; 
			wp_reset_postdata();

		}
		SendPress_Admin::redirect('Subscribers_Subscriber', array('subscriberID'=>$_POST['subscriberID'] ) );
    	
    }

	

	function html($sp) {
		?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Edit Subscriber','sendpress'); ?></h2>
	</div>
<?php
	$sub = SendPress_Data::get_subscriber($_GET['subscriberID'],$_GET['listID']);
	
	?>
	<form id="subscriber-edit" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="edit-subscriber" />
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <input type="hidden" name="subscriberID" value="<?php echo $_GET['subscriberID']; ?>" />
	    <span class="sublabel"><?php _e('Email','sendpress'); ?>:</span> <input type="text" name="email" class="regular-text sp-text" value="<?php echo $sub->email; ?>" /><br>
	    <span class="sublabel"><?php _e('Firstname','sendpress'); ?>:</span> <input type="text" name="firstname" value="<?php echo $sub->firstname; ?>" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Lastname','sendpress'); ?>:</span> <input type="text" name="lastname" value="<?php echo $sub->lastname; ?>" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Status','sendpress'); ?>:</span> <select name="status">
			<?php 
				$results = $sp->getData($sp->subscriber_status_table());
				foreach($results as $status){
					$selected = '';
					if($status->status == $sub->status){
						$selected = 'selected';
					}
					echo "<option value='$status->statusid' $selected>$status->status</option>";

				}


			?>

	    </select>
	    <br>
	   <input type="submit" class="btn btn-primary" value="<?php _e('submit','sendpress'); ?>"/>
	   <?php wp_nonce_field($sp->_nonce_value); ?>

	</form>
	<?php	
	}

}