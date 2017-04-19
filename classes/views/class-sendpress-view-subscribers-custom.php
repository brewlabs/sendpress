<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Custom extends SendPress_View_Subscribers {

	function save(){
		$name = sanitize_text_field(SPNL()->validate->_string('_custom_field_label'));
		$saved_post_id_1 = sanitize_text_field(SPNL()->validate->_int('saved_post_id_1'));
		$saved_post_id = sanitize_text_field(SPNL()->validate->_int('saved_post_id'));
		if ($saved_post_id_1 > 0) {

			update_post_meta($saved_post_id_1, '_sp_custom_field_description', $name);

		} else {
			$postid = SendPress_Data::create_settings_post($name, 'custom_field');
			add_post_meta($postid, "_sp_custom_field_key", "custom_field_" . $postid, false);
			add_post_meta($postid, "_sp_custom_field_description", $name, false);
		}

	}

	function html() {?>
<form id="create-custom-field" method="post">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="action" id="action" value="create-custom-field" />
        <!-- custom fields -->
			<?php $this->panel_start('Custom Fields');
				global $wpdb, $custom_field_id;
				$custom_field_list = SendPress_Data::get_custom_fields();
				$count = count($custom_field_list);
				$custom_field_label = "";
				if ($count > 0) {
					foreach ($custom_field_list as $key => $value) {
						$custom_field_label = $value['custom_field_label'];
						$id = $value['id'];
					}
 				}?>
				<p>
					<label for="_salutation_label"><?php _e('Custom Field Label:', 'sendpress'); ?></label>
					<input type="text" class="widefat" id="_custom_field_label" name="_custom_field_label" value="<?php echo $custom_field_label;?>" style="width:300px;" />
					<input type="hidden" name="saved_post_id_1" value="<?php echo $id;?>" />
				</p>

				<div>
					<div id="button-area">
						<!--<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=custom"><?php _e('Save','sendpress'); ?></a>-->
						<input type="submit" value="<?php _e('Save','sendpress'); ?>" class="btn btn-large btn-primary"/>
					</div>
				</div>

			<?php $this->panel_end();?>

			<?php $this->panel_start('Upgrade to SendPress Pro');
			if(defined('SENDPRESS_PRO_VERSION')){
				?>
				<p><?php _e('You have SendPress Pro Version','sendpress'); ?> <?php echo SENDPRESS_PRO_VERSION; ?> <?php _e('this version does not support custom fields. You will need to update to the latest version','sendpress'); ?>.</p>
				<?php
			} else {
				?>
				<p><?php _e('Multiple custom fields are coming in a future release for ','sendpress'); ?> <a href="https://sendpress.com" target="_blank"><?php _e('SendPress Pro','sendpress'); ?></a>. <?php _e('Please upgrade or install Pro to start using this feature','sendpress'); ?>.</p>
				<?php
			}
			$this->panel_end();
			?>
			<?php wp_nonce_field($this->_nonce_value); ?>
			<input type="hidden" name="saved_post_id" value="<?php echo $saved_post_id;?>" />
			</form>
	<?php }

}
