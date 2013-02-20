<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Settings extends SendPress_View {
	
	

	function sub_menu($sp){ 
	?>

	<div class="navbar">
		<div class="navbar-inner">
		<ul class="nav">
			<!--
		  <li <?php if($sp->_current_view == ''){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings::link(); ?>"><i class="icon-envelope"></i> <?php _e('Basic Setup','sendpress'); ?></a></li>
		 -->
		  <li <?php if($sp->_current_view == 'styles'){ ?>class="active"<?php } ?> >
		    <a href="<?php echo SendPress_View_Settings_Styles::link(); ?>"><i class="icon-pencil "></i> <?php _e('Basic Settings & Styles','sendpress'); ?></a>
		  </li>
		  <li <?php if($sp->_current_view == 'activation'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Activation::link(); ?>"><i class=" icon-bullhorn"></i> <?php _e('System Emails & Pages','sendpress'); ?></a></li>
		 <li <?php if($sp->_current_view == 'account'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Account::link(); ?>"><i class="icon-envelope "></i> <?php _e('Sending Account','sendpress'); ?></a></li>
			<li <?php if($sp->_current_view == 'access'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Access::link(); ?>"><i class="icon-user "></i> <?php _e('Permissions','sendpress'); ?></a></li>
			<li <?php if($sp->_current_view == 'notifications'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Notifications::link(); ?>"><i class="icon-bell"></i> <?php _e('Notifications','sendpress'); ?></a></li>	
			<li <?php if($sp->_current_view == 'advanced'){ ?>class="active"<?php } ?> ><a href="<?php echo SendPress_View_Settings_Advanced::link(); ?>"><i class=" icon-wrench "></i> <?php _e('Advanced','sendpress'); ?></a></li>		
		</ul>
	</div>
</div>
	<?php
	}
	function prerender($sp){
		if(  $sp->_current_view == '' ){
			SendPress_View_Settings_Styles::redirect();
		}
	}
	function html($sp) {
		echo "Parent view needs a child.";
	}

}
SendPress_View_Settings::cap('sendpress_settings');