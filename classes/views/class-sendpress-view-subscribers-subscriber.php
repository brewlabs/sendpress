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


    function pn_select( $sub_id, $listid ){
    	$pro_list = SendPress_Option::get('pro_notification_lists');
    	if(isset($pro_list['post_notifications']['id']) && $listid == $pro_list['post_notifications']['id'] ) {
		$current = SendPress_Data::get_subscriber_meta($sub_id,'post_notifications',$listid);
		$info = SendPress_Data::get_post_notification_types();				
		echo '<select name="'.$listid.'-pn">';
        echo "<option cls value='-1' >No Status</option>";
        foreach ($info as $key => $value) {

            $cls = '';
            if($current == $key){
                $cls = " selected='selected' ";
            }

           echo "<option $cls value='".$key."'>".$value."</option>";
        }

        
        echo '</select> ';




		}


    }


    function save(){
    	if(isset($_POST['delete-this-user']) && $_POST['delete-this-user'] == 'yes'){
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
				$notifications = SendPress_Data::get_post_notification_types();
				if(isset($_POST[$post->ID."-pn"]) && array_key_exists($_POST[$post->ID."-pn"], $notifications) ){
					SendPress_Data::update_subscriber_meta($_POST['subscriberID'], 'post_notifications',$_POST[$post->ID."-pn"], $post->ID );
				}


			endforeach; 
			wp_reset_postdata();

		}
		SendPress_Admin::redirect('Subscribers_Subscriber', array('subscriberID'=>$_POST['subscriberID'] ) );
    	
    }


	function html($sp) {
		?>
	<div id="taskbar" class="lists-dashboard rounded group">
		<form id="subscriber-edit" method="post">
	<div style="float:right;" >
	<input type="submit" class="btn btn-primary btn-large " id="subscriber-save" value="<?php _e('Save','sendpress'); ?>"/>
</div> 
		
	<h2><?php _e('Edit Subscriber','sendpress'); ?></h2>
	</div>
<?php
	$sub = SendPress_Data::get_subscriber($_GET['subscriberID']);
	
	?><div class="boxer">
	<div class="boxer-inner">
		<div class="spmedia">
			<div class="media-image">
		<?php
		echo get_avatar( $sub->email, $size = '96' ); 
		?>
		</div>
		<div class="media-body">
	
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <!--<input type="hidden" name="action" value="edit-subscriber" />-->
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <input type="hidden" name="subscriberID" value="<?php echo $_GET['subscriberID']; ?>" />
	    <strong><?php _e('Email','sendpress'); ?></strong>: <input type="text" name="email" class="regular-text sp-text" value="<?php echo $sub->email; ?>" /><br><br>
	    <strong><?php _e('Firstname','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="firstname" value="<?php echo $sub->firstname; ?>" /><br><br>
	    <strong><?php _e('Lastname','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="lastname" value="<?php echo $sub->lastname; ?>" /><br>
	    <br>
<input type="checkbox" id="delete-this-user" name="delete-this-user" value="yes"/> Checking this box will remove this subscriber and all related data from the system.<br><br>

	  
	   <?php wp_nonce_field($sp->_nonce_value); ?>

	
	</div></div>
	<?php 
	
	?>

	<h3>Subscriptions</h3>
	<div class="well">
		<table class=" table table-bordered table-striped">
			<tr>
				<th>List Name</th>
				<th>Status</th>
			</tr>
			<?php 
		global $post;
		$args = array( 'post_type' => 'sendpress_list','post_status' => array('publish','draft'),'posts_per_page' => 100, 'order'=> 'ASC', 'orderby' => 'title' );
		$postslist = get_posts( $args );
		foreach ( $postslist as $post ) :
		  setup_postdata( $post ); ?> 
			
				<tr>
					<td><?php the_title(); ?></td>
					<td><?php $info = SendPress_Data::get_subscriber_list_status($post->ID, $_GET['subscriberID']);
					if(isset($info) && $info !== false){
						$cls = '';
						if($info->statusid == 1){
							$cls = 'badge-warning';
						}
						if($info->statusid == 2){
							$cls = 'badge-success';
						}
						if($info->statusid == 3){
							$cls = 'badge-important';
						}
						if($info->statusid == 4){
							$cls = 'badge-inverse';
						}
						
						


						echo "<span class='badge $cls'>&nbsp;</span> ";
						$this->status_select($info->statusid,$post->ID);
						$this->pn_select( $_GET['subscriberID'] , $post->ID);

					} else {
						echo '<span class="badge">&nbsp;</span> '; $this->status_select(0,$post->ID);
					}

					 ?> </td>
				</tr>

			</div>
		<?php
		endforeach; 
		wp_reset_postdata();

		?></table>


	
	    		 
</div>
</form>
<h3>Subscriber Actions and Events</h3>
	<div class="well">
		<?php
		if(!defined("SENDPRESS_PRO_VERSION") ){
			_e('This feature requires SendPress Pro.','sendpress');
		} else {
			do_action('sendpress_subscriber_events_view', $_GET['subscriberID'] );
		}
		?>
	</div>

	


</div>
</div>



	<?php	
	}

}