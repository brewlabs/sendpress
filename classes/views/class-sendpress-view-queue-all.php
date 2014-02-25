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
class SendPress_View_Queue_All extends SendPress_View_Queue {


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
	
	function empty_queue( $get, $sp ){
		SendPress_Data::delete_queue_emails();
		SendPress_Admin::redirect('Queue');
	}

	function reset_queue(){
		SendPress_Data::requeue_emails();
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
	$testListTable = new SendPress_Queue_All_Table();
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
<h2><?php _e('Queue history for the last ','sendpress'); ?> <strong><?php echo SendPress_Option::get('queue-history',7); ?></strong> <?php _e('Days','sendpress'); ?>.</h2>
		<small>You can adjust these settings here: <a href="<?php echo SendPress_Admin::link('Settings_Advanced'); ?>">Settings > Advanced</a>.</small>
 		<br><br>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" action="<?php echo SendPress_Admin::link('Queue_All'); ?>" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	     <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 
	       <?php if(isset($_GET['listID']) && $_GET['listID'] > 0 ){ ?>
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <?php  } ?>
	    <input type="hidden" name="view" value="<?php echo $_GET['view']; ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<br>
	
	<form  method='get'>
		<input type='hidden' value="<?php echo $_GET['page']; ?>" name="page" />
		
		
		<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>

<?php
	}

}
SendPress_Admin::add_cap('Queue_All','sendpress_queue');