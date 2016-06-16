<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Dbfix extends SendPress_View_Settings {
	
	function save(){
		//$this->security_check();
		if(isset($_POST['templates'])){
			SendPress_Data::remove_all_templates();
			SendPress_Template_Manager::update_template_content();
		}

		if(isset($_POST['settings'])){
			SendPress_Data::remove_all_settings();
			//SendPress_Data::create_settings_post_signup_form();
		}
		
        SendPress_Admin::redirect('Settings_Dbfix');
	}

	

	function html() {
		?>
		<form method="post" id="post">
			<div class="sp-row">
				<div class="sp-50 sp-first">
				<?php $this->panel_start( __('1.0 Template Reset','sendpress') ); ?>
					<p><?php _e('This will remove all templates in the new template system','sendpress'); ?>.</p>
					<input type="submit" name="templates" class="btn btn-primary" value="Reset Templates" />
				<?php $this->panel_end(); ?>
				</div>
				<div class="sp-50">
				<?php $this->panel_start( __('Reset Settings','sendpress') ); ?>
					<p><?php _e('This will reset all form settings and remove extra metadata','sendpress'); ?></p>
					<input type="submit" name="settings" class="btn btn-primary" value="Reset Settings" />
					
					<?php $this->panel_end(); ?>
				</div>
			</div>
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>

		<?php
	}

}