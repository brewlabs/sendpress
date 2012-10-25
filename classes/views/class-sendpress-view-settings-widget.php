<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Settings_Widget extends SendPress_View_Settings {
	
	function html($sp) {
		?>



<div style="float:right;" >
	<a href="?page=sp-settings&view=widget" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<br class="clear">
<div id="widget-options" class="boxer form-box clearfix">

	<div id="shortcode">
		<h3><?php _e('Signup Shortcode','sendpress'); ?></h3>
		<p><?php _e('If you would rather add the SendPress signup form to a page, you can use the following short code.  If you want more detailed information on how to use the short code check out our <a href="http://manage.sendpress.com/support/knowledgebase/how-to-use-the-sign-up-shortcode/" target="_blank">knowledge base</a>.','sendpress'); ?></p>
		<pre>[sendpress-signup listids='1']</pre>
		<!-- <ul>
			<li>listids='1' </li>
			<li>firstname_label='First Name'</li>
			<li>lastname_label='Last Name' </li>
			<li>email_label='E-Mail' </li>
			<li>display_firstname='true' </li>
			<li>display_lastname='false' </li>
			<li>label_display='false' </li>
			<li>desc= '' </li>
			<li>label_width=100 </li>
			<li>thank_you='Thank you for subscribing!' </li>
			<li>button_text='Submit'</li>
		</ul> -->
		
	</div>

	<div id="widget-settings">
		<h3><?php _e('Signup Widget Settings','sendpress'); ?></h3>
		<form method="post" id="post">
			<?php 
				$widget_options = $sp->get_option('widget_options');
				//print_r($widget_options);
			?>
			<label for="load_css"><?php _e('Disable front end CSS','sendpress'); ?></label>
			<input class="turnoff-css-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_css']; ?>" type="checkbox" <?php if( $widget_options['load_css'] == 1 ){ echo 'checked'; } ?> id="load_css" name="load_css"/><br><br>
			
			<label for="load_ajax"><?php  _e('Disable signup form ajax','sendpress'); ?></label>
			<input class="turnoff-ajax-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_ajax']; ?>" type="checkbox" <?php if( $widget_options['load_ajax'] == 1 ){ echo 'checked'; } ?> id="load_ajax" name="load_ajax"/> <br><br>

			<label for="load_scripts_in_footer"><?php _e('Load Javascript in Footer','sendpress'); ?></label>
			<input class="footer-scripts-checkbox sendpress_checkbox" value="<?php echo $widget_options['load_scripts_in_footer']; ?>" type="checkbox" <?php if( $widget_options['load_scripts_in_footer'] == 1 ){ echo 'checked'; } ?> id="load_scripts_in_footer" name="load_scripts_in_footer"/> 

			<input type="hidden" name="action" value="temaplte-widget-settings" />
			<!-- <button class="btn btn-primary" type="submit">Save</button> -->
			<?php wp_nonce_field($sp->_nonce_value); ?>

		</form>
		<h4><?php _e('Front end CSS','sendpress'); ?></h4>
		<pre>
			<?php echo file_get_contents(SENDPRESS_PATH.'css/front-end.css'); ?>
		</pre>
	</div>

	

</div>
<?php
	}

}