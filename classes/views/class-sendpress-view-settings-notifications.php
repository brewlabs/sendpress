<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Notifications extends SendPress_View_Settings {

	function save($post, $sp){
		$options = SendPress_Option::get('notification_options');

		$options['email'] = $post['toemail'];

        $options['notifications-enable'] = ( array_key_exists('notifications-enable', $post) ) ? true : false;

        if( $options['notifications-enable'] ){
        	$options['subscribed'] = $post['subscribed'];
        	$options['unsubscribed'] = $post['unsubscribed'];

        	$options['send-to-admins'] = ( array_key_exists('send-to-admins', $post) ) ? true : false;
        }

        $options['enable-hipchat'] = ( array_key_exists('enable-hipchat', $post) ) ? true : false;
        $options['hipchat-api'] = $post['hipchat-api'];
        if( strlen($options['hipchat-api']) > 0 ){

    		$options['hipchat-rooms'] = array();

    		if( !array_key_exists('hipchat-rooms', $post) ){
    			$post['hipchat-rooms'] = array();
    		}

			global $hc;
			$hc = new SendPress_HipChat($options['hipchat-api'], 'https://api.hipchat.com');

			try{
				foreach ($hc->get_rooms() as $room) {
					$options['hipchat-rooms'][$room->room_id] = ( array_key_exists($room->room_id, $post['hipchat-rooms']) ) ? true : false;
				}
			}catch(Exception $e){
				$options['hipchat-room'] = $post['hipchat-room'];
			}
    	}

        $options = apply_filters('sendpress_notification_settings_save',$options, $post, $sp);

        SendPress_Option::set('notification_options', $options );
        SendPress_Admin::redirect('Settings_Notifications');
	}

	function html($sp) {?>
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
							<p>Select the notifications you'd like to receive and how often you'd like to receive them.</p>
							<h5>User Subscribed:</h5>
							<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['subscribed']) === 0){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Instant&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['subscribed']) === 1){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Daily&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['subscribed']) === 2){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Weekly&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['subscribed']) === 3){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Monthly&nbsp;&nbsp;&nbsp;

							<h5>User Unsbscribed:</h5>
							<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['unsubscribed']) === 0){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Instant&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['unsubscribed']) === 1){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Daily&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['unsubscribed']) === 2){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Weekly&nbsp;&nbsp;&nbsp;
							<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['unsubscribed']) === 3){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
							Monthly&nbsp;&nbsp;&nbsp;

						<?php $this->panel_end(); ?>

					</div>
			   		<div class="sp-50">
						<?php $this->panel_start( '<span class="glyphicon glyphicon-comment"></span> '. __('HipChat Notification','sendpress') ); ?>

							<p>
							<input class="form-control" type="checkbox" value="<?php echo $options['enable-hipchat']; ?>" name="enable-hipchat" id="enable-hipchat" <?php checked( $options['enable-hipchat'], true ); ?>/>
							<?php _e('Enable HipChat Notification','sendpress'); ?><br>
							</p>
							API Key: <input class="form-control" style="width:80%; display:inline;" name="hipchat-api" tabindex=2 type="text" id="hipchat-api" value="<?php echo $options['hipchat-api']; ?>"><br><a style="display:inline-block; margin-left:50px;" href="https://sendpress.hipchat.com/admin/api" target="_blank">Where is my API key?</a><br>

							<?php
								if( strlen($options['hipchat-api']) > 0 ){
									global $hc;
									$hc = new SendPress_HipChat($options['hipchat-api'], 'https://api.hipchat.com');

									try{
										$rooms = $hc->get_rooms();
										?>
										<br>
										<h5 class="nomargin"><?php _e('Select the rooms to send notifications to:','sendpress'); ?></h5>
										<p style="margin-left:30px;">
										<?php
										foreach ($rooms as $room) {
											?>
											<input type="checkbox" class="form-control" value="<?php echo $options['hipchat-rooms'][$room->room_id]; ?>" name="hipchat-rooms[<?php echo $room->room_id; ?>]" id="hipchat-rooms[<?php echo $room->room_id; ?>]" <?php checked( $options['hipchat-rooms'][$room->room_id], true ); ?>/>
											<?php echo $room->name; ?><br>
											<?php
										}
										?>
										</p>
										<?php
									}
									catch (Exception $e){
										?>
										Room Name: <input class="form-control" name="hipchat-room" tabindex=2 type="text" id="hipchat-room" value="<?php echo $options['hipchat-room']; ?>">
										<?php
									}

								}
							?>

						<?php $this->panel_end(); ?>
			   		</div>
			   	</div>
   				<?php do_action('sendpress_notification_settings_bottom'); ?>
   				<?php wp_nonce_field($sp->_nonce_value); ?>
   			</form>
   		</div>

		<?php
	}

}