<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Settings_Access') ){

class SendPress_View_Settings_Access extends SendPress_View_Settings {

	function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

	function save() {
		  
		foreach ($this->get_editable_roles() as $role => $role_name) 
		{
			if($role != 'administrator'){
			$sp_view = false;

			//$role = str_replace(" ","_", strtolower( $role)  );

			/*
			$pos = strrpos($role, "s2member");

			if($pos !== false){
				$role = $this->str_lreplace(" ", "", $role);
			}
			*/
			$saverole  = get_role( $role );
					

			if(false !== get_class($saverole)){

				
			if( isset($_POST[$role . "_edit"] )){
				$sp_view = true;
				$saverole->add_cap( 'sendpress_email');
			} else {
				$saverole->remove_cap('sendpress_email');
			}

			if( isset($_POST[$role . "_send"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_email_send');
			} else {
				$saverole->remove_cap('sendpress_email_send');
			}

			if( isset($_POST[$role . "_reports"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_reports');
			} else {
				$saverole->remove_cap('sendpress_reports');
			}

			if( isset($_POST[$role . "_subscribers"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_subscribers');
			} else {
				$saverole->remove_cap('sendpress_subscribers');
			}

			if( isset($_POST[$role . "_settings"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_settings');
			} else {
				$saverole->remove_cap('sendpress_settings');
			}

			if( isset($_POST[$role . "_settings_access"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_settings_access');
			} else {
				$saverole->remove_cap('sendpress_settings_access');
			}

			if( isset($_POST[$role . "_addons"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_addons');
			} else {
				$saverole->remove_cap('sendpress_addons');
			}
			if( isset($_POST[$role . "_queue"] )){
				$sp_view = true;
				$saverole->add_cap('sendpress_queue');
			} else {
				$saverole->remove_cap('sendpress_queue');
			}

			if($sp_view == true){
				$saverole->add_cap('sendpress_view');
			}else{
				$saverole->remove_cap('sendpress_view');
			}
			}
			}
			
		}	

		
		//SendPress_Admin::redirect('Settings_Access');
	}
	
	function html($sp) {
		?>
		<form method="post" id="post">
<!--
<div style="float:right;" >
	<a href="" class="btn btn-large btn-default" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
-->
		<br class="clear">
		<br class="clear">
		<table class="table table-bordered table-striped">
			<tr>
				<th><?php _e('Role','sendpress'); ?></th>
				<th><?php _e('Emails','sendpress'); ?></th>
				<th><?php _e('Reports','sendpress'); ?></th>
				<th><?php _e('Subscribers','sendpress'); ?></th>
				<th><?php _e('Settings','sendpress'); ?></th>
				<th><?php _e('Queue','sendpress'); ?></th>
				<th><?php _e('Add-ons','sendpress'); ?></th>
			</tr>


		<?php
		foreach ($this->get_editable_roles() as $role => $role_name) 
		{
			if($role != 'administrator'){
				
			//$role = str_replace(" ","_", strtolower( $role)  );

			/*
			$pos = strrpos($role, "s2member");
		
			if($pos !== false){
				$role = $this->str_lreplace("_", "", $role);
			}

			echo $role . "<br>";
			*/
			//$saverole  = get_role( $role );
				


				$listrole = get_role( str_replace(" ","_", strtolower( $role)  ) );
				//$role =  str_replace(" ","_", strtolower( $role)  );
				$checked = '';




				if(false !== get_class($listrole)){
				echo "<tr>";
				echo "<td>". $role_name . "</td>";
				
				
				
				if( $listrole->has_cap('sendpress_email')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_edit'  > ".__('Create/Edit/Delete','sendpress') . "&nbsp;&nbsp;&nbsp;&nbsp;";
				$checked = '';
				if( $listrole->has_cap('sendpress_email_send')) {
					$checked = 'checked';
				}
				echo "<input $checked name='". $role . "_send' type='checkbox' >&nbsp;".__('Send','sendpress') ."</td>";
				$checked = '';
				if( $listrole->has_cap('sendpress_reports')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_reports' > ". __('Full Access','sendpress') ."</td>";
				$checked = '';
				if( $listrole->has_cap('sendpress_subscribers')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_subscribers' > ".__('Full Access','sendpress') ."</td>";
				$checked = '';
				if( $listrole->has_cap('sendpress_settings')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_settings'  > ".__('Full Access (<small>No Permissions Access</small>)','sendpress') ." ";
				/*
				$checked = '';
				if( $listrole->has_cap('sendpress_settings_access')) {
					$checked = 'checked';
				}
				echo "<input $checked name='". $role . "_settings_access' type='checkbox' >&nbsp;Access Controls</td>";
				*/
				$checked = '';
				if( $listrole->has_cap('sendpress_queue')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_queue'  > ". __('Full Access','sendpress') ."</td>";
			
				$checked = '';
				if( $listrole->has_cap('sendpress_addons')) {
					$checked = 'checked';
				}
				echo "<td><input $checked type='checkbox' name='". $role . "_addons'  > ".__('Full Access','sendpress') ."</td>";
				echo "</tr>";
			}
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

	function get_role($role){
		global $wp_roles;

	    if ( ! isset( $wp_roles ) )
    		$wp_roles = new WP_Roles();

    	return $wp_roles->get_role( $role );
	}

	function get_editable_roles() {
	    global $wp_roles;

	    if ( ! isset( $wp_roles ) )
    		$wp_roles = new WP_Roles();

	    $all_roles = $wp_roles->get_names();
	    $editable_roles = apply_filters('editable_roles', $all_roles);

	    return $all_roles;
	}
}

} //End Class Check

SendPress_Admin::add_cap('Settings_Access','sendpress_settings_access');
