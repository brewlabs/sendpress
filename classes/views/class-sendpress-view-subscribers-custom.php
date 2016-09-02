<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Custom extends SendPress_View_Subscribers {
	
	function save(){
		$name = sanitize_text_field(SPNL()->validate->_string('_custom_field_label'));
		$saved_post_id = sanitize_text_field(SPNL()->validate->_int('saved_post_id'));
		if ($saved_post_id > 0) {

			update_post_meta($saved_post_id, '_sp_custom_field_description', $name);

		} else {
			$postid = SendPress_Data::create_settings_post($name, 'custom_field');
			add_post_meta($postid, "_sp_custom_field_description", $name, false);
		}
		
	}

	function html() {?>
<form id="create-custom-field" method="post">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="action" id="action" value="create-custom-field" />
			<?php $this->panel_start('Custom Fields');

				$args = array(
					'post_type' => 'sp_settings',
					'meta_query' => array(
						array(
							'key'     => '_sp_setting_type',
							'value'   => 'custom_field',
							'compare' => '=',
						),
					)
				);
				$query = new WP_Query( $args );

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {
						$query->the_post();
						//$custom_field_label = get_the_title();
						$saved_post_id = get_the_ID();
						$custom_field_label = get_post_meta($saved_post_id, '_sp_custom_field_description', true);
					}
					/* Restore original Post Data */
					wp_reset_postdata();
				} else {
					$custom_field_label = "";
					$saved_post_id = 0;
				}
			?>
			<p>
				<label for="_salutation_label"><?php _e('Custom Field Label:', 'sendpress'); ?></label>
				<input type="text" class="widefat" id="_custom_field_label" name="_custom_field_label" value="<?php echo $custom_field_label;?>" style="width:300px;" />
			</p>

				
			
				<div> 
					<div id="button-area">  
						<!--<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=custom"><?php _e('Save','sendpress'); ?></a>-->
						<input type="submit" value="<?php _e('Save List','sendpress'); ?>" class="btn btn-large btn-primary"/>
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