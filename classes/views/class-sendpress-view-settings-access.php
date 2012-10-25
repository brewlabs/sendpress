<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if( !class_exists('SendPress_View_Settings_Access') ){

class SendPress_View_Settings_Access extends SendPress_View_Settings {

	function save() {
		
		foreach ($this->get_editable_roles() as $role) 
		{
			$sp_view = false;
			$k = get_role( strtolower($role['name']) );
			if( isset($_POST[$role['name'] . "_edit"] )){
				$sp_view = true;
				$k->add_cap('sendpress_email');
			} else {
				$k->remove_cap('sendpress_email');
			}

			if( isset($_POST[$role['name'] . "_send"] )){
				$sp_view = true;
				$k->add_cap('sendpress_email_send');
			} else {
				$k->remove_cap('sendpress_email_send');
			}

			if( isset($_POST[$role['name'] . "_reports"] )){
				$sp_view = true;
				$k->add_cap('sendpress_reports');
			} else {
				$k->remove_cap('sendpress_reports');
			}

			if( isset($_POST[$role['name'] . "_subscribers"] )){
				$sp_view = true;
				$k->add_cap('sendpress_subscribers');
			} else {
				$k->remove_cap('sendpress_subscribers');
			}

			if( isset($_POST[$role['name'] . "_settings"] )){
				$sp_view = true;
				$k->add_cap('sendpress_settings');
			} else {
				$k->remove_cap('sendpress_settings');
			}

			if( isset($_POST[$role['name'] . "_settings_access"] )){
				$sp_view = true;
				$k->add_cap('sendpress_settings_access');
			} else {
				$k->remove_cap('sendpress_settings_access');
			}

			if( isset($_POST[$role['name'] . "_addons"] )){
				$sp_view = true;
				$k->add_cap('sendpress_addons');
			} else {
				$k->remove_cap('sendpress_addons');
			}
			if( isset($_POST[$role['name'] . "_queue"] )){
				$sp_view = true;
				$k->add_cap('sendpress_queue');
			} else {
				$k->remove_cap('sendpress_queue');
			}

			if($sp_view == true){
				$k->add_cap('sendpress_view');
			}else{
				$k->remove_cap('sendpress_view');
			}

			
		}	

		//print_r();
		//echo self::link();
		//print_r(get_class( $this ));
		//self::n();
		//echo "asdf";
		self::redirect();
	}
	
	function html($sp) {
		?>
		<form method="post" id="post">

		<div style="float:right;" >
			<a href=" " class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
		</div>
		<br class="clear">
		<br class="clear">
		<table class=" table table-bordered table-striped">
			<tr>
				<th><?php _e('Role','sendpress'); ?></th>
				<th><?php _e('Emails','sendpress'); ?></th>
				<th><?php _e('Reports','sendpress'); ?></th>
				<th><?php _e('Subscribers','sendpress'); ?></th>
				<th><?php _e('Settings','sendpress'); ?></th>
				<th><?php _e('Queue','sendpress'); ?></th>
				<th><?php _e('Add-ons','sendpress'); ?></th>
			<tr>


		<?php
		foreach ($this->get_editable_roles() as $role) 
		{
			if($role['name'] != 'Administrator' && isset($role['capabilities']['edit_posts'])){
				echo "<tr>";
				echo "<td>". $role['name'] . "</td>";
				
				$k = get_role( strtolower($role['name']) );
				$checked = '';
				if( $k->has_cap('sendpress_email')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_edit'  > ".__('Create/Edit/Delete','sendpress') . "&nbsp;&nbsp;&nbsp;&nbsp;";
				$checked = '';
				if( $k->has_cap('sendpress_email_send')) {
					$checked = 'checked';
				}
				echo "<input $checked name='". $role['name'] . "_send' type='checkbox' >&nbsp;".__('Send','sendpress') ."</td>";
				$checked = '';
				if( $k->has_cap('sendpress_reports')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_reports' > ". __('Full Access','sendpress') ."</td>";
				$checked = '';
				if( $k->has_cap('sendpress_subscribers')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_subscribers' > ".__('Full Access','sendpress') ."</td>";
				$checked = '';
				if( $k->has_cap('sendpress_settings')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_settings'  > ".__('Full Access (<small>No Permissions Access</small>)','sendpress') ." ";
				/*
				$checked = '';
				if( $k->has_cap('sendpress_settings_access')) {
					$checked = 'checked';
				}
				echo "<input $checked name='". $role['name'] . "_settings_access' type='checkbox' >&nbsp;Access Controls</td>";
				*/
				$checked = '';
				if( $k->has_cap('sendpress_queue')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_queue'  > ". __('Full Access','sendpress') ."</td>";
			
				$checked = '';
				if( $k->has_cap('sendpress_addons')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role['name'] . "_addons'  > ".__('Full Access','sendpress') ."</td>";
				echo "</tr>";
				//print_r($role);
			}
		}
		echo "</table>";
		/*
		echo "<pre>";	
		foreach ($this->get_editable_roles() as $role) 
		{
			if($role['name'] != 'Administrator'){
				print_r($role);
			}
		}
		echo "</pre>";
		*/
		?>
<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
		<?php
	}

	function get_editable_roles() {
	    global $wp_roles;

	    $all_roles = $wp_roles->roles;
	    $editable_roles = apply_filters('editable_roles', $all_roles);

	    return $editable_roles;
	}
}

} //End Class Check

SendPress_View_Settings_Access::cap('sendpress_settings_access');
