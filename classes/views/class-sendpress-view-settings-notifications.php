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
        $options['name'] = $post['toname'];

        $options['notifications-enable'] = ( array_key_exists('notifications-enable', $post) ) ? true : false;

        if( $options['notifications-enable'] ){
        	$options['notifications-subscribed-instant'] = ( array_key_exists('notifications-subscribed-instant', $post) ) ? true : false;
	        $options['notifications-subscribed-daily'] = ( array_key_exists('notifications-subscribed-daily', $post) ) ? true : false;
	        $options['notifications-subscribed-weekly'] = ( array_key_exists('notifications-subscribed-weekly', $post) ) ? true : false;
	        $options['notifications-subscribed-monthly'] = ( array_key_exists('notifications-subscribed-monthly', $post) ) ? true : false;

	        $options['notifications-unsubscribed-instant'] = ( array_key_exists('notifications-unsubscribed-instant', $post) ) ? true : false;
	        $options['notifications-unsubscribed-daily'] = ( array_key_exists('notifications-unsubscribed-daily', $post) ) ? true : false;
	        $options['notifications-unsubscribed-weekly'] = ( array_key_exists('notifications-unsubscribed-weekly', $post) ) ? true : false;
	        $options['notifications-unsubscribed-monthly'] = ( array_key_exists('notifications-unsubscribed-monthly', $post) ) ? true : false;
        }

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
				<?php $options = SendPress_Option::get('notification_options');?>
				<p> 
					<div class="left"><label for="enable-notifications">Enable Notifications?</label></div>
					<input class="ibutton" type="checkbox" value="<?php echo $options['notifications-enable']; ?>" name="notifications-enable" id="notifications-enable" <?php checked( $options['notifications-enable'], true ); ?>/> 
				</p>
				<h3>Notification E-mail</h3>
				<!-- <a href="#" class="tooltip" rel="tooltip" data-toggle="tooltip" title="The name and e-mail you want notifications to be sent to."><i class="icon-question-sign"></i></a> -->
				<div class="boxer form-box">
					<div style="float: right; width: 45%;">
						<h4 class="nomargin"><?php _e('E-mail','sendpress'); ?></h4>
						<input name="toemail" tabindex=2 type="text" id="toemail" value="<?php echo $options['email']; ?>" class="regular-text sp-text">
					</div>	
					<div style="width: 45%; margin-right: 10%">
						<h4 class="nomargin"><?php _e('To Name','sendpress'); ?></h4>
						<input name="toname" tabindex=1 type="text" id="toname" value="<?php echo $options['name']; ?>" class="regular-text sp-text">
					</div>
				</div>
				<h3>Notification Settings</h3>
				<p>Select the notifications you'd like to receive and how often you'd like to receive them.</p>
				
				<div class="half">
					<div class="well">
						<h4>User Subscribed:</h4>

						<div class="notification-container half">
							<div class="left"><label for="notifications-subscribed-instant">Instantly</label></div>
					  		<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-instant']; ?>" name="notifications-subscribed-instant" id="notifications-subscribed-instant" <?php checked( $options['notifications-subscribed-instant'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
						</div>
						<div class="notification-container half right">
					  		<div class="left"><label for="notifications-subscribed-daily">Daily</label></div>
					  		<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-daily']; ?>" name="notifications-subscribed-daily" id="notifications-subscribed-daily" <?php checked( $options['notifications-subscribed-daily'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
					  	</div>
						<div class="notification-container half">
					  		<div class="left"><label for="notifications-subscribed-weekly">Weekly</label></div>
					  		<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-weekly']; ?>" name="notifications-subscribed-weekly" id="notifications-subscribed-weekly" <?php checked( $options['notifications-subscribed-weekly'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
					  	</div>
						<div class="notification-container half right">
					  		<div class="left"><label for="notifications-subscribed-monthly">Monthly</label></div>
					  		<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-monthly']; ?>" name="notifications-subscribed-monthly" id="notifications-subscribed-monthly" <?php checked( $options['notifications-subscribed-monthly'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/>
					  	</div>
					</div>
				</div>
				<div class="half right">
					<div class="well">
						<h4>User Unsbscribed:</h4>
						<div class="notification-container half">
							<div class="left"><label for="notifications-unsubscribed-instant">Instantly</label></div>
						  	<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-instant']; ?>" name="notifications-unsubscribed-instant" id="notifications-unsubscribed-instant" <?php checked( $options['notifications-unsubscribed-instant'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
						</div>
						<div class="notification-container half right">
						<div class="left"><label for="notifications-unsubscribed-daily">Daily</label></div>
					  	<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-daily']; ?>" name="notifications-unsubscribed-daily" id="notifications-unsubscribed-daily" <?php checked( $options['notifications-unsubscribed-daily'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
					  	</div>
						<div class="notification-container half">
					  	<div class="left"><label for="notifications-unsubscribed-weekly">Weekly</label></div>
					  	<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-weekly']; ?>" name="notifications-unsubscribed-weekly" id="notifications-unsubscribed-weekly" <?php checked( $options['notifications-unsubscribed-weekly'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
					  	</div>
						<div class="notification-container half right">
					  	<div class="left"><label for="notifications-unsubscribed-monthly">Monthly</label></div>
					  	<input class="ibutton optional-notifications" type="checkbox" value="<?php echo $options['notifications-subscribed-monthly']; ?>" name="notifications-unsubscribed-monthly" id="notifications-unsubscribed-monthly" <?php checked( $options['notifications-unsubscribed-monthly'], true ); ?><?php if(!$options['notifications-enable']){ echo ' disabled="disabled"'; }?>/> 
					  	</div>
					</div>
				</div>
				<?php wp_nonce_field($sp->_nonce_value); ?>
			</form>
		</div>
		<?php
	}

}