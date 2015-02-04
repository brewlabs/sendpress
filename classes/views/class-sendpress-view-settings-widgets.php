<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Widgets extends SendPress_View_Settings {

	function save($post, $sp){

		// print_r($post);
		// die();

		$action = (isset($post['form_action'])) ? $post['form_action'] : 'save';
		
		switch($action){
			case 'copy':
				$postid = SendPress_Data::create_settings_post($post['post_subject'], "", $post['copy_from']);
				wp_redirect( '?page=sp-settings&view=widgets&id='. $postid );
				break;
			case 'create':

				$postid = SendPress_Data::create_settings_post($post['post_subject'], $post['form_type']);
				wp_redirect( '?page=sp-settings&view=widgets&id='. $postid );
				break;
			default:
				self::save_form($post, $sp);
				break;
		}

	}

	function save_form($post, $sp){
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

		SendPress_Data::update_post_meta_object($data['_settings_id'],$data);
	}

	function view_buttons(){
		?>
		<!--<button class="btn btn-default" id="save-menu-cancel">Cancel</button>-->
		<button class="btn btn-primary" id="save-menu-post"><?php _e('Save','sendpress'); ?></button>
		<?php
	}

	function html($sp) {

		$postid = ISSET($_GET['id']) ? $_GET['id'] : 0;
		$showCreate = (isset($_GET['create']) && $_GET['create'] == 1) ? true : false;

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

		if(strlen($settings['_form_type']) > 0){
			echo '<form method="post" id="post">';
		}

		switch($settings['_form_type']){
			case 'create_form':
				self::create_form();
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
			wp_nonce_field($sp->_nonce_value);
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
					<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=widgets&create=1"><?php _e('Create Form','sendpress'); ?></a>
				</div>
				<h2><?php _e('Forms','sendpress'); ?></h2>
			</div>
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		    <!-- Now we can render the completed list table -->
		    <?php $testListTable->display(); ?>
		    <?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		<?php
	}

	function create_form(){
		
		$copy_from = (isset($_GET['id'])) ? $_GET['id'] : 0;

		$save_type = ($copy_from > 0) ? "copy" : "create";

		?>
		
		<h2><?php ucfirst($save_type); ?> <?php _e('Form','sendpress'); ?></h2>
		<br>
		
    	<div class="sp-row">
    		<div class="sp-50 sp-first">
				<?php $this->panel_start( __('Form Name','sendpress') ); ?>
        
		        <input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
		        
		        <?php $this->panel_end(  ); ?>
			</div>
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
					<p><?php _e('Use the shortcode belot to insert this signup form into your posts and pages','sendpress'); ?>.</p>
					<pre>[sp-form formid=<?php echo $settings['_settings_id']; ?>]</pre>
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

				do_action('sendpress_post_notification_widget_form_new',$lists, $settings, $this, "_");
				?>

				<?php $this->panel_end(); ?>
			</div>
			
		
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