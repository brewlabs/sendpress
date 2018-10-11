<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Pro_Extras extends SendPress_View_Settings {
	
	function save(){
		$post = $_POST;
		if(isset( $post['import_update'] )){
			SendPress_Option::set('import_update', true );
		} else {
			SendPress_Option::set('import_update', false );
		}

        SendPress_Admin::redirect('Settings_Pro_Extras');
	}

	

	function html() {
		?><form method="post" id="post">
		<!--
		<div style="float:right;" >
			<a href="<?php echo SendPress_Admin::link('Settings_Advanced'); ?>" class="btn btn-large btn-default" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
		</div>
		-->
		

		
				<br class="clear">
				<br class="clear">
		<div class="sp-row">
			<div class="sp-50 sp-first">

				<?php $this->panel_start( __('Import Settings','sendpress') ); ?>
				<?php $ctype = SendPress_Option::get('import_update'); ?>
				<input type="checkbox" name="import_update" value="true" <?php if($ctype){echo "checked='checked'"; } ?> /> &nbsp;<?php _e('Update firstname, lastname, phone and salutation on csv import','sendpress'); ?>
				<?php $this->panel_end(); ?>




			</div>	
			<div class="sp-50">

				<?php $this->panel_start( __('Pro Extras','sendpress') ); ?>
				<?php $ctype = SendPress_Option::get('import_update'); ?>
				<p>Pro Extras are available as long as you have SendPress Pro installed.</p>
				<?php $this->panel_end(); ?>



			
			
			</div>

		</div>
		<?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		<?php
	}

}