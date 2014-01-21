<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Manage extends SendPress_Public_View {
	
	function startup(){
		
	}

	function prerender(){
		$info = $this->data();
		if ( isset($info->action) && $info->action == 'unsubscribe' ) {
		//$sid, $rid, $lid
		//SendPress_Data::update_subscriber_status( $list_id , $info->id , $_POST[ 'subscribe_'.$list_id ] );
		SendPress_Data::unsubscribe_from_list( $info->id , $info->report, $info->listID  );

			$link_data = array(
				"id"=>$info->id,
				"report"=>$info->report,
				"urlID"=> '0',
				"view"=>"manage",
				"listID"=>$info->listID,
				"action"=>""
			);
			$code = SendPress_Data::encrypt( $link_data );
			$link =  SendPress_Manager::public_url($code);
			$this->redirect(  $link ); 
			exit;

		}	
		add_action('sendpress_public_view_scripts', array(&$this,'scripts'));		
	}

	function scripts(){
		?>
		<script>
		(function($) {
			$(".xbutton").change(function(){
				var d = {};
				rbutton = $(this);
				d['lid'] = String(rbutton.data('list'));
				d['sid'] = $('#subscriberid').val();
				d['status'] = rbutton.val();
				d['spnonce'] = spdata.nonce;
				d['action'] = 'sendpress-list-subscription';
				$.post(spdata.ajaxurl, d, function(response){
					$('.alert').slideDown('slow');
					console.log(response);
					response = $.parseJSON(response);
					$('#list_'+d['lid']).html(response.updated);
					setTimeout(function(){ $('.alert').slideUp('slow'); },2000);
				});
            	


			});

				setTimeout(function(){ $('.alert').slideUp('slow'); },1000);

			})(jQuery);	
		</script>	
		<?php
	}
		

	function html() {
		$info = $this->data();


		if(!isset($info->id)){
			$info = NEW stdClass();
			$info->id = 0;
		}
		if(isset($_GET['email'])){
			$data = SendPress_Data::get_subscriber_by_email($_GET['email']);
			if($data != false){
				$info->id = $data;
			}
		}


		/*
		$link = array(
				"id"=>$email->subscriberID,
				"report"=> $email->emailID,
				"urlID"=> $urlID,
				"view"=>"manage",
				"listID"=>$email->listID,
				"action"=>"unsubscribe"
			);
		*/
		?>
		<?php


		
		$try_theme = SendPress_Option::use_theme_style();
		$name = get_bloginfo('name'); 
		echo "<div class='entry-header'>";
		if(!$try_theme){
			
			echo '<h1>'. $name .'</h1>';
		}
		echo '<h2 class="entry-title">';
		_e('Manage Subscription','sendpress');
		echo '</h2>';
		echo "</div>";
		
	
	$subscriber = SendPress_Data::get_subscriber( $info->id );

	if($subscriber == false){
		$subscriber = NEW stdClass();
		$subscriber->email = 'example@sendpress.com';
		$subscriber->join_date = date("F j, Y, g:i a");

	}
	
	echo '<h4>';
	_e('Subscriber Info','sendpress');
	echo '</h4>';
	echo '<div class="subscriber-info">';
	$emailtext = __('Email','sendpress');
	echo '<b>' .$emailtext. ':</b> '. $subscriber->email.'<br>';
	$date = __('Signup Date','sendpress');
	echo '<b>'.$date.':</b> '.$subscriber->join_date.'';
	echo '</div>';
	$c = ' hide ';
if ( !empty($_POST) && check_admin_referer($this->_nonce_value) ){
	$args=array(
	  'meta_key'=>'public',
	  'meta_value'=> 1,
	  'post_type' => 'sendpress_list',
	  'post_status' => 'publish',
	  'posts_per_page' => -1,
	  'ignore_sticky_posts'=> 1
	);
	$my_query = new WP_Query($args);


	if( $my_query->have_posts() ) {

	  while ($my_query->have_posts()) : $my_query->the_post(); 	

		

		$list_id = $my_query->post->ID;

		if(isset($_POST['subscribe_'.$list_id ])){
					$list_status = SendPress_Data::get_subscriber_list_status( $list_id , $info->id );
					if(isset($list_status->status)){
						SendPress_Data::update_subscriber_status( $list_id , $info->id , $_POST[ 'subscribe_'.$list_id ] );
					} elseif( $_POST['subscribe_'. $list_id ] == '2' ){
						SendPress_Data::update_subscriber_status( $list_id , $info->id, $_POST[ 'subscribe_'.$list_id ] );
					}
				} 

				$c = '';
		/*
		$lists = $this->getDetail($this->lists_table(),'public',1);
		if ( !empty($_POST) && check_admin_referer($this->_nonce_value) ){
			$lists_susbscriber = $this->getSubscriberLists( $subscriber[0]->subscriberID  );
			foreach ($lists as $list_loop) {
				
				
			}
			
		}
		*/
		
	  endwhile;
	}

	do_action('sendpress_public_view_manage_save', $_POST);
}
wp_reset_query();
	?>

	<div class="alert alert-block alert-info <?php echo $c; ?> fade in">
 
  <h4 class="alert-heading"><?php _e('Saved','sendpress'); ?>!</h4>
 <?php _e('Your subscriptions have been updated. Thanks.','sendpress'); ?>
</div>
<p><?php _e('You are subscribed to the following lists:','sendpress'); ?></p>
	<?php
	
	//echo $subscriber[0]->subscriberID;

	$info->action = "update";
	$key = SendPress_Data::encrypt( $info );




	?>
	
<form action="?sendpress=<?php echo $key; ?>" method="post">
<?php wp_nonce_field( SendPress_Data::nonce() ); ?>
<input type="hidden" name="subscriberid" id="subscriberid" value="<?php echo $info->id; ?>" />

<table cellpadding="0" cellspacing="0" class="table table-condensed table-striped table-bordered">
	<tr>
		<th  ><?php _e('Subscribed','sendpress'); ?></th>
		<th  ><?php _e('Unsubscribed','sendpress'); ?></th>
		<th  ><?php _e('List','sendpress'); ?></th>
		<th class="hidden-phone">Updated</th>
		<th class="hidden-phone">Other Info</th>
	</tr>
<?php

$lists = SendPress_Data::get_lists(
	apply_filters( 'sendpress_modify_manage_lists', 
		array('meta_query' => array(
			array(
				'key' => 'public',
				'value' => true
				)
			)
		) 
	),
	false
);

foreach($lists as $list){
	$subscriber = SendPress_Data::get_subscriber_list_status($list->ID, $info->id);
	?>
  	<tr>
  	<?php

  		$checked = (isset($subscriber->statusid) && $subscriber->statusid == 2) ? 'checked' : '';
		echo '<td><input type="radio" class="xbutton" data-list="'.$list->ID.'" name="subscribe_'.$list->ID.'" '.$checked.' value="2"></td>';
		$checked = (isset($subscriber->statusid) && $subscriber->statusid == 3) ? 'checked' : '';
		echo '<td><input type="radio" class="xbutton" data-list="'.$list->ID.'" name="subscribe_'.$list->ID.'" '.$checked.' value="3"></td>';
  	?>
  	<td><?php echo $list->post_title; ?></td>
  	<td class="hidden-phone"><span id="list_<?php echo $list->ID;?>"><?php 
  	if(isset($subscriber->updated)) { echo $subscriber->updated; } else {
		 	_e('Never Subscribed','sendpress');
		 }
		 ?></span>
	</td>
	<td class="hidden-phone">
		<?php 
			if( is_object($subscriber) ){
				if($subscriber->statusid != 3 && $subscriber->statusid != 2){
					echo $subscriber->status;
				} 
			}
		?>
	</td>
  	<tr>	
    <?php
}
	?>

</table>
<br>
<?php do_action( 'sendpress_manage_notifications', $info );?>
<input type="submit" class="btn btn-primary" value="<?php _e('Save My Settings','sendpress'); ?>"/>
</form>


	<br>
	<a  href="<?php echo home_url(); ?>"><i class="icon-hand-left"></i> <?php _e('Return to','sendpress'); ?> <?php echo $name; ?></a>

	<?php


		
	}

}
