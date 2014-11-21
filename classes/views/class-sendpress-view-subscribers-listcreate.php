<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listcreate extends SendPress_View_Subscribers {

	function save(){

		$name = $_POST['name'];
        $public = 0;
        if(isset($_POST['public']) && $_POST['sync_role'] == 'none'){
            $public = $_POST['public'];
        }
      

        $list_id = SendPress_Data::create_list( array('name'=> $name, 'public'=>$public ) );
        update_post_meta($list_id, 'sync_role', $_POST['sync_role']);
        SendPress_Admin::redirect('Subscribers');
	}
	
	function html($sp) {
		?>
	
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="list-create" method="post">
		<div id="button-area">  
		<input type="submit" value="<?php _e('Save List','sendpress'); ?>" class="btn btn-large btn-primary"/>
	</div>
	<h2><?php _e('Create List','sendpress'); ?></h2>
	
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="create-list" />
	    <p><input type="text" name="name" value="" /></p>
	    <p><input type="checkbox" class="edit-list-checkbox" name="public" value="1" checked /><label for="public"><?php _e('Allow user to sign up to this list','sendpress'); ?></label> <small>( <?php _e('synced lists will be made private','sendpress'); ?> )</small></p>
	    <!-- Now we can render the completed list table -->
	     <!-- Now we can render the completed list table -->
	   	
	   	 <?php 

	   	$roles = get_post_meta($listinfo->ID, 'sync_role', true);
	   	if($roles == false){
	   		$roles = 'none';
	   	}
	   	$d ='';
	   	if( $roles == 'none'){
	   	 		$d = 'checked';
	   	 	}
	   	?>
	   	<p><H4><?php _e('Select List Type','sendpress'); ?></h4>
	   		<?php _e('Pick SendPress list if you want to use SendPress to manage your subscribers. Pick a WordPress Role if you want to send emails to your users. <br>Synced lists can only have users that have a login to your WordPress site.','sendpress'); ?></p>
	   	<p>
	   	<input type="radio" name="sync_role" value="none" <?php echo $d; ?> /> <?php _e('SendPress List','sendpress'); ?> ( <?php _e('Use this to have subscribers sign up, import csv, etc.','sendpress'); ?> )<br><br>
	   	<?php
	   	 foreach (get_editable_roles() as $role_name => $role_info):
	   	 	$d ='';
	   	 	if( $role_name == $roles ){
	   	 		$d = 'checked';
	   	 	} ?>
    		<input type="radio" name="sync_role" value="<?php echo $role_name ?>" <?php echo $d; ?> /> <?php echo translate_user_role($role_info['name']); ?><br>
    		
       
  <?php endforeach; ?>
</p>
	  
	   	<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<?php
	}

}