<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings_Advanced extends SendPress_View_Settings {
	
	function save($post, $sp){

	if(isset( $post['allow_tracking'] )){
		SendPress_Option::set('allow_tracking', 'yes' );
		SendPress_Option::set('feedback', 'yes' );
	} else {
		SendPress_Option::set('allow_tracking', 'no' );
		SendPress_Option::set('feedback', 'no' );
	}
	if(isset( $post['old_permalink'] )){
		SendPress_Option::set('old_permalink', true );
	} else {
		SendPress_Option::set('old_permalink', false );
	}

	$widget_options =  array();

        $widget_options['widget_options']['load_css'] = 0;
        $widget_options['widget_options']['load_ajax'] = 0;
        $widget_options['widget_options']['load_scripts_in_footer'] = 0;
        if(isset($_POST['load_css'])){
            $widget_options['widget_options']['load_css'] = $_POST['load_css'];
        }
        if(isset($_POST['load_ajax'])){
            $widget_options['widget_options']['load_ajax'] = $_POST['load_ajax'];
        }
        if(isset($_POST['load_scripts_in_footer'])){
            $widget_options['widget_options']['load_scripts_in_footer'] = $_POST['load_scripts_in_footer'];
        }

        SendPress_Option::set($widget_options); 

        SendPress_Admin::redirect('Settings_Advanced');
	}

	function reset_transients(){
		delete_transient('sendpress_weekly_post_notification_check');
		delete_transient('sendpress_daily_post_notification_check');
		delete_transient('sendpress_post_notification_check');
		wp_clear_scheduled_hook('sendpress_post_notification_check');
	}

	function html($sp) {
		?><form method="post" id="post">
<div style="float:right;" >
	<a href="<?php echo SendPress_Admin::link('Settings_Advanced'); ?>" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<br class="clear">
		<br class="clear">
<div class="boxer form-box">
	<div style="float: right; width: 45%;">
		<h2>Javascript & CSS</h2>
		<?php 
				$widget_options = SendPress_Option::get('widget_options');
				//print_r($widget_options);
			?>
			<input class="turnoff-css-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_css']; ?>" type="checkbox" <?php if( $widget_options['load_css'] == 1 ){ echo 'checked'; } ?> id="load_css" name="load_css"/>  <?php _e('Disable front end CSS','sendpress'); ?><br>
			
			<input class="turnoff-ajax-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_ajax']; ?>" type="checkbox" <?php if( $widget_options['load_ajax'] == 1 ){ echo 'checked'; } ?> id="load_ajax" name="load_ajax"/>  <?php  _e('Disable signup form ajax','sendpress'); ?><br>

			
			<input class="footer-scripts-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_scripts_in_footer']; ?>" type="checkbox" <?php if( $widget_options['load_scripts_in_footer'] == 1 ){ echo 'checked'; } ?> id="load_scripts_in_footer" name="load_scripts_in_footer"/>  <?php _e('Load Javascript in Footer','sendpress'); ?> 
		
			<h2>Permalink Settings</h2>
			<?php $ctype = SendPress_Option::get('old_permalink'); ?>
			<input type="checkbox" name="old_permalink" value="true" <?php if($ctype){echo "checked='checked'"; } ?> /> Use old permalink with ?sendpress=.
			<br><br>
			<h2>Table Info</h2>
			<pre><?php echo SendPress_DB_Tables::check_setup_support(); ?></pre>
			<a class="btn btn-danger" href="<? echo SendPress_Admin::link('Settings_Install'); ?>">Install Missing Tables</a>
			

	<br class="clear">
	</div>	
	<div style="width: 45%; margin-right: 10%">
		<h2>Tracking</h2>
		<?php $ctype = SendPress_Option::get('allow_tracking'); ?>
	<input type="checkbox" name="allow_tracking" value="yes" <?php if($ctype=='yes'){echo "checked='checked'"; } ?> /> Allow tracking of this WordPress installs anonymous data.
		<p>	
	To maintain a plugin as big as SendPress, we need to know what we're dealing: what kinds of other plugins our users are using, what themes, etc. Please allow us to track that data from your install. It will not track any user details, so your security and privacy are safe with us.</p>

	<?php do_action('sendpress_advanced_settings'); ?>
	<br class="clear">
	
	</div>

</div>
<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<?php
	}

}