<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}
/**
 * Subscribe Form Shortcode
 *
 *
 * @author 		SendPress
 * @category 	Shortcodes
 * @version     0.9.9.4
 */
class SendPress_SC_Forms extends SendPress_SC_Base {

	public static function title(){
		return __('Forms', 'sendpress');
	}

	public static function options(){
		return 	array(
			'formid' => 0
			);
	}

	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts , $content = null ) {
		
		extract( shortcode_atts( self::options() , $atts ) );

		if($formid > 0){
			//get options
			$options = SendPress_Data::get_post_meta_object($formid);

			switch($options['_form_type']){
				case 'signup_widget':
					self::signup($options);
					break;
				case 'manage_subscriptions':
					self::manage_sub_prerender();
					self::manage_subscription($options);
					break;
			}
		}

	}

	private static function manage_subscription($options){
		$info = self::data();

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

		$s = $_GET['sid'];
		$s = (int)base64_decode($s);

		extract($options);

		if(is_numeric($s)){
			$sub = SendPress_Data::get_subscriber($s);

			if($sub == false){
				$sub = NEW stdClass();
				$sub->email = 'example@sendpress.com';
				$sub->join_date = date("F j, Y, g:i a");
			}

			print_r($sub);
			?>
			<link rel="stylesheet" type="text/css" href="http://dev.wp/sendpress/wp-content/plugins/sendpress/css/manage-front-end.css">
			<h4>Subscriber Info</h4>
			<div class="subscriber-info">
				<b><?php _e('Email','sendpress');?></b>
				<?php echo $sub->email;?><br>
				<b><?php _e('Signup Date','sendpress');?></b>
				<?php echo $sub->join_date;?>
			</div>
			<div class="alert alert-block alert-info <?php echo self::handle_unsubscribes(); ?> fade in">
 				<h4 class="alert-heading"><?php _e('Saved','sendpress'); ?>!</h4>
 				<?php _e('Your subscriptions have been updated. Thanks.','sendpress'); ?>
			</div>
			<p><?php _e('You are subscribed to the following lists:','sendpress'); ?></p>
			<?php
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

	private static function signup($options){

		global $load_signup_js, $sendpress_show_thanks, $sendpress_signup_error;
		$load_signup_js = true;
		$no_list_error = '-- NO LIST HAS BEEN SET! --';
		$_listids = '';
		extract($options);

	   	$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);
	   	$postnotification = '';
	   	$pnlistid = array();
	   	//find post notification list
	   	foreach ($options as $key => $value) {
		    if (strpos($key, '_meta_for_list_') === 0) {
		        $pnlistid = array_pop(explode('_',$key));
		        $postnotification = $value;
		    }
		}

		$label = filter_var($_display_labels_inside_fields, FILTER_VALIDATE_BOOLEAN);
		$widget_options = SendPress_Option::get('widget_options');
		$list_ids = (strlen($_listids) > 0) ? explode(",",$_listids) : array();
		$post_notifications_code = '';
		if( is_wp_error($list_ids) ||  is_wp_error($postnotification) || is_wp_error($pnlistid)   ){
			$post_notifications_code = apply_filters( 'sendpress-post-notifications-submit-code', "", $list_ids, $postnotification, $pnlistid );
		}
	    ?>

	    <div class="sendpress-signup-form">
			<form id="sendpress_signup" method="POST" <?php if( !$widget_options['load_ajax'] ){ ?>class="sendpress-signup"<?php } else { ?>action="?sendpress=post"<?php } ?> >
				<?php
					if( $widget_options['load_ajax'] ){
						echo '<input type="hidden" name="action" value="signup-user" />';
					}
					if(empty($_listids) && strlen($post_notifications_code) == 0){
						echo $no_list_error;
					}
					if($_thankyou_page != false && $_thankyou_page > 0){
						echo '<input type="hidden" name="redirect" value="'.$_thankyou_page.'" />';
					}

				?>

				<div id="error"><?php echo $sendpress_signup_error; ?></div>
				<div id="thanks" <?php if( $sendpress_show_thanks ){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"'; } ?>><?php echo $_thankyou_message; ?></div>
				<div id="form-wrap" <?php if( $sendpress_show_thanks ){ echo 'style="display:none;"'; } ?>>
					<p><?php echo $_form_description; ?></p>
					<?php

					if(count($list_ids) > 0){
						if( count($list_ids) > 1 || strlen($post_notifications_code) > 0) {
							?>
							<p>
								<label for="list"><?php echo $_list_label; ?>:</label>
								<?php
									foreach ($list_ids as $id) {
										if($id !== $pnlistid){
											?>
											<input type="checkbox" name="sp_list[]" class="sp_list" id="list<?php echo $id; ?>" value="<?php echo $id; ?>" <?php if($_lists_checked){ echo 'checked'; }?> /> <?php echo get_the_title($id); ?><br>
											<?php
										}
									}
								?>
							</p>
							<?php
						} else {
							?>
							<input type="hidden" name="sp_list" id="list" class="sp_list" value="<?php echo $list_ids[0]; ?>" />
							<?php
						}
					}

					echo $post_notifications_code;

					?>

					<?php if( filter_var($_collect_firstname, FILTER_VALIDATE_BOOLEAN)  ): ?>
						<p name="sp_firstname">
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_firstname"><?php echo $_firstname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_firstname" orig="<?php echo $_firstname_label; ?>" value="<?php if($_display_labels_inside_fields){ echo $_firstname_label; } ?>"  name="sp_firstname" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($_collect_lastname, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p name="sp_lastname">
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_lastname"><?php echo $_lastname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_lastname" orig="<?php echo $_lastname_label; ?>" value="<?php if($_display_labels_inside_fields){ echo $_lastname_label; } ?>" name="sp_lastname" />
						</p>
					<?php endif; ?>

					<p name="sp_email">
						<?php if( !$_display_labels_inside_fields ): ?>
							<label for="sp_email"><?php echo $_email_label; ?>:</label>
						<?php endif; ?>
						<input type="text" class="sp_email" orig="<?php echo $_email_label; ?>" value="<?php if($label){ echo $_email_label; } ?>" name="sp_email" />
					</p>
					<p name="extra_fields" class="signup-fields-bottom">
						<?php do_action('sendpress_signup_form_bottom'); ?>
					</p>

					<p class="submit">
						<input value="<?php echo $_button_label; ?>" class="sendpress-submit" type="submit"  id="submit" name="submit"><img class="ajaxloader" src="<?php echo SENDPRESS_URL; ?>/img/ajax-loader.gif" />
					</p>
				</div>
			</form>
		</div>

	    <?php
	}

	public static function docs(){

		add_action('sendpress_shortcode_examples_forms',array('SendPress_SC_Forms','example_shortcodes'));

		return __('This shortcode loads a form based on a formid.', 'sendpress');
	}

	public static function example_shortcodes(){
		//echo "<strong class='text-muted'>Post Notification Signup:</strong><pre>[sp-signup listids='1' postnotification='pn-weekly' pnlistid='123']</pre>";
	}

	private static function handle_unsubscribes(){

		$_nonce_value = 'sendpress-is-awesome';
		$c = ' hide ';

		if ( !empty($_POST) && check_admin_referer($this->_nonce_value) ){
			
			$args = array(
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
					
				endwhile;
			}

			do_action('sendpress_public_view_manage_save', $_POST);
		}
		wp_reset_query();

		return $c;
	}

	private static function manage_sub_prerender(){
		$info = self::data();

	 	if ( isset($info->action) && $info->action == 'unsubscribe' ) {
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
			//$this->redirect(  $link ); 
			//exit;
		}
		
	}

	private static function data(){
		$data = '';

		if( (get_query_var( 'sendpress' )) || isset($_POST['sendpress']) ){
		  	$action = isset($_POST['sendpress']) ? $_POST['sendpress'] : get_query_var( 'sendpress' );
			//Look for encrypted data
	  		$data = SendPress_Data::decrypt( urldecode($action) );

	  	}
	  	return $data;
	}

}
