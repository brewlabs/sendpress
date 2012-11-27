<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_View_Settings extends SendPress_View {
	
	

	function sub_menu($sp){ 
	?>

	<div class="subnav">
		<ul class="nav nav-pills">
			<!--
		  <li <?php if($sp->_current_view == ''){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings::link();; ?>"><i class="icon-envelope"></i> <?php _e('Basic Setup','sendpress'); ?></a></li>
		 -->
		  <li <?php if($sp->_current_view == 'styles'){ ?>class="active"<?php } ?> >
		    <a href="<?php echo SendPress_View_Settings_Styles::link(); ?>"><i class="icon-envelope"></i> <?php _e('Basic Settings & Styles','sendpress'); ?></a>
		  </li>
		  <!--
		  <li <?php if($sp->_current_view == 'activation'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Activation::link(); ?>"><i class="icon-user"></i> <?php _e('Double Opt-in Email','sendpress'); ?></a></li>
			-->
		  <li <?php if($sp->_current_view == 'account'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Account::link(); ?>"><i class="icon-user"></i> <?php _e('Sending Account','sendpress'); ?></a></li>
			 <li <?php if($sp->_current_view == 'feedback'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Feedback::link();  ?>"><i class="icon-wrench"></i> <?php _e('Feedback','sendpress'); ?></a></li>	
			<li <?php if($sp->_current_view == 'widget'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Widget::link(); ?>"><i class="icon-cog"></i> <?php _e('Widget/Shortcode','sendpress'); ?></a></li>
			<li <?php if($sp->_current_view == 'access'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Access::link(); ?>"><i class="icon-lock"></i> <?php _e('Premissions','sendpress'); ?></a></li>		
		</ul>
	</div>

	<?php
	}
	function html($sp) {
		SendPress_View_Settings_Styles::redirect();
/*
		$default_styles_id = SendPress_Data::get_template_id_by_slug('user-style');
$post =  get_post( $default_styles_id );
?>
<form method="post" id="post">
	<input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
<br class="clear">
<div style="float:right;" >
	<a href="?page=sp-settings&view=information" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<input type="hidden" name="action" value="template-default-setup" />
<br class="clear">
<div class="boxer form-box">
<div style="float: right; width: 45%;">
	<h3>Socail Media</h3>
	<p>These items only show on the tempalte if a url is entered.</p>
	<p><label><?php _e('Twitter URL','sendpress'); ?>:</label>
	<input name="twitter" type="text" id="twitter" value="<?php echo $sp->get_option('twitter'); ?>" class="regular-text"></p>
<p><label><?php _e('Facebook URL','sendpress'); ?>:</label>
<input name="facebook" type="text" id="facebook" value="<?php echo $sp->get_option('facebook'); ?>" class="regular-text"></p>
<p><label><?php _e('LinkedIn URL','sendpress'); ?>:</label>
<input name="linkedin" type="text" id="linkedin" value="<?php echo $sp->get_option('linkedin'); ?>" class="regular-text"></p>
	<p class="alert alert-info">Make sure you include http:// in your links</p>
</div>	
<div style="width: 45%; margin-right: 10%">
<p><label><?php _e('From Name','sendpress'); ?>:</label>
	<input name="fromname" type="text" id="fromname" value="<?php echo $sp->get_option('fromname'); ?>" class="regular-text"></p>
<p><label><?php _e('From Email','sendpress'); ?>:</label>
<input name="fromemail" type="text" id="fromemail" value="<?php echo $sp->get_option('fromemail'); ?>" class="regular-text"></p>
<p><label style='width: 100%;'><?php _e('CAN-SPAM','sendpress'); ?>: <small><?php _e('required in the US.','sendpress'); ?></small></label>
<textarea cols="20" rows="10" class="large-text code" name="can-spam"><?php echo $sp->get_option('canspam'); ?></textarea>
<p><?php _e('<b>Tell recipients where you’re located.</b> Your message must include your valid physical postal address. This can be your current street address, a post office box you’ve registered with the U.S. Postal Service, or a private mailbox you’ve registered with a commercial mail receiving agency established under Postal Service regulations.','sendpress'); ?></p>
<?php _e('This is dictated under the <a href="http://business.ftc.gov/documents/bus61-can-spam-act-compliance-guide-business" target="_blank">Federal CAN-SPAM Act of 2003</a>.','sendpress'); ?>
					</p>
</div></div>

<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<?php
*/
	}

}
SendPress_View_Settings::cap('sendpress_settings');