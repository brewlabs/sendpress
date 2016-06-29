<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Templates extends SendPress_View_Emails{
	function prerender(){
		wp_enqueue_script( 'dashboard' );
		/*
		sp_add_help_widget( 'help_support', 'Support Information', array(&$this,'help_support'));
		sp_add_help_widget( 'help_knowledge', 'Recent Knowledge Base Articles', array(&$this,'help_knowledge'),'side' );
		sp_add_help_widget( 'help_debug', 'Debug Information', array(&$this,'help_debug'), 'side');
		
		sp_add_help_widget( 'help_blog', 'Recent Blog Posts', array(&$this,'help_blog'),'normal',  array(&$this,'help_blog_control') );
		sp_add_help_widget( 'help_shortcodes', 'Shortcode Cheat Sheet', array(&$this,'help_shortcodes') ,'normal');
		sp_add_help_widget( 'help_editemail', 'Customizing Emails', array(&$this,'help_editemail') ,'normal');
		*/
	}

	

	function html(){
			?>
			
		<div class="wrap about-wrap">
<!--
<h1>Welcome to SendPress <?php echo SENDPRESS_VERSION; ?></h1>

<div class="about-text">Thank you for helping us reach 100K+ downloads and using SendPress.</div>
<div class="changelog point-releases">
	<h3>Maintenance & Bug Fix Release</h3>
	<p><strong>Version <?php echo SENDPRESS_VERSION; ?></strong> addressed several bugs. For more information, see <a href="http://wordpress.org/plugins/sendpress/changelog/">the changelog</a>.</p>
</div>
-->
<div class="changelog">
	<h2 class="about-headline-callout"><?php _e('Introducing Custom HTML Templates','sendpress'); ?></h2>

<p style="text-align:center;"><?php _e('Custom Templates Requires SendPress Pro v1.0+','sendpress'); ?></p>
	<img class="about-overview-img" src="<?php echo SENDPRESS_URL; ?>img/customedit.png">
	<div class="feature-section col three-col about-updates">
		<div class="col-1">
			<!--<img src="<?php echo SENDPRESS_URL;?>/img/sending-errors.png?1">-->
			<h3><?php _e('Send Any Template','sendpress'); ?></h3>
			<p><?php _e('SendPress allows you to send any HTML email template you wish.','sendpress'); ?></p>
		</div>
		<div class="col-2">
			<!--<img src="<?php echo SENDPRESS_URL;?>/img/sendhistory.png?1">-->
			<h3><?php _e('Template Tags','sendpress'); ?></h3>
			<p><?php _e('Quickly insert email content, subscriber information and social icons with our new tag system.','sendpress'); ?></p>
		</div>
		<div class="col-3 last-feature">
			<!--<img src="<?php echo SENDPRESS_URL;?>/img/autocron.png?1">-->
			<h3><?php _e('Upgrade to Pro','sendpress'); ?></h3>
			<p><?php _e('Go to <a href="https://sendpress.com/purchase-pricing/">SendPress.com</a> to get a license for SendPress Pro. Also if you enable AutoCron you should recive a 20% discount.','sendpress'); ?></p>
		</div>
	</div>
</div>
<!--
<hr>

<div class="changelog">
	<div class="feature-section col two-col">
		<div>
			<h3>Give us a Review</h3>
			<p>We love feedback so please let us know how we are doing.<br>Add your <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/sendpress">★★★★★</a> on <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/sendpress">wordpress.org</a></p>
			<h4>Help Translate</h4>
			<p>You can help translate on <a href="https://www.transifex.com/projects/p/sendpress/">Transifex</a>.</p>
			<h4>Report and Fix bugs</h4>
			<p>SendPress is on <a href="http://wordpress.org/support/plugin/sendpress">GitHub</a> feel free to patch bugs you find or please report them on the <a href="http://wordpress.org/support/plugin/sendpress">forum</a>.</p>
		
		</div>
		<div class="last-feature about-colors-img">
			<a href="http://wordpress.org/support/view/plugin-reviews/sendpress" target="_blank"><img class="about-overview-img" src="<?php echo SENDPRESS_URL;?>/img/review-us.png"></a>
		</div>
	</div>
</div>

<hr>



<div class="changelog">

	<div class="feature-section col three-col">
	<h3>Whats Ahead</h3>
		<div>
			<h4>Post Notifications</h4>
			<p>Subscribers will be able to get your new post's via email. Post Notifications will only be available with SendPress Pro.</p>
		</div>
		<div>
			<h4>Autoresponders</h4>
			<p>Automatically send emails to subscribers based on actions like link clicks, subscribe to list or email opens.</p>
		</div>
		<div class="last-feature">
			<h4>Custom fields</h4>
			<p>Add your own information to subscribers and collect the data you need.</p>
	
		</div>
	</div>

	
</div>


<div class="return-to-dashboard">
		SendPress: <a href="<?php echo SendPress_Admin::link('Emails'); ?>">Emails</a> |
		<a href="<?php echo SendPress_Admin::link('Reports'); ?>">Reports</a> |
		<a href="<?php echo SendPress_Admin::link('Subscribers'); ?>">Subscribers</a> |
		<a href="<?php echo SendPress_Admin::link('Queue'); ?>">Queue</a> |
		<a href="<?php echo SendPress_Admin::link('Settings'); ?>">Settings</a> 
		
		|
		<a href="<?php echo SendPress_Admin::link('Pro'); ?>">Pro</a>
</div>
<br>
</div>
-->
		<?php
				
	}

}
// Add Access Controll!
SendPress_Admin::add_cap('Emails_Templates','sendpress_email');
//SendPress_View_Overview::cap('sendpress_access');

