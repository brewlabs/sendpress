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

		if(is_numeric($formid)){
			$options = SendPress_Data::get_post_meta_object($formid);
		} else {
			$options = false;
		}

		if( !$options ){
			switch($formid){
				case 'manage':
					$options = SendPress_Data::get_default_settings_for_type('manage_subscriptions',true);
					break;
				case 'signup':
					$options = SendPress_Data::get_default_settings_for_type('signup_widget',true);
					break;
			}
			
		}

		if($options){
			switch($options['_form_type']){
				case 'signup_widget':
					self::signup($options);
					break;
				case 'manage_subscriptions':
					//self::manage_sub_prerender();
					self::manage_subscription($options);
					break;
			}
		}

	}

	private static function manage_subscription($options){
		//debug
		
		// $link_data = array(
		// 	"id"=>23,
		// 	"report"=>0,
		// 	"urlID"=> '0',
		// 	"view"=>"manage",
		// 	"listID"=>"0",
		// 	"action"=>""
		// );
		// $code = SendPress_Data::encrypt( $link_data );
		// $link =  SendPress_Manager::public_url($code);

		// print_r($link);

		$_nonce_value = 'sendpress-is-awesome';
		$info = self::data();

		//SendPress_Error::log($info->id);
		//print_r($info);

		if(!isset($info->id)){
			$info = NEW stdClass();
			$info->id = '';
		}

		$s = $info->id;

		//SendPress_Error::log($s);

		extract($options);

		if(is_numeric($s)){
			$sub = SendPress_Data::get_subscriber($s);

			if($sub == false){
				$sub = NEW stdClass();
				$sub->email = 'example@sendpress.com';
				$sub->join_date = date("F j, Y, g:i a");
			}

			// print_r($sub);
			?>
			<link rel="stylesheet" type="text/css" href="<?php echo SENDPRESS_URL; ?>/css/manage-front-end.css">
			<div class="sendpress-content">
				<h4><?php _e('Manage Subscriptions','sendpress'); ?></h4>
				<div class="subscriber-info">
					<b><?php _e('Email','sendpress');?></b>
					<?php echo $sub->email;?><br>
					<b><?php _e('Signup Date','sendpress');?></b>
					<?php echo $sub->join_date;?>
				</div>
				<?php if(self::handle_unsubscribes()){
					?>
					<div class="alert alert-block alert-info">
		 				<h4 class="alert-heading"><?php _e('Saved','sendpress'); ?>!</h4>
		 				<?php _e('Your subscriptions have been updated. Thanks.','sendpress'); ?>
					</div>
					<?php
				} ?>
				
				<p><?php _e('You are subscribed to the following lists:','sendpress'); ?></p>
				<?php
					$info->action = "update";
					$key = SendPress_Data::encrypt( $info );
					$query_var = '';
					if(get_query_var( 'spms' )){
						$query_var = "?spms=".$key;
					}elseif(get_query_var( 'sendpress' )){
						$query_var = "?sendpress=".$key;
					}
				?>
				<form action="<?php echo $query_var; ?>" method="post">
				<?php wp_nonce_field( SendPress_Data::nonce() ); ?>
				<input type="hidden" name="subscriberid" id="subscriberid" value="<?php echo $s; ?>" />

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
					$subscriber = SendPress_Data::get_subscriber_list_status($list->ID, $s);
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
			</div>
			<?php
		}else{
			_e("No e-mail found, please try again.<br><br>","sendpress");
		}

	}

	private static function signup($options){
		//print_r($options);
		$_collect_custom_field = false;
		global $load_signup_js, $sendpress_show_thanks, $sendpress_signup_error;
		$sendpress_signup_exists = __("You've already signed up, Thanks!",'sendpress');
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

	   	$default_list_ids = array();
		foreach($lists as $list){
			$default_list_ids[] = $list->ID;
		}

	   	$postnotification = '';
	   	$pnlistid = array();
	   	//find post notification list
	   	foreach ($options as $key => $value) {
		    if (strpos($key, '_meta_for_list_') === 0) {
		        $exploded_id = explode('_',$key);
		        $pnlistid = array_pop($exploded_id);
		        $postnotification = $value;
		    }
		}

		$label = filter_var($_display_labels_inside_fields, FILTER_VALIDATE_BOOLEAN);
		$widget_options = SendPress_Option::get('widget_options');
		$list_ids = (strlen($_listids) > 0) ? explode(",",$_listids) : array();

		if(!isset($_settings_id) && empty($list_ids)){
			$list_ids = $default_list_ids;
		}


		$post_notifications_code = '';
		if( !is_wp_error($list_ids) || !is_wp_error($postnotification) || !is_wp_error($pnlistid)   ){
			$post_notifications_code = apply_filters( 'sendpress-post-notifications-submit-code', "", $list_ids, $postnotification, $pnlistid );
			
		}
			

	    ?>

	    <div class="sendpress-signup-form">
			<form id="sendpress_signup" method="POST" data-form-id="<?php echo $_settings_id; ?>" <?php if( !$widget_options['load_ajax'] ){ ?>class="sendpress-signup"<?php } else { ?>action="?sendpress=post"<?php } ?> >
				<?php
					if( $widget_options['load_ajax'] ){
						echo '<input type="hidden" name="action" value="signup-user" />';
					}
					if(empty($_listids) && strlen($post_notifications_code) == 0 && isset($_settings_id)){
						echo $no_list_error;
					}
					if($_thankyou_page != false && $_thankyou_page > 0){
						echo '<input type="hidden" name="redirect" value="'.$_thankyou_page.'" />';
					}

				?>
				<div id="exists" style="display:none;"><?php echo $sendpress_signup_exists; ?></div>
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

					$salutation_required = false;
					$fn_required = false;
					$ln_required = false;
					$phone_required = false;

					if(filter_var($_salutation_required, FILTER_VALIDATE_BOOLEAN) ){ 
						$_salutation_label = '*'.$_salutation_label;
						$salutation_required = true;
					}

					if(filter_var($_firstname_required, FILTER_VALIDATE_BOOLEAN) ){ 
						$_firstname_label = '*'.$_firstname_label;
						$fn_required = true;
					}

					if(filter_var($_lastname_required, FILTER_VALIDATE_BOOLEAN) ){ 
						$_lastname_label = '*'.$_lastname_label;
						$ln_required = true;
					}

					if(filter_var($_phonenumber_required, FILTER_VALIDATE_BOOLEAN) ){ 
						$_phonenumber_label = '*'.$_phonenumber_label;
						$phone_required = true;
					}
					
					?>

					<?php if( filter_var($_collect_salutation, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p>
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_salutation"><?php echo $_salutation_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_salutation <?php if($salutation_required){ echo 'required'; } ?>" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $_salutation_label; ?>"<?php endif; ?> value="" name="sp_salutation" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($_collect_firstname, FILTER_VALIDATE_BOOLEAN)  ): ?>
						<p>
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_firstname"><?php echo $_firstname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_firstname <?php if($fn_required){ echo 'required'; } ?>" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $_firstname_label; ?>"<?php endif; ?> value=""  name="sp_firstname" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($_collect_lastname, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p>
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_lastname"><?php echo $_lastname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_lastname <?php if($ln_required){ echo 'required'; } ?>" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $_lastname_label; ?>"<?php endif; ?> value="" name="sp_lastname" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($_collect_phonenumber, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p>
							<?php if( !$_display_labels_inside_fields ): ?>
								<label for="sp_phonenumber <?php if($phone_required){ echo 'required'; } ?>"><?php echo $_phonenumber_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_phonenumber" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $_phonenumber_label; ?>"<?php endif; ?> value="" name="sp_phonenumber" />
						</p>
					<?php endif; ?>

					<p>
						<?php if( !$_display_labels_inside_fields ): ?>
							<label for="sp_email">*<?php echo $_email_label; ?>:</label>
						<?php endif; ?>
						<input type="text" class="sp_email required" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $_email_label; ?>"<?php endif; ?> value="" name="sp_email" />
					</p>


					<?php
					//new custom field section
					$custom_field_list = SendPress_Data::get_custom_fields_new();

					foreach ($custom_field_list as $key => $value) {
						

						if(filter_var($options['_collect_custom_field_'.$value['id']], FILTER_VALIDATE_BOOLEAN) ){
							
							$label = $value['custom_field_label'];
							$required = false;

							if(filter_var(($options['_custom_field_'.$value['id'].'_required'] === 'on'), FILTER_VALIDATE_BOOLEAN) ){ 
								$label = '*'.$label;
								$required = true;
							}

							?>
							<p>
								<?php if( !$_display_labels_inside_fields ): ?>
									<label for="<?php echo $value['custom_field_key']; ?>"><?php echo $label; ?>:</label>
								<?php endif; ?>
								<input id="<?php echo $value['custom_field_key']; ?>"  type="text" class="sp_custom_field <?php if($required){ echo 'required'; } ?>" <?php if( $_display_labels_inside_fields ): ?>placeholder="<?php echo $label; ?>"<?php endif; ?> value="" name="<?php echo $value['custom_field_key']; ?>" />
							</p>
							<?php

						}
					}

					?>


					<p class="signup-fields-bottom">
						<?php do_action('sendpress_signup_form_bottom'); ?>
					</p>

					<p class="submit">
						<input value="<?php echo $_button_label; ?>" class="sendpress-submit" type="submit"  id="submit" name="submit"><img class="ajaxloader" style="display:none;" src="<?php echo SENDPRESS_URL; ?>/img/ajax-loader.gif" />
					</p>
				</div>
			</form>
		</div>

	    <?php
	}

	public static function docs(){

		add_action('sendpress_shortcode_examples_forms',array('SendPress_SC_Forms','example_shortcodes'));

		return __('This shortcode loads a form based on a form id.', 'sendpress');
	}

	public static function example_shortcodes(){
		//echo "<strong class='text-muted'>Post Notification Signup:</strong><pre>[sp-signup listids='1' postnotification='pn-weekly' pnlistid='123']</pre>";
	}

	private static function handle_unsubscribes(){

		$_nonce_value = 'sendpress-is-awesome';
		$c = false;

		if ( !empty($_POST) && check_admin_referer($_nonce_value) ){
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
						$list_status = SendPress_Data::get_subscriber_list_status( $list_id , $_POST['subscriberid'] );
						if(isset($list_status->status)){
							SendPress_Data::update_subscriber_status( $list_id , $_POST['subscriberid'] , $_POST[ 'subscribe_'.$list_id ] );
						} elseif( $_POST['subscribe_'. $list_id ] == '2' ){
							SendPress_Data::update_subscriber_status( $list_id , $_POST['subscriberid'], $_POST[ 'subscribe_'.$list_id ] );
						}
					} 
					$c = true;
					
				endwhile;
			}

			//do_action('sendpress_public_view_manage_save', $_POST);
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

	static function data(){
		$data = '';
		if( (get_query_var( 'spms' ) || get_query_var( 'sendpress' )) ){
		  	$action = (get_query_var( 'spms' )) ? get_query_var( 'spms' ) : get_query_var( 'sendpress' );
	  	}else{
	  		$parsed = explode('/',$_SERVER['REQUEST_URI']);
	  		$action = $parsed[count($parsed)-2];
	  	}

	  	//SendPress_Error::log($action);

	  	$data = SendPress_Data::decrypt( $action );

	  	//print_r($data);

	  	return $data;
	}

}
