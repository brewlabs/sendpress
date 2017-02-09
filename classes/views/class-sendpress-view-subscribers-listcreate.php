<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listcreate extends SendPress_View_Subscribers {

	function save(){
		//$this->security_check();
		$name = sanitize_text_field(SPNL()->validate->_string('name'));
        $public = 0;
        if( SPNL()->validate->_isset('public') &&  SPNL()->validate->_string('sync_role') == 'none'){
            $public = SPNL()->validate->_int( 'public');
        }

        $list_id = SendPress_Data::create_list( array('name'=> $name, 'public'=> $public ) );
      	update_post_meta($list_id, '_test_list', SPNL()->validate->_string('test_list'));
		update_post_meta($list_id, 'sync_role', SPNL()->validate->_string('sync_role'));
		update_post_meta($list_id, 'meta-key', SPNL()->validate->_string('meta-key'));
		update_post_meta($list_id, 'meta-compare', SPNL()->validate->_string('meta-compare'));
		update_post_meta($list_id, 'meta-value',SPNL()->validate->_string('meta-value'));
		update_post_meta($list_id, 'opt-in-id', SPNL()->validate->_int('opt-in-id'));

        SendPress_Admin::redirect('Subscribers');
	}

	function html() {
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
	    <p><input type="checkbox" class="edit-list-checkbox" name="test_list" value="0"/><label for="public"><?php _e('Mark list as test. Adds <span class="label label-info">Test List</span> to list title every where.','sendpress'); ?></label></p>

	    <!-- Now we can render the completed list table -->
	     <!-- Now we can render the completed list table -->

	   	 <?php


	   $roles = 'none';
	   	 		$d = 'checked';


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

		<input type="radio" name="sync_role" value="meta"  <?php echo $d; ?> /> <?php _e('User Meta Query - Advanced','sendpress'); ?> ( <?php _e('Use this to sync a list based on user meta data.','sendpress'); ?> )<br><br>
   		<label>Meta Key</label>
			<input type="text" name="meta-key" value="" />
		<?php $this->select('meta-compare', '' , array( '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'EXISTS', 'NOT EXISTS') ); ?>

		<label>Meta Value</label>
			<input type="text" name="meta-value" value="" />


		<br><br>

		<?php

		$optin_emails = SendPress_Data::get_list_sys_emails('opt_in');
		// echo '<pre>';
		// print_r($optin_emails);

		// echo '</pre>';


		?>
		<label>Double Opt In E-mail</label>
		<select name="opt-in-id">
			<option value="0">Default</option>
			<?php

				foreach ($optin_emails as $key => $email) {

					?>
					<option value="<?php echo $email->ID; ?>"><?php echo get_post_meta($email->ID, '_sendpress_subject', true);?></option>
					<?php
				}
			?>
		</select>

</p>

	   	<?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}
