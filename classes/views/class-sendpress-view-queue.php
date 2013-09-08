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
class SendPress_View_Queue extends SendPress_View {


	function admin_init(){
		add_action('load-sendpress_page_sp-queue',array($this,'screen_options'));
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
	
	function empty_queue( $get, $sp ){
		SendPress_Data::delete_queue_emails();
		SendPress_Admin::redirect('Queue');
	}

	

	function reset_counters(){
		SendPress_Manager::reset_counters();
		SendPress_Admin::redirect('Queue');
	}

	function html($sp) {

	if(isset($_GET['cron'])){
		$sp->fetch_mail_from_queue();
	}	

		//Create an instance of our package class...
	$testListTable = new SendPress_Queue_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();
	SendPress_Option::set('no_cron_send', 'false');
	//$sp->fetch_mail_from_queue();
	$sp->cron_start();
	//echo $sp->get_key(). "<br>";

	$open_info = array(
				"id"=>13,
				"report"=> 10,
				"view"=>"open"
				);
	/*
			$x = $sp->encrypt_data($open_info);

		echo $x."<br>";
		$x = $sp->decrypt_data($x);

		print_r($x);
			echo "<br>";

		$d = $_GET['t'];
		$x = $sp->decrypt_data($d);

		print_r($x->id);
			echo "<br>";
		
		
		//echo wp_get_schedule('sendpress_cron_action_run');
		//
		$time_delay =  SendPress_Option::get('time-delay');
		echo $time_delay;
		echo date('l jS \of F Y H:i:s A',$time_delay );
		echo "<br>";
		$time = date('H:i:s');

echo $time;//11:09
		$time = date('H:i:s', $time_delay);

echo $time;//11:09
	*/
	?>


	<div id="taskbar" class="lists-dashboard rounded group"> 

	<div id="button-area">  
	<a id="send-now" class="btn btn-primary btn-large " data-toggle="modal" href="#sendpress-sending"   ><i class="icon-white icon-refresh"></i> <?php _e('Send Emails Now','sendpress');?></a>
	</div>
	<?php
		$emails_per_day = SendPress_Option::get('emails-per-day');
		if($emails_per_day == 0){
			$emails_per_day = __('Unlimited','sendpress');
		}
  $emails_per_hour =  SendPress_Option::get('emails-per-hour');
  $emails_today = SendPress_Option::get('emails-today');
  $emails_so_far = isset($emails_today[date("z")]) ? $emails_today[date("z")] : 0;
	?>
		
		<h2><strong><?php echo $emails_so_far; ?></strong> <?php _e('of a possible','sendpress'); ?> <strong><?php echo $emails_per_day; ?></strong> <?php _e('emails sent today','sendpress'); ?>.</h2>
		<h2><strong><?php  echo SendPress_Manager::emails_this_hour(); ?></strong> <?php _e('of a possible','sendpress'); ?> <strong><?php echo $emails_per_hour; ?></strong> <?php _e('emails sent this hour','sendpress'); ?>.</h2>
		<small>You can adjust these settings here: <a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>">Settings > Sending Account</a>.</small>
 		<?php
$offset = get_option( 'gmt_offset' ) * 60 * 60; // Time offset in seconds
$local_timestamp = wp_next_scheduled('sendpress_cron_action') + $offset;

?><br><small>The cron will run again around: <?php
echo date_i18n( get_option('date_format') .' '. get_option('time_format'), $local_timestamp);
?></small>
 		<br><br>
		</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<br>
	<form  method='get'>
		<input type='hidden' value="<?php echo $_GET['page']; ?>" name="page" />
		
		<input type='hidden' value="empty-queue" name="action" />
		<a class="btn btn-large " data-toggle="modal" href="#sendpress-empty-queue" ><i class="icon-warning-sign "></i> <?php _e('Delete All Emails in the Queue','sendpress'); ?></a>
		<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
<div class="modal hide fade" id="sendpress-empty-queue">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">×</button>
		<h3><?php _e('Really? Delete All Emails in the Queue.','sendpress');?></h3>
	</div>
	<div class="modal-body">
		<p><?php _e('This will remove all emails from the queue without attempting to send them','sendpress');?>.</p>
	</div>
	<div class="modal-footer">
	<a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('No! I was Joking','sendpress');?></a><a href="<?php echo SendPress_Admin::link('Queue'); ?>&action=empty-queue" id="confirm-delete" class="btn btn-danger" ><?php _e('Yes! Delete All Emails','sendpress');?></a>
	</div>
</div>


	<div class="modal hide fade" id="sendpress-sending">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3><?php _e('Sending Emails','sendpress');?></h3>
  </div>
  <div class="modal-body">
    <div id="sendbar" class="progress progress-striped
     active">
  <div id="sendbar-inner" class="bar"
       style="width: 40%;"></div>
</div>
	Sent <span id="queue-sent">-</span> <?php _e('of','sendpress');?> <span id="queue-total">-</span> emails.<br>
	You are currently sending 1 email approximately every <?php 
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

	?> seconds.<br>
	You are also limited to <?php echo $hour; ?> emails per hour.<br>
	To change these settings go to <a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>">Settings > Sending Account</a>.
  </div>
  <div class="modal-footer">
   <?php _e('If you close this window sending will stop. ','sendpress');?><a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('Close','sendpress');?></a>
  </div>
</div>
<?php
	}

}
SendPress_Admin::add_cap('Queue','sendpress_queue');