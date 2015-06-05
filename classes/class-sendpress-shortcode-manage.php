<?php
// SendPress Required Class: SendPress_Signup_Shortcode

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Shortcode_Manage{

	static function init(){
		add_shortcode('sendpress-manage', array('SendPress_Shortcode_Manage','load_page'));
	}

	static function load_page( $attr, $content = null ) {

				$action = get_query_var( 'spmanage' );
				//Look for encrypted data
		  		$info = SendPress_Data::decrypt( $action );

		  		//print_r( $info );
	?>

<form action="" method="post">
<?php wp_nonce_field( SendPress_Data::nonce() ); ?>
<input type="hidden" name="subscriberid" id="subscriberid" value="<?php echo $info->id; ?>" />
<input type="hidden" name="action" id="action" value="sendpress-manage-shortcode" />
<table cellpadding="0" cellspacing="0" class="table table-condensed table-striped table-bordered">
	<tr>
		<th  ><?php _e('Subscribed','sendpress'); ?></th>
		<th  ><?php _e('Unsubscribed','sendpress'); ?></th>
		<th  ><?php _e('List','sendpress'); ?></th>
		<th class="hidden-phone"><?php _e('Updated','sendpress'); ?></th>
		<th class="hidden-phone"><?php _e('Other Info','sendpress'); ?></th>
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
</form><?php




	}

}

