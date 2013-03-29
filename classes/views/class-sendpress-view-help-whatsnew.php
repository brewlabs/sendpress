<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Help_Whatsnew extends SendPress_View{
	function prerender($sp){
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

	function page_start(){

	}

	function page_end(){

	}

	function html($sp){
		?>
		<div class="wrap about-wrap">

<h1>Welcome to SendPress <?php echo SENDPRESS_VERSION; ?></h1>

<div class="about-text">Thank you for updating to the latest version! SendPress <?php echo SENDPRESS_VERSION; ?> has multiple bug fixes and other minor updates. We hope you like it.</div>

<div class="sp-badge">Version <?php echo SENDPRESS_VERSION; ?></div>

<h2 class="nav-tab-wrapper">
	<a href="#" class="nav-tab nav-tab-active">
		What’s New	</a><!--<a href="credits.php" class="nav-tab">
		Credits	</a><a href="freedoms.php" class="nav-tab">
		Freedoms	</a>-->
</h2>

<div class="changelog">
	<h3>New Pro Options</h3>

	<div class="feature-section col two-col">
		<p style="padding: 5px;">
		<img alt="" src="<?php echo SENDPRESS_URL;?>/img/whatsnew-header.jpg" class="image-100">
		</p>
		<div>
			<h4>New Sending Options</h4>
			<p>Send your emails with SendGrid, MailJet or a Custom Provider. Amazon SES and more coming soon.</p>
		</div>
		<div class="last-feature">
			<h4>API Sending for SendGrid</h4>
			<p>Blocked ports? no problem use SendGrid API sending to get your emails out without issue.</p>
		</div>
	</div>
</div>
<!--
<div class="changelog">
	<h3>Pro Add-ons</h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo SENDPRESS_URL;?>/img/pro-reports.jpg" class="image-66">
		<h4>Coming in February SendPress Pro</h4>
		<p>Take your newsletters and marketing to the next level with advanced, flexible, and elegant add-ons.</p>
		<p>SendPress Pro is built to extend SendPress FREE to a full Email Marketing System like MailChimp, Constant Contact, etc.</p>
		<p>The initial release includes Advanced Reports, Spam Testing, Sending via SendGrid*, Custom SMTP and of course access to our Priority Support site.</p>
		<p><small>*SendGrid Account Required</small></p>
	</div>
</div>

<div class="changelog">
	<h3>Forms Ready for Anywhere</h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="<?php echo SENDPRESS_URL;?>/img/new-forms.jpg" class="image-66">
		<h4>New Forms to use on your website and more</h4>
		<p>The new form options allow for a wide array of setups. You can use an iframe, a shortcode, plain old html or the API to add a user from code.</p>
	</div>
</div>
-->
<!--
<div class="changelog">
	<h3>Smoother Experience</h3>

	<div class="feature-section images-stagger-right">
		<img alt="" src="http://joshlmbprd.whipplehill.com/wp2/wp-admin/images/screenshots/about-color-picker.png" class="image-30">
		<h4>Better Accessibility</h4>
		<p>WordPress supports more usage modes than ever before. Screenreaders, touch devices, and mouseless workflows all have improved ease of use and accessibility.</p>

		<h4>More Polish</h4>
		<p>A number of screens and controls have been refined. For example, a new color picker makes it easier for you to choose that perfect shade of blue.</p>
	</div>
</div>
-->
<div class="changelog">
	<h3>Even More</h3>

	<div class="feature-section col three-col">
		<div>
			<h4>Better Support for PHP 5.2</h4>
			<p>Fixed multiple issue's with older versions of php.</p>
		</div>
		<div>
			<h4>Updated Permissions Settings</h4>
			<p>Added support for more WordPress roles and fixed email editing bugs</p>
		</div>
		<div class="last-feature">
			<h4>Added Support for Twenty Ten</h4>
			<p>SendPress is now aware of styling for Twenty Ten. This allows for embeding SendPress subscription management screen in theme.</p>
	
		</div>
	</div>

	<div class="feature-section col three-col">
		<div>
			<h4>Give us a Review</h4>
			<p>Tell us how we are doing. We love to know what you think and it encourages us to make SendPress even better. Help us out and give us a <a href="http://wordpress.org/support/view/plugin-reviews/sendpress" target="_blank">review today</a>. It's easy and free :)</p>
		</div>
		<div>
					<h4>Bug Fixes and Performance Updates</h4>
			<p>With this release we fixed multiple small issues and worked to improve overall performance using plugins like <a href="http://wordpress.org/extend/plugins/p3-profiler/" target="_blank">P3 - Plugin Performance Profiler</a>.</p>
	

		</div>
	
		<div class="last-feature">
			<h4>Request a Feature</h4>
			<p>Got something you would like to see? Please add it to our <a href="http://sendpress.uservoice.com">Uservoice</a>.</p>
		</div>
	</div>
</div>

<div class="changelog">
	<h3>Whats Ahead</h3>

	<div class="feature-section col three-col">
		<div>
			<h4>Post Notifications</h4>
			<p>Subscribers will be able to get your new post's via email.</p>
		</div>
		<div>
			<h4>Send to WordPress Roles</h4>
			<p>We are working on sending emails to your WordPress users.</p>
		</div>
		<div class="last-feature">
			<h4>Admin Notifications</h4>
			<p>Get emails when someone subscribers or unsubscribes from your lists.</p>
	
		</div>
	</div>

	
</div>


<div class="return-to-dashboard">
		Back to SendPress: <a href="<?php echo SendPress_Admin::link('Emails'); ?>">Emails</a> |
		<a href="<?php echo SendPress_Admin::link('Reports'); ?>">Reports</a> |
		<a href="<?php echo SendPress_Admin::link('Subscribers'); ?>">Subscribers</a> |
		<a href="<?php echo SendPress_Admin::link('Queue'); ?>">Queue</a> |
		<a href="<?php echo SendPress_Admin::link('Settings'); ?>">Settings</a> 
		<?php if( SendPress_Option::get('beta') ) { ?>
		|
		<a href="<?php echo SendPress_Admin::link('Pro'); ?>">Pro Add-ons</a> 
		<?php } ?>
</div>
<br>
</div>
		<?php
				
	}

}
// Add Access Controll!
SendPress_Admin::add_cap('Pro','sendpress_view');
//SendPress_View_Overview::cap('sendpress_access');

