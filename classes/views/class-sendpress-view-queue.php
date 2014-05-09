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

		SendPress_Data::clean_queue_table();
		


	}


	function sub_menu($sp = false){


		?>
		<div class="navbar navbar-default" >
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>

    </button>
    <a class="navbar-brand" href="#">Queues</a>
</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
					<li <?php if(!isset($_GET['view']) ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Queue'); ?>"><span class="glyphicon glyphicon-open"></span>  <?php _e('Active','sendpress'); ?> (<?php echo SendPress_Data::emails_active_in_queue(); ?>)</a>
				  	</li>
				  	<li <?php if(isset($_GET['view']) && $_GET['view'] === 'stuck'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Queue_Stuck'); ?>"><span class="glyphicon glyphicon-exclamation-sign"></span>  <?php _e('Stuck','sendpress'); ?> (<?php echo  SendPress_Data::emails_maxed_in_queue(); ?>)</a>
				  	</li>
				  	<li <?php if(isset($_GET['view']) && $_GET['view'] === 'all'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Queue_All'); ?>"><span class="glyphicon glyphicon-time"></span>  <?php _e('Send History','sendpress'); ?></a>
				  	</li>
				  	<li <?php if(isset($_GET['view']) && $_GET['view'] === 'errors'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Queue_Errors'); ?>"><span class="glyphicon glyphicon-warning-sign"></span>  <?php _e('Send Errors','sendpress'); ?></a>
				  	</li>
				</ul>
			</div>
		</div>
		
		<?php

		do_action('sendpress-queue-sub-menu');
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

	function reset_queue(){
		SendPress_Data::requeue_emails();
		SendPress_Admin::redirect('Queue');
	}

	function pause_queue(){
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
		SendPress_Manager::reset_counters();
		SendPress_Admin::redirect('Queue');
	}

	function html($sp) {

		 SendPress_Tracking::event('Queue Tab');
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
	<a class="btn btn-large btn-default " href="<?php echo SendPress_Admin::link('Queue'); ?>&action=pause-queue" ><i class="icon-repeat icon-white "></i> <?php echo $txt; ?></a>

	<a id="send-now" class="btn btn-primary btn-large " data-toggle="modal" href="#sendpress-sending"   ><i class="icon-white icon-refresh"></i> <?php _e('Send Emails Now','sendpress');?></a>
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
		<small>You can adjust these settings here: <a href="<?php echo SendPress_Admin::link('Settings_Account'); ?>">Settings > Sending Account</a>.</small>
 		<?php
 		if(  $autocron == 'no'){
$offset = get_option( 'gmt_offset' ) * 60 * 60; // Time offset in seconds
$local_timestamp = wp_next_scheduled('sendpress_cron_action') + $offset;

?><br><small>The cron will run again around: <?php
echo date_i18n( get_option('date_format') .' '. get_option('time_format'), $local_timestamp);
?></small>
<?php } ?>
 		<br><br>
		</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" action="<?php echo SendPress_Admin::link('Queue'); ?>" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	     <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<br>
	<!--
		<a class="btn btn-large btn-success " href="<?php echo SendPress_Admin::link('Queue'); ?>&action=reset-queue" ><i class="icon-repeat icon-white "></i> <?php _e('Re-queue All Emails','sendpress'); ?></a><br><br>
	-->
	<form  method='get'>
		<input type='hidden' value="<?php echo $_GET['page']; ?>" name="page" />
		
		<input type='hidden' value="empty-queue" name="action" />
		<a class="btn btn-large  btn-danger" data-toggle="modal" href="#sendpress-empty-queue" ><i class="icon-warning-sign "></i> <?php _e('Delete All Emails in the Queue','sendpress'); ?></a>
		<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
<div class="modal fade" id="sendpress-empty-queue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  	<div class="modal-content">
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
</div></div>
<?php
	}

}
SendPress_Admin::add_cap('Queue','sendpress_queue');