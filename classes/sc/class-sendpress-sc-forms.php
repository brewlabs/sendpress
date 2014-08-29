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
			$options = SendPress_Data::get_sp_settings_object($formid);

			switch($options['_setting_type']){
				case 'signup_widget':
					self::signup($options);
					break;
			}
		}

	}

	private static function signup($options){

		global $load_signup_js, $sendpress_show_thanks, $sendpress_signup_error;
		$load_signup_js = true;
		$no_list_error = '-- NO LIST HAS BEEN SET! --';

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

		$post_notifications_code = apply_filters( 'sendpress-post-notifications-submit-code', "", $list_ids, $postnotification, $pnlistid );
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
							<input type="hidden" name="sp_list" id="list" class="sp_list" value="<?php echo $listids; ?>" />
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

}
