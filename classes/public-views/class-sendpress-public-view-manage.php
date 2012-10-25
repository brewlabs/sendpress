<?php

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

class SendPress_Public_View_Manage extends SendPress_Public_View {
	
	function prerender($sp){
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
				console.log(d);
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
		

	function html($sp) {
		$info = $this->data();
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
		echo "<div class='span12'>";
		echo "<div class='area'>";
		$name = get_bloginfo('name'); 
		echo '<h1>'. $name .'</h1>';
		echo '<h2>Manage Subscription</h2>';

	
	if ( isset($info->action) && $info->action == 'unsubscribe' ) {
		//$sid, $rid, $lid
		$sp->unsubscribe_from_list( $info->id , $info->report, $info->listID  );
	}

	$subscriber = $sp->getSubscriber( $info->id );
	
	echo '<h4>Subscriber Info</h4>';
	echo '<div class="well">';
	echo '<b>Email:</b> '. $subscriber->email.'<br>';
	echo '<b>Signup date:</b> '.$subscriber->join_date.'';
	echo '</div>';
	$c = ' hide ';
if ( !empty($_POST) && check_admin_referer($this->_nonce_value) ){
$args=array(
  'meta_key'=>'public',
  'meta_value'=> 1,
  'post_type' => 'sendpress_list',
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);
$my_query = null;
$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
  //echo 'List of Posts';
  $lists_susbscriber = $sp->getSubscriberLists( $info->id  );

  while ($my_query->have_posts()) : $my_query->the_post(); 	

	

	$list_id = $my_query->post->ID;

	if(isset($_POST['subscribe_'.$list_id ])){
				$list_status = $sp->getSubscriberListsStatus( $list_id , $info->id );
				if(isset($list_status->status)){
					$sp->updateStatus( $list_id , $info->id , $_POST[ 'subscribe_'.$list_id ] );
					//echo $info->status;
					/*
					if( listID && $_POST['subscribe_'.$list_id ] == '3' && $list_status->status != '3'  ){
						//echo 'why';
						//$sp->register_unsubscribed( $info->id , $ );
					}
					*/

				} elseif( $_POST['subscribe_'. $list_id ] == '2' ){
					$sp->linkListSubscriber( $list_id , $info->id, $_POST[ 'subscribe_'.$list_id ] );
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
}
wp_reset_query();
	?>

	<div class="alert alert-block alert-info <?php echo $c; ?> fade in">
 
  <h4 class="alert-heading">Saved!</h4>
 Your subscriptions have been updated. Thanks.
</div>

	<?php
	echo '<p>You are subscribed to the following lists:</p>';
	//echo $subscriber[0]->subscriberID;

	$info->action = "update";
	$key = $sp->encrypt_data( $info );




	?>
	
<form action="?sendpress=<?php echo $key; ?>" method="post">
<?php wp_nonce_field($sp->_nonce_value); ?>
<input type="hidden" name="subscriberid" id="subscriberid" value="<?php echo $info->id; ?>" />

<table cellpadding="0" cellspacing="0" class="table table-condensed table-striped">
	<tr>
		<th  >Subscribed</th>
		<th  >Unsubscribed</th>
		<th  >List</th>
		<th class="hidden-phone">Updated</th>
	</tr>
<?php
	$args=array(
  'meta_key'=>'public',
  'meta_value'=> 1,
  'post_type' => 'sendpress_list',
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);
$my_query = null;
$my_query = new WP_Query($args);
if( $my_query->have_posts() ) {
 
  while ($my_query->have_posts()) : $my_query->the_post(); 
  $subscriber = $sp->getSubscriberListsStatus($my_query->post->ID, $info->id);
  ?>
  	<tr>
  	<?php
  	$checked = (isset($subscriber->status) && $subscriber->status == 2) ? 'checked' : '';
		echo '<td><input type="radio" class="xbutton" data-list="'.$my_query->post->ID.'" name="subscribe_'.$my_query->post->ID.'" '.$checked.' value="2"></td>';
		$checked = (empty($subscriber->status) || $subscriber->status == 3) ? 'checked' : '';
		echo '<td><input type="radio" class="xbutton" data-list="'.$my_query->post->ID.'" name="subscribe_'.$my_query->post->ID.'" '.$checked.' value="3"></td>';
  	?>
  	<td><?php the_title(); ?></td>
  	<td class="hidden-phone"><span id="list_<?php echo $my_query->post->ID;?>"><?php 
  	if(isset($subscriber->updated)) { echo $subscriber->updated; } else {
		 	echo 'Never Subscribed';
		 }
		 ?></span>
	</td>
  	<tr>	
    <?php
  endwhile;
}
wp_reset_query();


/*

	foreach ($lists as $list) {
		echo "<tr>";
		$info = $this->getSubscriberListsStatus($list->listID, $subscriber[0]->subscriberID);
		$checked = (isset($info->status) && $info->status == 2) ? 'checked' : '';
		echo '<td><input type="radio" name="subscribe_'.$list->listID.'" '.$checked.' value="2"></td>';
		$checked = (empty($info->status) || $info->status == 3) ? 'checked' : '';
		echo '<td><input type="radio" name="subscribe_'.$list->listID.'" '.$checked.' value="3"></td>';
		echo '<td>'. $list->name. '</td>';
		echo '<td>';
		 if(isset($info->updated)) { echo $info->updated; } else {
		 	echo 'Never Subscribed';
		 }

		 echo '</td>';
		echo "</tr>";
	}

*/
	?>

</table>
<br>
<input type="submit" class="btn btn-primary" value="Save My Settings"/>
</form>
	<a  href="<?php echo site_url(); ?>"><i class="icon-hand-left"></i> Return to <?php echo $name; ?></a>
</div>
</div>
	<?php


		
	}

}