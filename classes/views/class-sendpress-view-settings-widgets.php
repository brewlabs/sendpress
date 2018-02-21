<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Widgets extends SendPress_View_Settings {

	function save(){
		//$this->security_check();
		// print_r($post);
		// die();

		$action = 'save';
		$new_action = SPNL()->validate->_string('form_action');
		if($new_action !== false || $new_action !== null){
			$action = $new_action;
		}
		//$action = (isset($post['form_action'])) ? $post['form_action'] : 'save';

		$post_subject = SPNL()->validate->_string('post_subject');
		$copy_from = SPNL()->validate->_string('copy_from');
		$form_type = SPNL()->validate->_string('form_type');

		if(strlen($post_subject) === 0){
			$post_subject = "SendPress Form";
		}

		switch($action){
			case 'copy':
				$postid = SendPress_Data::create_settings_post($post_subject, "", $copy_from);
				//wp_redirect( '?page=sp-settings&view=widgets&id='. $postid );
				SendPress_Admin::redirect( 'Settings_Widgets' , array( 'id' => $postid ) );
				break;
			case 'create':

				$postid = SendPress_Data::create_settings_post($post_subject, $form_type);
				//wp_redirect( '?page=sp-settings&view=widgets&id='. $postid );
				SendPress_Admin::redirect( 'Settings_Widgets' , array( 'id' => $postid ) );
				break;
			case 'delete':
				self::delete_form_save();
				//wp_redirect( '?page=sp-settings&view=widgets' );
				SendPress_Admin::redirect( 'Settings_Widgets' );
				break;
			default:
				self::save_form();
				break;
		}

	}

	//fix this somehow...
	function save_form(){
		//$data = array_slice($post, 0, -2);
		$data = $_POST;

		//error_log(print_r($data, true));

		//fix list ids for signup
		$listids = array();
		foreach($data as $key => $item){
			if (strpos($key, "_list_") === 0){
				if( $item === "on" ){
					$id = intval(substr($key, 6));
					if($id > 0){
						$listids[] = $id;
					}

				}

			}
		}

		$data['_listids'] = implode(',', $listids);

		SendPress_Data::update_post_meta_object($data['_settings_id'],$data);
	}

	function delete_form_save(){
		//$this->security_check();
		SendPress_Data::delete_post_meta_object(SPNL()->validate->_int('deleteid'),$data);
	}

	function view_buttons(){
		//$this->security_check();

		$postid = SPNL()->validate->_int('id');
		$showCreate = SPNL()->validate->_int('create') == 1 ? true : false;
		$showDelete = SPNL()->validate->_int('delete') == 1 ? true : false;

		if($showDelete || $showCreate|| $postid > 0){
			$btnText = $showDelete ? "Confirm" : "Save";
			$btnClass = $showDelete ? "danger" : "primary";
			?>
			<button class="btn btn-default" id="save-menu-cancel">Cancel</button>
			<button class="btn btn-<?php echo $btnClass; ?>" id="save-menu-post"><?php _e($btnText,'sendpress'); ?></button>
			<?php
		}

	}

	function html() {

		$postid = SPNL()->validate->_int('id');
		$showCreate = SPNL()->validate->_int('create') == 1 ? true : false;
		$showDelete = SPNL()->validate->_int('delete') == 1 ? true : false;

		$settings = SendPress_Data::get_post_meta_object($postid);
		$settings['_settings_id'] = $postid;

		if(!$postid && !$showCreate){
			//self::display_forms();
			$settings['_form_type'] = '';
		}

		if( $showCreate ){
			//self::create_form();
			$settings['_form_type'] = 'create_form';
		}

		if( $showDelete ){
			//self::create_form();
			$settings['_form_type'] = 'delete_form';
		}

		if(strlen($settings['_form_type']) > 0){
			echo '<form method="post" id="post">';
		}

		switch($settings['_form_type']){
			case 'create_form':
				self::create_form();
				break;
			case 'delete_form':
				self::delete_form();
				break;
			case 'signup_widget':
				self::signup($settings);
				break;
			case 'manage_subscriptions':
				self::manage_subscriptions($settings);
				break;
			default:
				self::display_forms();
				break;
		}

		if(strlen($settings['_form_type']) > 0){
			wp_nonce_field($this->_nonce_value);
			echo '</form>';
		}

	}

	function display_forms(){
		SendPress_Tracking::event('Emails Tab');

		//Create an instance of our package class...
		$testListTable = new SendPress_Settings_Forms_Table();
		//Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items();

		?>

		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="forms-filter" method="get">
			<div id="taskbar" class="lists-dashboard rounded group">
				<div id="button-area">
					<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=widgets&create=1"><?php _e('Create Form','sendpress'); ?></a>
				</div>
				<h2><?php _e('Forms','sendpress'); ?></h2>
			</div>
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page() ?>" />
		    <!-- Now we can render the completed list table -->
		    <?php $testListTable->display(); ?>
		    <?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		<?php
	}

	function delete_form(){
		?>
		<h2><?php _e('Confirm Delete','sendpress'); ?></h2>
		<div>Click confirm to delete the form. THIS CANNOT BE UNDONE!</div>
		<input type="hidden" name="form_action" id="form_action" value="delete" />
		<input type="hidden" name="deleteid" id="deleteid" value="<?php echo SPNL()->validate->_int('id'); ?>" />
		<?php
	}

	function create_form(){

		$copy_from =  SPNL()->validate->_int('id');
		$save_type = ($copy_from > 0) ? "copy" : "create";

		?>

		<h2><?php ucfirst($save_type); ?> <?php _e('Form','sendpress'); ?></h2>
		<br>

    	<div class="sp-row">
    		<div class="sp-50 sp-first">
				<?php $this->panel_start( __('Form Name','sendpress') ); ?>

		        <input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="" id="email-subject" autocomplete="off" />

		        <?php $this->panel_end(  ); ?>
			</div>
			<?php if($save_type === 'create'){
				?>
				<div class="sp-50">
					<?php
					$form_types = SendPress_Data::get_widget_form_types();
					?>
					<?php $this->panel_start( __('Form Type','sendpress') ); ?>
					<select class="form-control" name="form_type" id="form_type">
						<option value="0"></option>
						<?php
							foreach ($form_types as $key => $value) {
								echo '<option value="'.$key .'">' . $value . '</option>';
							}
						?>

					</select>
					<?php $this->panel_end(); ?>
				</div>
				<?php
			} ?>

		</div>


		<input type="hidden" name="_setting_type" id="setting_type" value="form" />
		<!--<input type="hidden" name="_form_type" id="form_type" value="signup_widget" />-->
		<input type="hidden" name="form_action" id="form_action" value="<?php echo $save_type; ?>" />

		<?php
		if($copy_from > 0){
			?>
			<input type="hidden" name="copy_from" id="copy_from" value="<?php echo $copy_from; ?>" />
			<?php
		}
	}

	function signup($settings){

		$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);

	    $listids = array();

		foreach($lists as $list){
			if( !array_key_exists('list_'.$list->ID, $settings) ){
				$settings['list_'.$list->ID] = false;
			}
		}
		?>
		<div class="sp-row">
			<h3><?php echo $settings['post_title'];?></h3>
		</div>
		<div class="sp-row">
			<div class="sp-50 sp-first">
				<?php $this->panel_start( __('Signup Ui','sendpress') ); ?>

				<p>
					<label for="_form_description"><?php _e('Description:', 'sendpress'); ?></label>
					<textarea rows="5" type="text" class="widefat" id="_form_description" name="_form_description"><?php echo $settings['_form_description']; ?></textarea>
				</p>

				<p>
					<label for="_salutation_label"><?php _e('Salutation Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_salutation_label" name="_salutation_label" value="<?php echo $settings['_salutation_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_firstname_label"><?php _e('First Name Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_firstname_label" name="_firstname_label" value="<?php echo $settings['_firstname_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_lastname_label"><?php _e('Last Name Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_lastname_label" name="_lastname_label" value="<?php echo $settings['_lastname_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_phonenumber_label"><?php _e('Phone Number Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_phonenumber_label" name="_phonenumber_label" value="<?php echo $settings['_phonenumber_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_email_label"><?php _e('E-Mail Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_email_label" name="_email_label" value="<?php echo $settings['_email_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_button_label"><?php _e('Button Text:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_button_label" name="_button_label" value="<?php echo $settings['_button_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_list_label"><?php _e('Lists Label: multiple lists only', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_list_label" name="_list_label" value="<?php echo $settings['_list_label']; ?>" style="width:100%;" />
				</p>

				<p>
					<label for="_thankyou_message"><?php _e('Thank you message:', 'sendpress'); ?></label>
					<textarea rows="5" type="text" class="widefat" id="_thankyou_message" name="_thankyou_message"><?php echo $settings['_thankyou_message']; ?></textarea>
				</p>

				<?php $this->panel_end(); ?>
			</div>
			<div class="sp-50">
				<?php $this->panel_start( __('Shortcode','sendpress') ); ?>
					<p><?php _e('Use the shortcode below to insert this signup form into your posts and pages','sendpress'); ?>.</p>
					<pre>[sp-form formid=<?php echo $settings['_settings_id']; ?>]</pre>
				<?php $this->panel_end(); ?>

				<?php $this->panel_start( __('Signup Settings','sendpress') ); ?>

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_salutation'], 'on' ); ?> id="_collect_salutation" name="_collect_salutation" />
					<label for="_collect_salutation"><?php _e('Collect Salutation', 'sendpress'); ?></label>

					<br>

					<input style="margin-left:30px;" class="checkbox" type="checkbox" <?php checked( $settings['_salutation_required'], 'on' ); ?> id="_salutation_required" name="_salutation_required" />
					<label for="_salutation_required"><?php _e('Salutation Required', 'sendpress'); ?></label>

				</p>

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_firstname'], 'on' ); ?> id="_collect_firstname" name="_collect_firstname" />
					<label for="_collect_firstname"><?php _e('Collect First Name', 'sendpress'); ?></label>

					<br>

					<input style="margin-left:30px;" class="checkbox" type="checkbox" <?php checked( $settings['_firstname_required'], 'on' ); ?> id="_firstname_required" name="_firstname_required" />
					<label for="_firstname_required"><?php _e('First Name Required', 'sendpress'); ?></label>

				</p>

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_lastname'], 'on' ); ?> id="_collect_lastname" name="_collect_lastname" />
					<label for="_collect_lastname"><?php _e('Collect Last Name', 'sendpress'); ?></label>

					<br>

					<input style="margin-left:30px;" class="checkbox" type="checkbox" <?php checked( $settings['_lastname_required'], 'on' ); ?> id="_lastname_required" name="_lastname_required" />
					<label for="_lastname_required"><?php _e('Last Name Required', 'sendpress'); ?></label>

				</p>

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_phonenumber'], 'on' ); ?> id="_collect_phonenumber" name="_collect_phonenumber" />
					<label for="_collect_phonenumber"><?php _e('Collect Phone Number', 'sendpress'); ?></label>

					<br>

					<input style="margin-left:30px;" class="checkbox" type="checkbox" <?php checked( $settings['_phonenumber_required'], 'on' ); ?> id="_phonenumber_required" name="_phonenumber_required" />
					<label for="_phonenumber_required"><?php _e('Phone Number Required', 'sendpress'); ?></label>

				</p>

				<p>
					<label for="_display_labels_inside_fields"><?php _e('Display labels inside','sendpress'); ?>?:</label>
					<input type="radio" name="_display_labels_inside_fields" value="1"<?php echo $settings['_display_labels_inside_fields'] == 1 ? ' checked' : ''; ?> /> <?php _e('Yes','sendpress'); ?>
					<input type="radio" name="_display_labels_inside_fields" value="0"<?php echo $settings['_display_labels_inside_fields'] == 0 ? ' checked' : ''; ?> /> <?php _e('No','sendpress'); ?>
				</p>

				<p>
					<label for="_thankyou_page"><?php _e('Thank You Page (AJAX OFF ONLY):', 'sendpress'); ?></label>
					<select name="_thankyou_page" id="_thankyou_page">
						 <option value="0">
						 	<?php $cpageid = $settings['_thankyou_page'];
						 	?>
						<?php echo esc_attr( __( 'Default' ) ); ?></option>
						 <?php
						  $pages = get_pages();
						  foreach ( $pages as $page ) {
						  	$s ='';
						  	if($cpageid == $page->ID){ $s =  "selected"; }
						  	$option = '<option value="' . $page->ID .'" ' .$s. '>';
							$option .= $page->post_title;
							$option .= '</option>';
							echo $option;
						  }
						 ?>
					</select>

				</p>

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_lists_checked'], 'on' ); ?> id="_lists_checked" name="_lists_checked" />
					<label for="_lists_checked"><?php _e('Select Lists by Default', 'sendpress'); ?></label>
				</p>


				<p><b><?php _e('Check off the lists you would like','sendpress'); ?><br><?php _e('users to subscribe to','sendpress'); ?>.</b></p>
				<?php
				if( count($lists) === 0 ){
					?><p><?php
					_e('No public lists available','sendpress');
					?></p><?php
				}else{
					foreach($lists as $list){
						?>
						<p>
							<input class="checkbox" type="checkbox" <?php if(isset($settings['_list_'.$list->ID])){ checked( $settings['_list_'.$list->ID], 'on' ); } ?> id="_list_<?php echo $list->ID; ?>" name="_list_<?php echo $list->ID; ?>" />
							<label for="_list_<?php echo $list->ID; ?>"><?php echo $list->post_title; ?></label>
						</p>
						<?php
					}
				}

				do_action('sendpress_post_notification_widget_form_new',$lists, $settings, $this, "_");
				?>

				<?php $this->panel_end(); ?>
			</div>

				<?php
					global $wpdb, $custom_field_id;

					$custom_field_list = SendPress_Data::get_custom_fields_new();

 					$count = count($custom_field_list);

					if ($count > 0) {
					?>
			<!-- custom fields -->
			<div class="sp-50">
				<?php $this->panel_start( __('Custom Fields','sendpress') ); ?>


 					<?php
					foreach ($custom_field_list as $key => $value) {
						$custom_field_label = $value['custom_field_label'];

						?>
						<p>
							<input class="checkbox custom-field" type="checkbox" <?php checked( $settings['_collect_custom_field_'.$value['id']], 'on' ); ?> id="_collect_custom_field_<?php echo $value['id']; ?>" name="_collect_custom_field_<?php echo $value['id']; ?>" />
							<label for="_collect_custom_field_<?php echo $value['id']; ?>"><?php _e('Collect field')?> - <?php echo $custom_field_label; ?></label>

							<br>

							<input style="margin-left:30px;" class="checkbox" type="checkbox" <?php checked( $settings['_custom_field_'.$value['id'].'_required'], 'on' ); ?> id="<?php echo '_custom_field_'.$value['id'].'_required'; ?>" name="<?php echo '_custom_field_'.$value['id'].'_required'; ?>" />
							<label for="<?php echo '_custom_field_'.$value['id'].'_required'; ?>"><?php echo $custom_field_label; ?> <?php _e('Required', 'sendpress'); ?></label>

						</p>

						<?php
				}
				?>

				<?php $this->panel_end(); ?>
			</div>
		<?php }?>
		</div>
		<input type="hidden" name="_setting_type" id="setting_type" value="form" />
		<input type="hidden" name="_form_type" id="form_type" value="signup_widget" />
		<input type="hidden" name="_settings_id" id="sp_settings_id" value="<?php echo $settings['_settings_id']; ?>" />
		<?php
	}

	function manage_subscriptions($settings){
		?>
		<div class="sp-row">
			<h3><?php echo $settings['post_title'];?></h3>
		</div>
		<div class="sp-row">
			<div class="sp-50 sp-first">
				<?php $this->panel_start( __('Manage Subscription Options','sendpress') ); ?>

				<p>
					<label for="_form_description"><?php _e('Description:', 'sendpress'); ?></label>
					<textarea placeholder="This text will show above the manage subscription form." rows="5" type="text" class="widefat" id="_form_description" name="_form_description"><?php echo $settings['_form_description']; ?></textarea>
				</p>

				<?php $this->panel_end(); ?>
			</div>
			<div class="sp-50">
				<?php $this->panel_start( __('Shortcode','sendpress') ); ?>
					<p><?php _e('Use the shortcode below to insert this signup form into your posts and pages','sendpress'); ?>.</p>
					<pre><tt>[sp-form formid=<?php echo $settings['_settings_id']; ?>]</tt></pre>
				<?php $this->panel_end(); ?>
			</div>

		</div>
		<input type="hidden" name="_setting_type" id="setting_type" value="form" />
		<input type="hidden" name="_form_type" id="form_type" value="manage_subscriptions" />
		<input type="hidden" name="_settings_id" id="sp_settings_id" value="<?php echo $settings['_settings_id']; ?>" />
		<?php
	}

}
