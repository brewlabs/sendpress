<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Customfields extends SendPress_View_Subscribers {

	function save(){
		$data = SPNL()->validate->_string('fieldJson');

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';

		 $json = json_decode($data, true);

		// echo '<pre>';
		// print_r($json);
		// echo '</pre>';

		//die();


		foreach ($json as $key => $d) {
			
			$d['slug'] = SendPress_Data::slugify($d['label'], $d['id']);
			$id = SPNL()->load('Customfields')->add($d);

			if(intval($d['id']) === 0){
				$d['id'] = $id;
				$d['slug'] = SendPress_Data::slugify($d['label'], $id);
				SPNL()->load('Customfields')->add($d);
			}

		}

	}

	function html() {

		$fields = SPNL()->load('Customfields')->get_all();

		$lastId = array_values(array_slice($fields, -1))[0]['id'] + 1;

		$this->panel_start('Custom Fields');
		?>

		<form id="create-custom-field" method="post" role="form" data-newfield="<?php echo htmlspecialchars(SendPress_Data::custom_field_template());?>">
       		<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />

        <?php

        $json = array();

		foreach ($fields as $key => $field) {
			// echo '<pre>';
			// print_r($field);
			// echo '</pre>';

			$json[] = array('id' => $field['id'], 'label' => $field['label']);
		
			?>

			<p>
				<label class="custom-field-label" for="_salutation_label"><?php _e('Label ', 'sendpress'); ?>:</label><input type="text" class="widefat custom-field" data-field-id="<?php echo $field['id']; ?>" id="custom_field_label" name="custom_field_label" value="<?php echo $field['label'];?>" style="width:300px;" />
				<!--<input type="hidden" name="saved_post_id_<?php echo($field['id']);?>" value="<?php echo($field['id']);?>" />-->
			</p>

			<?php
			
		}

		?>
		<input type="hidden" name="fieldJson" id="fieldJson" value="<?php echo htmlspecialchars(json_encode($json)); ?>" />
		<a class="btn btn-primary btn-large pull-right" id="save-custom-fields" href="#"><?php _e('Submit','sendpress'); ?></a>
		<?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		<?php


		$this->panel_end();

		$this->panel_start('Upgrade to SendPress Pro');

		if(defined('SENDPRESS_PRO_VERSION')){
			?>
			<p><?php _e('You have SendPress Pro Version','sendpress'); ?> <?php echo SENDPRESS_PRO_VERSION; ?> <?php _e('this version does not support our new custom fields. You will need to update to the latest version','sendpress'); ?>.</p>
			<?php
		} else {
			?>
			<p><?php _e('Multiple custom fields are avalible if you upgrade to ','sendpress'); ?> <a href="https://www.sendpress.com" target="_blank"><?php _e('SendPress Pro','sendpress'); ?></a>. <?php _e('Please upgrade or install Pro to start using this feature','sendpress'); ?>.</p>
			<?php
		}

		$this->panel_end();
				
	}
}
