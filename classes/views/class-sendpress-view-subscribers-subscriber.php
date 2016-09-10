<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Subscriber extends SendPress_View_Subscribers {

	function status_select($status, $listid){
		//$this->security_check();
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
    	//$this->security_check();
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
    	//$this->security_check();
    	if(SPNL()->validate->_string('delete-this-user') == 'yes'){
    		SendPress_Data::delete_subscriber(  SPNL()->validate->_int('subscriberID') );
    		$lid = SPNL()->validate->_int('listID');
    		if($lid > 0){
    			SendPress_Admin::redirect( 'Subscribers_Subscribers',array('listID'=>$lid) );
    		}else {
    			SendPress_Admin::redirect( 'Subscribers_All' );
    		}
    	}else {

			global $post;

			$subscriber_info=array(
				'email' => SPNL()->validate->_email('email'),
				'firstname' => SPNL()->validate->_string('firstname'),
				'lastname' => SPNL()->validate->_string('lastname'),
				'phonenumber' => SPNL()->validate->_string('phonenumber'),
				'salutation' => SPNL()->validate->_string('salutation')
				);
			SendPress_Data::update_subscriber(SPNL()->validate->_int('subscriberID'), $subscriber_info);

	    	$args = array( 'post_type' => 'sendpress_list','post_status' => array('publish','draft'),'posts_per_page' => 500, 'order'=> 'ASC', 'orderby' => 'title' );
			$postslist = get_posts( $args );
			foreach ( $postslist as $post ) :
			  setup_postdata( $post ); 
				$status = SPNL()->validate->_int($post->ID."-status");
				$sid = SPNL()->validate->_int('subscriberID');

				if( $status > 0 ){
					SendPress_Data::update_subscriber_status( $post->ID,$sid,$status );
				} else {
					SendPress_Data::remove_subscriber_status($post->ID,$sid);
				}
				$notifications = SendPress_Data::get_post_notification_types();

				if(isset($_POST[$post->ID."-pn"]) && array_key_exists($_POST[$post->ID."-pn"], $notifications) ){
					SendPress_Data::update_subscriber_meta($sid, 'post_notifications',$_POST[$post->ID."-pn"], $post->ID );
				}

			
			endforeach; 
			wp_reset_postdata();

			$have_custom_field = SPNL()->validate->_bool('have_custom_field');
			$custom_field_key = SPNL()->validate->_string('custom_field_key');

			// custom field save
			if ($have_custom_field) {
				SendPress_Data::update_subscriber_meta($sid, $custom_field_key, SPNL()->validate->_string('custom_field_value'), false);
			} else {
				SendPress_Data::add_subscriber_meta($sid, $custom_field_key, SPNL()->validate->_string('custom_field_value'), false, 0);
			}
		}
		$sid = SPNL()->validate->_int('subscriberID');
		SendPress_Admin::redirect('Subscribers_Subscriber', array('subscriberID'=>$sid  ) );
    	
    }


	function html() {
		?>
	<div id="taskbar" class="lists-dashboard rounded group">
		<form id="subscriber-edit" method="post">
	<div style="float:right;" >
	<input type="submit" class="btn btn-primary btn-large " id="subscriber-save" value="<?php _e('Save','sendpress'); ?>"/>
</div> 
		
	<h2><?php _e('Edit Subscriber','sendpress'); ?></h2>
	</div>
<?php
	$sub = SendPress_Data::get_subscriber(SPNL()->validate->_int('subscriberID'));
	
	?><div class="boxer">
	<div class="boxer-inner">
		<div class="spmedia">
			<div class="media-image">
		<?php
		echo get_avatar( $sub->email, $size = '96' ); 
		?>
		</div>
		<div class="media-body">
	
		<input type="hidden" name="listID" value="<?php echo SPNL()->validate->_int('listID'); ?>" />
	    <input type="hidden" name="subscriberID" value="<?php echo SPNL()->validate->_int('subscriberID'); ?>" />
	    <strong><?php _e('Email','sendpress'); ?></strong>: <input type="text" name="email" class="regular-text sp-text" value="<?php echo $sub->email; ?>" /><br><br>
	    <strong><?php _e('Salutation','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="salutation" value="<?php echo $sub->salutation; ?>" /><br>
	    <strong><?php _e('Firstname','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="firstname" value="<?php echo $sub->firstname; ?>" /><br><br>
	    <strong><?php _e('Lastname','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="lastname" value="<?php echo $sub->lastname; ?>" /><br>
	    <strong><?php _e('Phone Number','sendpress'); ?></strong>: <input type="text" class="regular-text sp-text" name="phonenumber" value="<?php echo $sub->phonenumber; ?>" /><br>

			<?php 
				$args = array(
					'post_type' => 'sp_settings',
					'meta_query' => array(
						array(
							'key'     => '_sp_setting_type',
							'value'   => 'custom_field',
							'compare' => '=',
						),
					)
				);
				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {
						$query->the_post();
						//$custom_field_label = get_the_title();
						$saved_post_id = get_the_ID();
						$custom_field_label = get_post_meta($saved_post_id, '_sp_custom_field_description', true);
						$custom_field_key = get_post_meta($saved_post_id, '_sp_custom_field_key', true);
					}
					/* Restore original Post Data */

					wp_reset_postdata();
					if (strlen($custom_field_label) > 0) {
						$have_custom_field = true;
						$subid = SPNL()->validate->_int('subscriberID');
						$custom_field_value = SendPress_Data::get_subscriber_meta($subid,$custom_field_key, false);

				?>

	   		<strong><?php echo $custom_field_label; ?></strong>: <input type="text" class="regular-text sp-text" name="custom_field_value" value="<?php echo $custom_field_value;?>" /><br>
	    	<br>
            <?php 
            		}
				} else {
					$custom_field_label = "";
					$saved_post_id = 0;
				}
			?>

<input type="hidden" name="have_custom_field" value="<?php echo $have_custom_field;?>" />
<input type="hidden" name="custom_field_key" value="<?php echo $custom_field_key;?>" />
<input type="checkbox" id="delete-this-user" name="delete-this-user" value="yes"/> Checking this box will remove this subscriber and all related data from the system.<br><br>

	  
	   <?php wp_nonce_field($this->_nonce_value); ?>

	
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
		$args = array( 'post_type' => 'sendpress_list','post_status' => array('publish','draft'),'posts_per_page' => 500, 'order'=> 'ASC', 'orderby' => 'title' );
		$postslist = get_posts( $args );
		foreach ( $postslist as $post ) :
		  setup_postdata( $post ); ?> 
			
				<tr>
					<td><?php the_title(); ?></td>
					<td><?php $info = SendPress_Data::get_subscriber_list_status($post->ID,  SPNL()->validate->_int('subscriberID'));
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
						$this->pn_select( SPNL()->validate->_int('subscriberID') , $post->ID);

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
<!--<h3>Subscriber Actions and Events</h3>-->
	<div class="well">
		<?php
		/*
		if(!defined("SENDPRESS_PRO_VERSION") ){
			_e('This feature requires SendPress Pro.','sendpress');
		} else {
			do_action('sendpress_subscriber_events_view', SPNL()->validate->_int('subscriberID') );
		}
		*/
		?>
	</div>
	

</div>
</div>



	<?php	
	}

}