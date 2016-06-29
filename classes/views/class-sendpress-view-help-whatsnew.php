<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Help_Whatsnew extends SendPress_View{
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

	function page_start(){

	}

	function page_end(){

	}

	function html(){
		SendPress_Option::base_set('update-info','hide');
			?>
			
		<div class="wrap about-wrap">

<h1>Welcome to SendPress <?php echo SENDPRESS_VERSION; ?></h1>

<div class="about-text">Thank you for updating! SendPress <?php echo SENDPRESS_VERSION; ?> brings you one of our biggest releases yet.</div>

<!--<div class="changelog point-releases">
	<h3>Maintenance & Bug Fix Release</h3>
	<p><strong>Version <?php echo SENDPRESS_VERSION; ?></strong> addressed several bugs. For more information, see <a href="http://wordpress.org/plugins/sendpress/changelog/">the changelog</a>.</p>
</div>-->
<hr>
<div class="changelog">
<div class="about-overview">
	<h2 class="about-headline-callout">Introducing New Responsive Templates</h2>
	<img class="about-overview-img" src="<?php echo SENDPRESS_URL;?>/img/v1update.png">
	<p>Create as many responsive templates as you like with ease. Want to send 100% custom HTML emails upgrade to Pro.</p>
	</div>
	<hr>
	<div class="feature-section col two-col">
		<div class="col-1">
		<!-- focus.png -->
			<img src="<?php echo SENDPRESS_URL;?>/img/simpleeditor.png">
			</div>
		<div class="col-2 last-feature">
			<h3>Focus on your content</h3>
			<p>Writing and editing is smoother and simpler with a new editor layout and email creation screen. Designed to support our upcoming multicolumn email templates.</p>
		</div>
	</div>
	<hr>
	<div class="feature-section col two-col">
		<div class="col-1">
		<!-- focus.png -->
				<h3>All new Forms &amp; Widget</h3>
			<p>Creating that perfect signup form is now much simpler. Edit settings in one place to manage both widgets and forms shortcodes.</p>
		
			</div>
		<div class="col-2 last-feature">
		<img src="<?php echo SENDPRESS_URL;?>/img/forms.png">
		</div>
	</div>
<hr>
	<div class="feature-section col three-col about-updates">
		<div class="col-1">
			<img src="<?php echo SENDPRESS_URL;?>/img/prot.png?1">
			<h3>Custom HTML</h3>
			<p>SendPress PRO adds the ability to send any HTML template you want.</p>
		</div>
		<div class="col-2">
			<img src="<?php echo SENDPRESS_URL;?>/img/sendhistory.png?1">
			<h3>Send History</h3>
			<p>Keep an eye on the email messages being sent and look up your recent send history.</p>
		</div>
		<div class="col-3 last-feature">
			<img src="<?php echo SENDPRESS_URL;?>/img/autocron.png?1">
			<h3>Pro Discount</h3>
			<p>Enable AutoCron and recieve a discount from pro. Discounts are emails to the admin email of the site.</p>
		</div>
	</div>
</div>

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
			<h4>Ninja Forms</h4>
			<p>We are working on an exentsion so you can collect all your subscriber data with the <a href="http://ninjaforms.com/">Ninja Forms Plugin</a>.</p>
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
		<?php
				
	}

}
// Add Access Controll!
SendPress_Admin::add_cap('Pro','sendpress_view');
//SendPress_View_Overview::cap('sendpress_access');

