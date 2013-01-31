<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Help_Whatsnew extends SendPress_View{
	function prerender($sp){
		wp_enqueue_script( 'dashboard' );
		sp_add_help_widget( 'help_support', 'Support Information', array(&$this,'help_support'));
		sp_add_help_widget( 'help_knowledge', 'Recent Knowledge Base Articles', array(&$this,'help_knowledge'),'side' );
		sp_add_help_widget( 'help_debug', 'Debug Information', array(&$this,'help_debug'), 'side');
		
		sp_add_help_widget( 'help_blog', 'Recent Blog Posts', array(&$this,'help_blog'),'normal',  array(&$this,'help_blog_control') );
		sp_add_help_widget( 'help_shortcodes', 'Shortcode Cheat Sheet', array(&$this,'help_shortcodes') ,'normal');
		sp_add_help_widget( 'help_editemail', 'Customizing Emails', array(&$this,'help_editemail') ,'normal');

	}

	function page_start(){

	}

	function page_end(){

	}

	function html($sp){
		?>
		<div class="wrap about-wrap">

<h1>Welcome to SendPress <?php echo SENDPRESS_VERSION; ?></h1>

<div class="about-text">Thank you for updating to the latest version! SendPress <?php echo SENDPRESS_VERSION; ?> is more polished and enjoyable than ever before. We hope you like it.</div>

<div class="sp-badge">Version <?php echo SENDPRESS_VERSION; ?></div>

<h2 class="nav-tab-wrapper">
	<a href="#" class="nav-tab nav-tab-active">
		What’s New	</a><!--<a href="credits.php" class="nav-tab">
		Credits	</a><a href="freedoms.php" class="nav-tab">
		Freedoms	</a>-->
</h2>

<div class="changelog">
	<h3>New Queue Manager</h3>

	<div class="feature-section col two-col">
		<img alt="" src="<?php echo SENDPRESS_URL;?>/img/whatsnew-header.jpg" class="image-100">

		<div>
			<h4>Daily and Hourly Sending Limits</h4>
			<p>Easily stay on your hosting providers good side. With the new sending limits you don't have to worry about going over your limit or being marked for abuse.</p>
		</div>
		<div class="last-feature">
			<h4>Better Queue Information</h4>
			<p>Quickly see how many emails are left to be sent and how close you are to your limit. Once your daily limit is reached sending will resume in 24 hours.</p>
		</div>
	</div>
</div>

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
			<h4>Screen Options</h4>
			<p>Every table view now uses settings to adjust the number of records displayed.</p>
		</div>
		<div>
			<h4>Updated Basic Reports</h4>
			<p>Quickly see how main Recipients an email was sent to. Also you can see the number of emails actually sent and the number left in the Queue.</p>
		</div>
		<div class="last-feature">
			<h4>Support for Genesis eNews Extended</h4>
			<p>If you run a theme that uses the <a href="http://studiopress.com" target="_blank">Genesis Framework</a>, this option makes it easy to create a subscription area to match your theme. Check out <a href="http://wordpress.org/extend/plugins/genesis-enews-extended" target="_blank">Genesis eNews Extended</a> on WordPress.org.</p>
	
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
			<h4>Our First Contributer</h4>
			<p>Big thanks to <a href="https://github.com/mattsnowboard" target="_blank">Matt Durak</a> for helping make SendPress better. You can as well, our code is <a href="https://github.com/brewlabs/sendpress" target="_blank">GitHub</a>.</p>
		</div>
	</div>
</div>

<div class="return-to-dashboard">
		Back to SendPress: <a href="<?php echo SendPress_View_Emails::link(); ?>">Emails</a> |
		<a href="<?php echo SendPress_View_Reports::link(); ?>">Reports</a> |
		<a href="<?php echo SendPress_View_Subscribers::link(); ?>">Subscribers</a> |
		<a href="<?php echo SendPress_View_Queue::link(); ?>">Queue</a> |
		<a href="<?php echo SendPress_View_Settings::link(); ?>">Settings</a> 
		<?php if( SendPress_Option::get('beta') ) { ?>
		|
		<a href="<?php echo SendPress_View_Pro::link(); ?>">Pro Add-ons</a> 
		<?php } ?>
</div>
<br>
</div>
		<?php
				
	}

}
// Add Access Controll!
SendPress_View_Help::cap('sendpress_view');
//SendPress_View_Overview::cap('sendpress_access');

