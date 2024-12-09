<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Notifications extends SendPress_View_Settings {

	function save(){
		$post = $_POST;
		//$this->security_check();
		$options = SendPress_Option::get('notification_options');

		$options['email'] =  sanitize_email( SPNL()->validate->_email('toemail') );

        $options['notifications-enable'] = ( array_key_exists('notifications-enable', $post) ) ? true : false;

        if( $options['notifications-enable'] ){
        	$options['subscribed'] = SPNL()->validate->_string('subscribed');
        	$options['unsubscribed'] =  SPNL()->validate->_string('unsubscribed');

        	$options['send-to-admins'] = ( array_key_exists('send-to-admins', $post) ) ? true : false;
        }

        SendPress_Option::set('notification_options', $options );
        SendPress_Admin::redirect('Settings_Notifications');
	}

	function html() {?>
		<div class="notifications">
			<form method="post" id="post">

				<div class="sp-row">
					<?php do_action('sendpress_notification_settings_top'); ?>
				</div>
				<div class="sp-row">
					<div class="sp-50 sp-first">
						<?php $this->panel_start( '<span class="glyphicon glyphicon-envelope"></span> '. __('E-mail Notifications','sendpress') ); ?>

							<?php $options = SendPress_Option::get('notification_options');?>

							<p>
								<input type="checkbox" class="form-control" value="<?php echo $options['notifications-enable']; ?>" name="notifications-enable" id="notifications-enable" <?php checked( $options['notifications-enable'], true ); ?>/>&nbsp;<?php _e('Send E-mail Notifications');?>
							</p>

							<h5 style="display:inline;" class="nomargin"><?php _e('To E-mail','sendpress'); ?>:</h5>
							<input name="toemail" class="form-control" style="display:inline; width:80%;" tabindex=2 type="text" id="toemail" value="<?php echo $options['email']; ?>">
							<br><br>

							<p>
							<input type="checkbox" class="form-control" value="<?php echo $options['send-to-admins']; ?>" name="send-to-admins" id="send-to-admins" <?php checked( $options['send-to-admins'], true ); ?>/>
							<?php _e('Send Notifications to all WordPress Administrators','sendpress'); ?>
							</p>
							<p><?php _e('Select the notifications you\'d like to receive and how often you\'d like to receive them','sendpress'); ?>.</p>
							<h5><?php _e('User Subscribed','sendpress'); ?>:</h5>
							<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['subscribed']) === 0){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Instant','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['subscribed']) === 1){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Daily','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['subscribed']) === 2){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Weekly','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['subscribed']) === 3){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Monthly','sendpress'); ?>&nbsp;&nbsp;&nbsp;

							<h5><?php _e('User Unsbscribed','sendpress'); ?>:</h5>
							<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['unsubscribed']) === 0){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Instant','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['unsubscribed']) === 1){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Daily','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['unsubscribed']) === 2){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Weekly','sendpress'); ?>&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['unsubscribed']) === 3){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							<?php _e('Monthly','sendpress'); ?>&nbsp;&nbsp;&nbsp;

						<?php $this->panel_end(); ?>

					</div>
			   		<div class="sp-50">

			   		</div>
			   	</div>
   				<?php do_action('sendpress_notification_settings_bottom'); ?>
   				<?php wp_nonce_field($this->_nonce_value); ?>
   			</form>
   		</div>

		<?php
	}

}