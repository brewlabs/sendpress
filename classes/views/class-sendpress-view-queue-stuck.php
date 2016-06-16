<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_View_Queue
*
* @uses     SendPress_View
*
*/
class SendPress_View_Queue_Stuck extends SendPress_View_Queue {


	function admin_init(){
		add_action('load-sendpress_page_sp-queue',array($this,'screen_options'));

		SendPress_Data::clean_queue_table();
		


	}



	function screen_options(){

		$screen = get_current_screen();
	 	
	 
		$args = array(
			'label' => __('Emails per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_queue_per_page'
		);
		add_screen_option( 'per_page', $args );
	}
	
	function empty_queue(  ){
		//$this->security_check();
		SendPress_Data::delete_stuck_queue_emails();
		SendPress_Admin::redirect('Queue_Stuck');
	}

	function reset_queue(){
		//$this->security_check();
		SendPress_Data::requeue_emails();
		SendPress_Admin::redirect('Queue');
	}

	function pause_queue(){
		//$this->security_check();
		$pause_sending = SendPress_Option::get('pause-sending','no');
		//Stop Sending for now
		if($pause_sending == 'yes'){
			SendPress_Option::set('pause-sending','no');
		} else {
			SendPress_Option::set('pause-sending','yes');
		}
		SendPress_Admin::redirect('Queue');
	}

	function reset_counters(){
		//$this->security_check();
		SendPress_Manager::reset_counters();
		SendPress_Admin::redirect('Queue');
	}

	function html() {

		 SendPress_Tracking::event('Queue Tab');
	if( SPNL()->validate->_isset('cron') ){
		SPNL()->fetch_mail_from_queue();
	}	

		//Create an instance of our package class...
	$testListTable = new SendPress_Queue_Stuck_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();
	SendPress_Option::set('no_cron_send', 'false');
	SPNL()->cron_start();

	$open_info = array(
				"id"=>13,
				"report"=> 10,
				"view"=>"open"
				);
	

	?>

<br>
	<div id="taskbar" class="lists-dashboard rounded group"> 

	<div id="button-area">  
	<?php 
	$pause_sending = SendPress_Option::get('pause-sending','no');
	$txt = __('Pause Sending','sendpress');
		//Stop Sending for now
		if($pause_sending == 'yes'){
			$txt = __('Resume Sending','sendpress');
		}
	?>
	<div class="btn-group">
		<!--
	<a class="btn btn-large btn-default " href="<?php echo SendPress_Admin::link('Queue'); ?>&action=pause-queue" ><i class="icon-repeat icon-white "></i> <?php echo $txt; ?></a>

	<a id="send-now" class="btn btn-primary btn-large " data-toggle="modal" href="#sendpress-sending"   ><i class="icon-white icon-refresh"></i> <?php _e('Send Emails Now','sendpress');?></a>
	-->
	</div>
	</div>
	<?php
		$emails_per_day = SendPress_Option::get('emails-per-day');
		if($emails_per_day == 0){
			$emails_per_day = __('Unlimited','sendpress');
		}
	  $emails_per_hour =  SendPress_Option::get('emails-per-hour');
	  $hourly_emails = SendPress_Data::emails_sent_in_queue("hour");
	  $emails_so_far = SendPress_Data::emails_sent_in_queue("day");
	  $autocron = SendPress_Option::get('autocron','no');
		//print_r(SendPress_Data::emails_stuck_in_queue());
		?>

		
		<h2><strong><?php echo $emails_so_far; ?></strong> <?php _e('of a possible','sendpress'); ?> <strong><?php echo $emails_per_day; ?></strong> <?php _e('emails sent in the last 24 hours','sendpress'); ?>.</h2>
		<h2><strong><?php  echo $hourly_emails; ?></strong> <?php _e('of a possible','sendpress'); ?> <strong><?php echo $emails_per_hour; ?></strong> <?php _e('emails sent in the last hour','sendpress'); ?>.</h2>
		<small><?php _e('You can adjust these settings here','sendpress'); ?>: <a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>"><?php _e('Settings','sendpress'); ?> > <?php _e('Sending Account','sendpress'); ?></a>.</small>
 		<?php
 		if(  $autocron == 'no'){
$offset = get_option( 'gmt_offset' ) * 60 * 60; // Time offset in seconds
$local_timestamp = wp_next_scheduled('sendpress_cron_action') + $offset;

?><br><small><?php _e('The cron will run again around','sendpress'); ?>: <?php
echo date_i18n( get_option('date_format') .' '. get_option('time_format'), $local_timestamp);
?></small>
<?php } ?>
 		<br><br>
		</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" action="<?php echo SendPress_Admin::link('Queue_Stuck'); ?>" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	     <input type="hidden" name="page" value="<?php echo SPNL()->validate->page() ?>" /> 
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<br>
		<a class="btn btn-large btn-success " href="<?php echo SendPress_Admin::link('Queue'); ?>&action=reset-queue" ><i class="icon-repeat icon-white "></i> <?php _e('Re-queue All Emails','sendpress'); ?></a><br><br>
	<form  method='get'>
		<input type='hidden' value="<?php echo SPNL()->validate->page(); ?>" name="page" />
		
		<input type='hidden' value="empty-queue" name="action" />
		<a class="btn btn-large  btn-danger" data-toggle="modal" href="#sendpress-empty-queue" ><i class="icon-warning-sign "></i> <?php _e('Delete All Stuck Emails','sendpress'); ?></a>
		<?php wp_nonce_field($this->_nonce_value); ?>
	</form>
<div class="modal fade" id="sendpress-empty-queue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3><?php _e('Really? Delete All Emails stuck in the Queue.','sendpress');?></h3>
	</div>
	<div class="modal-body">
		<p><?php _e('This will remove all stuck emails from the queue without attempting to send them','sendpress');?>.</p>
	</div>
	<div class="modal-footer">
	<a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('No! I was Joking','sendpress');?></a><a href="<?php echo SendPress_Admin::link('Queue_Stuck'); ?>&action=empty-queue" id="confirm-delete" class="btn btn-danger" ><?php _e('Yes! Delete All Emails','sendpress');?></a>
	</div>
</div></div>
</div>

<div class="modal fade" id="sendpress-sending" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
	<div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3><?php _e('Sending Emails','sendpress');?></h3>
  </div>
  <div class="modal-body">
    <div id="sendbar" class="progress progress-striped
     active">
  <div id="sendbar-inner" class="progress-bar"
       style="width: 40%;"></div>
</div>
	Sent <span id="queue-sent">-</span> <?php _e('of','sendpress');?> <span id="queue-total">-</span> <?php _e('emails','sendpress'); ?>.<br>
	<?php _e('You are currently sending 1 email approximately every','sendpress'); ?> <?php 
	$hour = SendPress_Option::get('emails-per-hour');
	if($hour != 0){
	$rate = 3600 / $hour; 
	if($rate > 8){
			$rate = 8;
		}
	} else {
		$rate = "0.25";
	}

	echo $rate;

	?> <?php _e('seconds','sendpress'); ?>.<br>
	<?php _e('You are also limited to','sendpress'); ?> <?php echo $hour; ?> <?php _e('emails per hour','sendpress'); ?>.<br>
	<?php _e('To change these settings go to','sendpress'); ?> <a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>"><?php _e('Settings','sendpress'); ?> > <?php _e('Sending Account','sendpress'); ?></a>.
  </div>
  <div class="modal-footer">
   <?php _e('If you close this window sending will stop. ','sendpress');?><a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('Close','sendpress');?></a>
  </div>
</div>
</div></div>
<?php
	}

}
SendPress_Admin::add_cap('Queue','sendpress_queue');