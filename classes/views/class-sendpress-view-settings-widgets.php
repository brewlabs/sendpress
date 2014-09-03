<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Widgets extends SendPress_View_Settings {

	function save($post, $sp){
		//$data = array_slice($post, 0, -2);
		$data = $post;

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

		SendPress_Data::update_sp_settings_object($data['_sp_settings_id'],$data);
	}

	function view_buttons(){
		?>
		<!--<button class="btn btn-default" id="save-menu-cancel">Cancel</button>-->
		<button class="btn btn-primary" id="save-menu-post">Save</button>
		<?php
	}

	function html($sp) {

		$postid = $_GET['id'];
		if( !$postid ){
			self::display_forms();
			return;
		}


		$settings = SendPress_Data::get_sp_settings_object($postid);
		$settings['_sp_settings_id'] = $postid;
		?>
		<form method="post" id="post">

		<?php 
			switch($settings['_form_type']){

				case 'signup_widget':
					self::signup($settings);
					break;
			}
		?>

		<?php wp_nonce_field($sp->_nonce_value); ?>
		</form>
		<?php

	}

	function display_forms(){
		echo 'suprise mothafucker!';
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
			<h3>Signup Form</h3>
		</div>
		<div class="sp-row">
			<div class="sp-50 sp-first">
				<?php $this->panel_start( __('Signup Ui','sendpress') ); ?>

				<p>
					<label for="_form_description"><?php _e('Description:', 'sendpress'); ?></label>
					<textarea rows="5" type="text" class="widefat" id="_form_description" name="_form_description"><?php echo $settings['_form_description']; ?></textarea>
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
					<p>Use the shortcode belot to insert this signup form into your posts and pages.</p>
					<pre>[sp-form formid=<?php echo $settings['_sp_settings_id']; ?>]</pre>
				<?php $this->panel_end(); ?>

				<?php $this->panel_start( __('Signup Settings','sendpress') ); ?>
				
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_firstname'], 'on' ); ?> id="_collect_firstname" name="_collect_firstname" /> 
					<label for="_collect_firstname"><?php _e('Collect First Name', 'sendpress'); ?></label>
				</p> 

				<p>
					<input class="checkbox" type="checkbox" <?php checked( $settings['_collect_lastname'], 'on' ); ?> id="_collect_lastname" name="_collect_lastname" /> 
					<label for="_collect_lastname"><?php _e('Collect Last Name', 'sendpress'); ?></label>
				</p> 

				<p>
					<label for="_display_labels_inside_fields">Display labels inside?:</label>
					<input type="radio" name="_display_labels_inside_fields" value="1"<?php echo $settings['_display_labels_inside_fields'] == 1 ? ' checked' : ''; ?> /> Yes
					<input type="radio" name="_display_labels_inside_fields" value="0"<?php echo $settings['_display_labels_inside_fields'] == 0 ? ' checked' : ''; ?> /> No
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
				
				
				<p><b>Check off the lists you would like<br>users to subscribe to.</b></p>
				<?php 
				if( count($lists) === 0 ){
					echo '<p>No public lists available</p>';
				}else{
					foreach($lists as $list){
						?>
						<p>
							<input class="checkbox" type="checkbox" <?php checked( $settings['_list_'.$list->ID], 'on' ); ?> id="_list_<?php echo $list->ID; ?>" name="_list_<?php echo $list->ID; ?>" /> 
							<label for="_list_<?php echo $list->ID; ?>"><?php echo $list->post_title; ?></label>
						</p> 
						<?php
					}
				}

				do_action('sendpress_post_notification_widget_form',$lists, $settings, $this, "_");
				?>

				<?php $this->panel_end(); ?>
			</div>
			
		
		</div>
		<input type="hidden" name="_setting_type" id="setting_type" value="form" />
		<input type="hidden" name="_form_type" id="form_type" value="signup_widget" />
		<input type="hidden" name="_sp_settings_id" id="sp_settings_id" value="<?php echo $settings['_sp_settings_id']; ?>" />
		<?php
	}

}