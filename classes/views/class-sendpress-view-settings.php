<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings extends SendPress_View {
	
	

	function sub_menu($sp = false){ 
		 SendPress_Tracking::event('Settings Tab');
	?>

	<div class="navbar">
		<div class="navbar-inner">
		<ul class="nav">
			<!--
		  <li <?php if($sp->_current_view == ''){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings'); ?>"><i class="icon-envelope"></i> <?php _e('Basic Setup','sendpress'); ?></a></li>
		 -->
		  <li <?php if($sp->_current_view == 'styles'){ ?>class="active"<?php } ?> >
		    <a href="<?php echo SendPress_Admin::link('Settings_Styles'); ?>"><i class="icon-pencil "></i> <?php _e('Basic Settings & Styles','sendpress'); ?></a>
		  </li>
		<li <?php if($sp->_current_view == 'activation'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings_Activation'); ?>"><i class=" icon-bullhorn"></i> <?php _e('System Emails & Pages','sendpress'); ?></a></li>
		<li <?php if($sp->_current_view == 'account'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>"><i class="icon-envelope "></i> <?php _e('Sending Account','sendpress'); ?></a></li>
		<li <?php if($sp->_current_view == 'access'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings_Access'); ?>"><i class="icon-user "></i> <?php _e('Permissions','sendpress'); ?></a></li>
		<li <?php if($sp->_current_view == 'notifications'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings_Notifications'); ?>"><i class="icon-bell"></i> <?php _e('Notifications','sendpress'); ?></a></li>	
		<li <?php if($sp->_current_view == 'advanced'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_Admin::link('Settings_Advanced'); ?>"><i class=" icon-wrench "></i> <?php _e('Advanced','sendpress'); ?></a></li>	
		<?php do_action('sendpress_view_settings_menu', $sp->_current_view); ?>	
		</ul>
	</div>
</div>
	<?php
	}
	function prerender($sp = false){
		if(  $sp->_current_view == '' ){
			SendPress_Admin::redirect('Settings_Styles');
		}
	}
	function html($sp) {
		_e('Parent view needs a child.', 'sendpress');
	}

}
SendPress_Admin::add_cap('Settings','sendpress_settings');
