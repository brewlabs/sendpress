<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_View_Emails_Postnotifications
*
* @uses     SendPress_View
*
* @package  SendPress
* @since 0.8.7
*
*/
class SendPress_View_Emails_Postnotifications extends SendPress_View_Emails {

	
	
	function html() {
		
		?>
		
		<div class="wrap about-wrap">

			<div class="changelog">
				<h2 class="about-headline-callout"><?php _e('Introducing Post Notifcations','sendpress'); ?></h2>

				<p style="text-align:center;"><?php _e('Post Notifcations Requires SendPress Pro v1.0+','sendpress'); ?></p>
				<img class="about-overview-img" src="<?php echo SENDPRESS_URL; ?>img/postnotifications.jpg"><br><br>
				<p>Post notifications automatically queues e-mails to your readers with your new posts.  Customize how your posts look using a custom post template, and keep your readers engaged.</p>
				<div class="feature-section col three-col about-updates">
					<div class="col-1">
						<!--<img src="<?php echo SENDPRESS_URL;?>/img/sending-errors.png?1">-->
						<h3><?php _e('Custom Post Templates','sendpress'); ?></h3>
						<p><?php _e('SendPress Post Notifications can be formatted using HTML and custom templae tags.','sendpress'); ?></p>
					</div>
					<div class="col-2">
						<!--<img src="<?php echo SENDPRESS_URL;?>/img/sendhistory.png?1">-->
						<h3><?php _e('Updates on your schedule','sendpress'); ?></h3>
						<p><?php _e('Send updates to your readers whenever you want.  Send instantly after you publish a post, or daily/weekly updates of all your posts.','sendpress'); ?></p>
					</div>
					<div class="col-3 last-feature">
						<!--<img src="<?php echo SENDPRESS_URL;?>/img/autocron.png?1">-->
						<h3><?php _e('Upgrade to Pro','sendpress'); ?></h3>
						<p><?php _e('Go to <a href="https://sendpress.com/purchase-pricing/">SendPress.com</a> to get a license for SendPress Pro. Also if you enable AutoCron you should recive a 20% discount.','sendpress'); ?></p>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

}