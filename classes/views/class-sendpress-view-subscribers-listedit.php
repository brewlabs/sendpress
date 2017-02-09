<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listedit extends SendPress_View_Subscribers {

	function save(){
		//$this->security_check();
		$listid = SPNL()->validate->_int( 'listID' );
		if($listid > 0 ){
        $name = sanitize_text_field(SPNL()->validate->_string('name'));
        $public = 0;
        if( SPNL()->validate->_isset('public') &&  SPNL()->validate->_string('sync_role') == 'none'){
            $public = SPNL()->validate->_int( 'public');
        }
      
        SendPress_Data::update_list($listid, array( 'name'=>$name, 'public'=>$public ) );
        
        update_post_meta($listid, '_test_list', SPNL()->validate->_string('test_list'));
		update_post_meta($listid, 'sync_role', SPNL()->validate->_string('sync_role'));
		update_post_meta($listid, 'meta-key', SPNL()->validate->_string('meta-key'));
		update_post_meta($listid, 'meta-compare', SPNL()->validate->_string('meta-compare'));
		update_post_meta($listid, 'meta-value',SPNL()->validate->_string('meta-value'));
		update_post_meta($listid, 'opt-in-id', SPNL()->validate->_int('opt-in-id'));
		}
      	SendPress_Admin::redirect('Subscribers');
	}
	
	function html() {
		
	$list ='';
	$listId = SPNL()->validate->_int( 'listID' );
	if($listId > 0){
		$listinfo = get_post( $listId );
		$list = '&listID='.$listId;
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
	   
	    <input type="hidden" name="listID" value="<?php echo $listId; ?>" />
	    <p><input type="text" class="form-control" name="name" value="<?php echo $listinfo->post_title; ?>" /></p>
	    <p><input type="checkbox" class="edit-list-checkbox" name="public" value="<?php echo get_post_meta($listinfo->ID,'public',true); ?>" <?php if( get_post_meta($listinfo->ID,'public',true) == 1 ){ echo 'checked'; } ?> /><label for="public"><?php _e('Allow user to sign up to this list','sendpress'); ?></label></p>
	     <p><input type="checkbox" class="edit-list-checkbox" name="test_list" value="<?php echo get_post_meta($listinfo->ID,'_test_list',true); ?>" <?php if( get_post_meta($listinfo->ID,'_test_list',true) == 1 ){ echo 'checked'; } ?> /><label for="public"><?php _e('Mark list as test. Adds <span class="label label-info">Test List</span> to list title every where.','sendpress'); ?></label></p>
	    <!-- Now we can render the completed list table -->
	   	  	<p><H4><?php _e('Select List Type','sendpress'); ?></h4>
	   		<?php _e('Pick SendPress list if you want to use SendPress to manage your subscribers. Pick a WordPress Role if you want to send emails to your users. <br>Synced lists can only have users that have a login to your WordPress site.','sendpress'); ?></p>
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
	   	<input type="radio" name="sync_role" value="none" <?php echo $d; ?> /> <?php _e('SendPress List','sendpress'); ?> ( <?php _e('Use this to have subscribers sign up, import csv, etc.','sendpress'); ?> )<br><br>
	   	<?php
	   	 foreach (get_editable_roles() as $role_name => $role_info):
	   	 	$d ='';
	   	 	if( $role_name == $roles ){
	   	 		$d = 'checked';
	   	 	} ?>
    		<input type="radio" name="sync_role" value="<?php echo $role_name ?>" <?php echo $d; ?> /> <?php echo translate_user_role($role_info['name']); ?><br>
    		
       
		<?php endforeach; ?>
			<?php 
		if( $roles == 'meta'){
   	 		$d = 'checked';
   	 	} else {
   	 		$d = '';
   	 	}
		?>

		<input type="radio" name="sync_role" value="meta"  <?php echo $d; ?> /> <?php _e('User Meta Query - Advanced','sendpress'); ?> ( <?php _e('Use this to sync a list based on user meta data.','sendpress'); ?> )<br><br>
   		<label>Meta Key</label>
			<input type="text" name="meta-key" value="<?php echo get_post_meta($listinfo->ID, 'meta-key', true); ?>" />
		<?php $this->select('meta-compare', get_post_meta($listinfo->ID, 'meta-compare', true) , array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'EXISTS', 'NOT EXISTS') ); ?>

		<label>Meta Value</label>
		<input type="text" name="meta-value" value="<?php echo get_post_meta($listinfo->ID, 'meta-value', true); ?>" />

		<br><br>
		
		<?php 

		$optin_emails = SendPress_Data::get_list_sys_emails('opt_in');
		$current_opt_in_id = get_post_meta($listinfo->ID, 'opt-in-id', true);
		?>
		<label>Double Opt In E-mail</label>
		<select name="opt-in-id">
			<option value="0">Default</option>
			<?php

				foreach ($optin_emails as $key => $email) {
					
					?>
					<option value="<?php echo $email->ID; ?>"<?php if( intval($current_opt_in_id) === intval($email->ID) ){echo ' selected';} ?>><?php echo get_post_meta($email->ID, '_sendpress_subject', true);?></option>
					<?php
				}
			?>
		</select>

	   	<?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}