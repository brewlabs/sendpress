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
        }

        $options = apply_filters('sendpress_notification_settings_save',$options, $post, $sp);

        SendPress_Option::set('notification_options', $options );
        SendPress_Admin::redirect('Settings_Notifications');
	}

	function html($sp) {?>
		<div class="notifications">
			<form method="post" id="post">
				<div style="float:right;" >
					<a href="<?php echo SendPress_Admin::link('Settings_Notifications'); ?>" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
				</div>
				<br class="clear">

				<?php do_action('sendpress_notification_settings_top'); ?>
				
				<h3>Admin Notification Settings</h3>
				
				<div class="well">

					<?php $options = SendPress_Option::get('notification_options');?>
					<h3 style="display:inline;">
						<input class="ibutton" type="checkbox" value="<?php echo $options['notifications-enable']; ?>" name="notifications-enable" id="notifications-enable" <?php checked( $options['notifications-enable'], true ); ?>/>&nbsp;<?php _e('Send Subscription Notifications');?>
					</h3><br><br>
					<!-- <a href="#" class="tooltip" rel="tooltip" data-toggle="tooltip" title="The name and e-mail you want notifications to be sent to."><i class="icon-question-sign"></i></a> -->
					<div class="clearfix">
						<div style="float:left; width:45%;">
							<h4 class="nomargin"><?php _e('Notification E-mail','sendpress'); ?></h4>
							<input name="toemail" tabindex=2 type="text" id="toemail" value="<?php echo $options['email']; ?>">
							<br>
							
							<input type="checkbox" value="<?php echo $options['send-to-admins']; ?>" name="send-to-admins" id="send-to-admins" <?php checked( $options['send-to-admins'], true ); ?>/>
							<?php _e('Send Notifications to all WordPress Administrators','sendpress'); ?>
						</div>
						<div style="float:right; width:45%;">
							<h4 class="nomargin"><?php _e('HipChat Integration','sendpress'); ?></h4>
							<input type="checkbox" value="<?php echo $options['enable-hipchat']; ?>" name="enable-hipchat" id="enable-hipchat" <?php checked( $options['enable-hipchat'], true ); ?>/>
							<?php _e('Enable HipChat Notification','sendpress'); ?><br>
							API Key: <input name="hipchat-api" tabindex=2 type="text" id="hipchat-api" value="<?php echo $options['hipchat-api']; ?>"><br><a href="https://sendpress.hipchat.com/admin/api" target="_blank">Where is my API key?</a><br>

							<?php 
								if( strlen($options['hipchat-api']) > 0 ){
									global $hc;
									$hc = new SendPress_HipChat($options['hipchat-api'], 'https://api.hipchat.com');
									
									try{
										$rooms = $hc->get_rooms();
										?>
										<br>
										<h4 class="nomargin"><?php _e('Select the rooms to send notifications to:','sendpress'); ?></h4>
										<?php
										foreach ($rooms as $room) {
											?>
											<input type="checkbox" value="<?php echo $options['hipchat-rooms'][$room->room_id]; ?>" name="hipchat-rooms[<?php echo $room->room_id; ?>]" id="hipchat-rooms[<?php echo $room->room_id; ?>]" <?php checked( $options['hipchat-rooms'][$room->room_id], true ); ?>/>
											<?php echo $room->name; ?><br>
											<?php
										}
									}
									catch (Exception $e){
										?>
										Room Name: <input name="hipchat-room" tabindex=2 type="text" id="hipchat-room" value="<?php echo $options['hipchat-room']; ?>">
										<?php
									}


									

									
								}
							?>
						</div>
					</div>
					<p>Select the notifications you'd like to receive and how often you'd like to receive them.</p>
					<h4>User Subscribed:</h4>
					<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['subscribed']) === 0){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Instant&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['subscribed']) === 1){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Daily&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['subscribed']) === 2){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Weekly&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['subscribed']) === 3){echo 'checked="checked"';} ?> name="subscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Monthly&nbsp;&nbsp;&nbsp;

					<h4>User Unsbscribed:</h4>
					<input class="notifications-radio" type="radio" value="0" <?php if(intval($options['unsubscribed']) === 0){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Instant&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="1" <?php if(intval($options['unsubscribed']) === 1){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Daily&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="2" <?php if(intval($options['unsubscribed']) === 2){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Weekly&nbsp;&nbsp;&nbsp;
					<input class="notifications-radio" type="radio" value="3" <?php if(intval($options['unsubscribed']) === 3){echo 'checked="checked"';} ?> name="unsubscribed" <?php if(!$options['notifications-enable']){echo 'disabled';} ?>>
					Monthly&nbsp;&nbsp;&nbsp;
				</div>

				<?php do_action('sendpress_notification_settings_bottom'); ?>
				
				<?php wp_nonce_field($sp->_nonce_value); ?>
			</form>
		</div>
		<?php
	}

}