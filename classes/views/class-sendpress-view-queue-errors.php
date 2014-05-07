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
class SendPress_View_Queue_Errors extends SendPress_View_Queue {


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
	
	

	function html($sp) {

		 SendPress_Tracking::event('Queue Tab');
	if(isset($_GET['cron'])){
		$sp->fetch_mail_from_queue();
	}	

		//Create an instance of our package class...
	$testListTable = new SendPress_Queue_Errors_Table();
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

	<h2><?php _e('Error history for the last 2 Weeks','sendpress'); ?>.</h2>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" action="<?php echo SendPress_Admin::link('Queue_Errors'); ?>" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	     <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" /> 
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<br>
	
<?php
	}

}
SendPress_Admin::add_cap('Queue','sendpress_queue');