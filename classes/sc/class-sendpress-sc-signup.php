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
class SendPress_SC_Signup extends SendPress_SC_Base {

	public static function title(){
		return __('Signup', 'sendpress');
	}

	public static function options(){
		return 	array(
			'firstname_label' => 'First Name',
			'lastname_label' => 'Last Name',
			'email_label' => 'EMail',
			'list_label' => 'List Selection',
			'listids' => '',
			'redirect_page'=>false,
			'lists_checked'=>true,
			'display_firstname' => false,
			'display_lastname' => false,
			'label_display' => false,
			'desc' => '',
			'label_width' => 100,
			'thank_you'=>'Thank you for subscribing!',
			'button_text' => 'Submit',
			'no_list_error' => '-- NO LIST HAS BEEN SET! --',
			'postnotification' => '',
			'pnlistid' => 0
			);
	}

	/**
	 * Output the form
	 *
	 * @param array $atts
	 */
	public static function output( $atts , $content = null ) {
		global $load_signup_js, $sendpress_show_thanks, $sendpress_signup_error;
		$load_signup_js = true;
		$sendpress_signup_exists = __("You've already signed up, Thanks!",'sendpress');


	   	$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);

	    extract( shortcode_atts( self::options() , $atts ) );

		$label = filter_var($label_display, FILTER_VALIDATE_BOOLEAN);
		$widget_options = SendPress_Option::get('widget_options');
		$list_ids = (strlen($listids) > 0) ? explode(",",$listids) : array();

		$post_notifications_code = apply_filters( 'sendpress-post-notifications-submit-code', "", $list_ids, $postnotification, $pnlistid );
	    ?>

	    <div class="sendpress-signup-form">
			<form id="sendpress_signup" method="POST" <?php if( !$widget_options['load_ajax'] ){ ?>class="sendpress-signup"<?php } else { ?>action="?sendpress=post"<?php } ?> >
				<?php
					if( $widget_options['load_ajax'] ){
						echo '<input type="hidden" name="action" value="signup-user" />';
					}
					if(empty($listids) && strlen($post_notifications_code) == 0){
						echo $no_list_error;
					}
					if($redirect_page != false && $redirect_page > 0){
						echo '<input type="hidden" name="redirect" value="'.$redirect_page.'" />';
					}

				?>
				<div id="exists" style="display:none;"><?php echo $sendpress_signup_exists; ?></div>
				<div id="error"><?php echo $sendpress_signup_error; ?></div>
				<div id="thanks" <?php if( $sendpress_show_thanks ){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"'; } ?>><?php echo $thank_you; ?></div>
				<div id="form-wrap" <?php if( $sendpress_show_thanks ){ echo 'style="display:none;"'; } ?>>
					<p><?php echo $desc; ?></p>
					<?php

					if(count($list_ids) > 0){
						if( count($list_ids) > 1 || strlen($post_notifications_code) > 0) {
							?>
							<p>
								<label for="list"><?php echo $list_label; ?>:</label>
								<?php
									foreach ($list_ids as $id) {
										?>
										<input type="checkbox" name="sp_list[]" class="sp_list" id="list<?php echo $id; ?>" value="<?php echo $id; ?>" <?php if($lists_checked){ echo 'checked'; }?> /> <?php echo get_the_title($id); ?><br>
										<?php
									}
								?>
							</p>
							<?php
						} else {
							?>
							<input type="hidden" name="sp_list" id="list" class="sp_list" value="<?php echo $listids; ?>" />
							<?php
						}
					}

					if(is_string($post_notifications_code) && strlen($post_notifications_code) > 0){
						echo $post_notifications_code;
					}
					

					if( strlen($postnotification) > 0 ){
						do_action('sendpress_add_post_notification_list', $postnotification, $pnlistid);
					}

					?>

					<?php if( filter_var($display_firstname, FILTER_VALIDATE_BOOLEAN)  ): ?>
						<p name="firstname">
							<?php if( !$label ): ?>
								<label for="firstname"><?php echo $firstname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_firstname" <?php if($label){ echo 'placeholder="'.$firstname_label.'"';}?> value=""  name="sp_firstname" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($display_lastname, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p name="lastname">
							<?php if( !$label ): ?>
								<label for="lastname"><?php echo $lastname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_lastname" <?php if($label){ echo 'placeholder="'.$lastname_label.'"';}?>  value="" name="sp_lastname" />
						</p>
					<?php endif; ?>

					<p name="email">
						<?php if( !$label ): ?>
							<label for="email"><?php echo $email_label; ?>:</label>
						<?php endif; ?>
						<input type="text" class="sp_email" <?php if($label){ echo 'placeholder="'.$email_label.'"';}?> value="" name="sp_email" />
					</p>
					<p name="extra_fields" class="signup-fields-bottom">
						<?php do_action('sendpress_signup_form_bottom'); ?>
					</p>

					<p class="submit">
						<input value="<?php echo $button_text; ?>" class="sendpress-submit" type="submit"  id="submit" name="submit"><img class="ajaxloader" style="display:none;"  src="<?php echo SENDPRESS_URL; ?>/img/ajax-loader.gif" />
					</p>
				</div>
			</form>
		</div>

	    <?php


	}

	public static function docs(){

		add_action('sendpress_shortcode_examples_signup',array('SendPress_SC_Signup','example_shortcodes'));

		return __('This shortcode creates a sign up form for users on your site.  This shortcode is not required for users to signup, you can also use our signup widget.', 'sendpress');
	}

	public static function example_shortcodes(){
		echo "<strong class='text-muted'>Post Notification Signup:</strong><pre>[sp-signup listids='1' postnotification='pn-weekly' pnlistid='123']</pre>";
	}

}
