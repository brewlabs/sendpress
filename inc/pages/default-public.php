<?php 

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}



switch($action){
case 'link':
$link = get_query_var('spurl');

if( get_query_var('fxti') &&  get_query_var('spreport') ){


$this->register_click(get_query_var('fxti'), get_query_var('spreport'), $link);

}




header( 'Location: '.$link ) ;

break;
case 'open':
if(  get_query_var('fxti') &&  get_query_var('spreport') ){

$this->register_open( get_query_var('fxti'), get_query_var('spreport') );

}

header('Content-type: image/gif'); 
include(SENDPRESS_PATH. '/im/clear.gif'); 



break;
case 'manage':
$a = get_query_var('a');
$this->simple_page_start();
$name = get_bloginfo('name');
echo '<h1>'. $name .'</h1>';
echo '<h2>Manage Subscription</h2>';

if( get_query_var('fxti') ){
	if( $a =="u" && get_query_var('splist') && get_query_var('spreport')   ){
		$this->register_unsubscribe(get_query_var('fxti'), get_query_var('spreport'),get_query_var('splist'));
	}
	$subscriber = $this->getSubscriberbyKey( get_query_var('fxti') );
	echo '<h4>Subscriber Info</h4>';
	echo '<div class="well">';
	echo '<b>Email:</b> '. $subscriber[0]->email.'<br>';
	echo '<b>Signup date:</b> '.$subscriber[0]->join_date.'';
	echo '</div>';
	

	$c = ' hide ';
	$lists = $this->getDetail($this->lists_table(),'public',1);
	if ( !empty($_POST) && check_admin_referer($this->_nonce_value) ){
		$lists_susbscriber = $this->getSubscriberLists( $subscriber[0]->subscriberID  );
		foreach ($lists as $list_loop) {
			if(isset($_POST['subscribe_'.$list_loop->listID ])){
				$info = $this->getSubscriberListsStatus($list_loop->listID, $subscriber[0]->subscriberID);
				if(isset($info->status)){
					$this->updateStatus($list_loop->listID,$subscriber[0]->subscriberID, $_POST['subscribe_'.$list_loop->listID] );
					//echo $info->status;
					if(isset($_POST['spreport']) && $_POST['subscribe_'.$list_loop->listID ] == '3' && $info->status != '3' && $_POST['splist'] == $list_loop->listID ){
						//echo 'why';
						$this->register_unsubscribe(get_query_var('fxti'), $_POST['spreport'] , $_POST['splist'] );
					}

				} elseif( $_POST['subscribe_'.$list_loop->listID ] == '2' ){
					$this->linkListSubscriber($list_loop->listID,$subscriber[0]->subscriberID, $_POST['subscribe_'.$list_loop->listID] );
				}
			} 
			
		}
		$c = '';
	}

	
	if($a=="u"){
		$c = '';
	}

	?>

	<div class="alert alert-info <?php echo $c; ?> fade in">
  <a class="close" data-dismiss="alert" href="#">×</a>
  <h4 class="alert-heading">Saved!</h4>
 Your subscriptions have been updated. Thanks.
</div>

	<?php
	echo '<p>You are subscribed to the following lists:</p>';
	//echo $subscriber[0]->subscriberID;
	?>
	
<form action="?sendpress=manage&fxti=<?php echo get_query_var('fxti'); ?>" method="post">
<?php wp_nonce_field($this->_nonce_value); ?>
<input type="hidden" name="fxti" value="<?php echo get_query_var('fxti'); ?>" />
<input type="hidden" name="spreport" value="<?php echo get_query_var('spreport'); ?>" />
<input type="hidden" name="splist" value="<?php echo get_query_var('splist'); ?>" />

<table cellpadding="0" cellspacing="1" width="100%" class="table table-striped">
	<tr>
		<th width="100" >Subscribed</th>
		<th width="120" >Unsubscribed</th>
		<th width="120" >List Name</th>
		<th>Updated</th>
	</tr>
<?php
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


	?>

</table>
<br>
<input type="submit" class="btn btn-primary" value="Save My Settings"/>
</form>
	<a  href="<?php echo site_url(); ?>"><i class="icon-hand-left"></i> Return to <?php echo $name; ?></a>
	<?php
}
$this->simple_page_end();

break;
case 'confirm':
break;
default:
	$data = $this->decrypt_data($action);
	//print_r($data);	
 	$view = false;
 	if(is_object($data)){
 		$view = isset($data->view) ? $data->view : false;
 	}
 	$view_class = $this->get_public_view_class($view);
 	//print_r($class);
	$view_class = NEW $view_class;
	$view_class->data($data);
	$view_class->prerender( $this );
	$view_class->render( $this );
}