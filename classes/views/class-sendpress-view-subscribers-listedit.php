<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listedit extends SendPress_View_Subscribers {

	function save(){
		$listid = $_POST['listID'];
        $name = $_POST['name'];
        $public = 0;
        if(isset($_POST['public']) && $_POST['sync_role'] == 'none'){
            $public = $_POST['public'];
        }
      
        SendPress_Data::update_list($listid, array( 'name'=>$name, 'public'=>$public ) );
        $roles_list = array();
        $test_list = 0;
        if(isset($_POST['test_list'])){
            $public = $_POST['test_list'];
        }
      
        update_post_meta($listid, '_test_list', $_POST['test_list']);
		update_post_meta($listid, 'sync_role', $_POST['sync_role']);
      	SendPress_Admin::redirect('Subscribers');
	}
	
	function html($sp) {
		
	$list ='';
	if(isset($_GET['listID'])){
		//$listinfo = $this->getDetail( $this->lists_table(),'listID', $_GET['listID'] );	
		$listinfo = get_post($_GET['listID']);
		$list = '&listID='.$_REQUEST['listID'];
		$listname = 'for '. $listinfo->post_title;
	}
	?>
	<form id="list-edit" method="post">
	<div id="button-area">  
		<input type="submit" value="<?php _e('Save List','sendpress'); ?>" class="btn btn-large btn-primary"/>
	</div>
	<h2><?php _e('Edit List','sendpress'); ?></h2>
	
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	   
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <p><input type="text" class="form-control" name="name" value="<?php echo $listinfo->post_title; ?>" /></p>
	    <p><input type="checkbox" class="edit-list-checkbox" name="public" value="<?php echo get_post_meta($listinfo->ID,'public',true); ?>" <?php if( get_post_meta($listinfo->ID,'public',true) == 1 ){ echo 'checked'; } ?> /><label for="public"><?php _e('Allow user to sign up to this list','sendpress'); ?></label></p>
	     <p><input type="checkbox" class="edit-list-checkbox" name="test_list" value="<?php echo get_post_meta($listinfo->ID,'_test_list',true); ?>" <?php if( get_post_meta($listinfo->ID,'_test_list',true) == 1 ){ echo 'checked'; } ?> /><label for="public"><?php _e('Mark list as test. Adds <span class="label label-info">Test List</span> to list title every where.','sendpress'); ?></label></p>
	    <!-- Now we can render the completed list table -->
	   	  	<p><H4>Select List Type</h4>
	   		Pick SendPress list if you want to use SendPress to manage your subscribers. Pick a WordPress Role if you want to send emails to your users. <br>Synced lists can only have users that have a login to your WordPress site.</p>
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
	   	<input type="radio" name="sync_role" value="none" <?php echo $d; ?> /> SendPress List ( Use this to have subscribers sign up, import csv, etc.  )<br><br>
	   	<?php
	   	 foreach (get_editable_roles() as $role_name => $role_info):
	   	 	$d ='';
	   	 	if( $role_name == $roles ){
	   	 		$d = 'checked';
	   	 	} ?>
    		<input type="radio" name="sync_role" value="<?php echo $role_name ?>" <?php echo $d; ?> /> <?php echo translate_user_role($role_info['name']); ?><br>
    		
       
  <?php endforeach; ?>
	   	<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<?php
	}

}