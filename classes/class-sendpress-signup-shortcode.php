<?php
// SendPress Required Class: SendPress_Signup_Shortcode

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Signup_Shortcode{

	static function init(){
		add_shortcode('sendpress-signup', array('SendPress_Signup_Shortcode','load_form'));
	}

	static function load_form( $attr, $content = null ) {

		global $load_signup_js, $sendpress_show_thanks, $sendpress_signup_error;
		$load_signup_js = true;

	    ob_start();

	   	$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);

	    extract(shortcode_atts(array(
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
			'no_list_error' => '<div><b>-- NO LIST HAS BEEN SET! --</b></div>',
			'postnotification' => '', 
			'pnlistid' => 0
		), $attr));

		$label = filter_var($label_display, FILTER_VALIDATE_BOOLEAN);

		$widget_options = SendPress_Option::get('widget_options');
	    ?>
	    
	    <div class="sendpress-signup-form">
			<form id="sendpress_signup" method="POST" <?php if( !$widget_options['load_ajax'] ){ ?>class="sendpress-signup"<?php } else { ?>action="?sendpress=post"<?php } ?> >
				<?php 
					if( $widget_options['load_ajax'] ){
						echo '<input type="hidden" name="action" value="signup-user" />';
						//echo '<input type="hidden" name="redirect" value="'.get_permalink().'" />';
					}
					if(empty($listids)){
						echo $no_list_error;
					}
					if($redirect_page != false && $redirect_page > 0){
						echo '<input type="hidden" name="redirect" value="'.$redirect_page.'" />';
					}

				?>
					
				<div id="error"><?php echo $sendpress_signup_error; ?></div>
				<div id="thanks" <?php if( $sendpress_show_thanks ){ echo 'style="display:block;"'; }else{ echo 'style="display:none;"'; } ?>><?php echo $thank_you; ?></div>
				<div id="form-wrap" <?php if( $sendpress_show_thanks ){ echo 'style="display:none;"'; } ?>>
					<p><?php echo $desc; ?></p>
					<?php
					$list_ids = explode(",",$listids);
					if( count($list_ids) > 1 ) { ?>
						<p>
						<label for="list"><?php echo $list_label; ?>:</label>
						<?php
						foreach ($list_ids as $id) { ?>
							<input type="checkbox" name="sp_list[]" class="sp_list" id="list<?php echo $id; ?>" value="<?php echo $id; ?>" <?php if($lists_checked){ echo 'checked'; }?> /> <?php echo get_the_title($id); ?><br>
						<?php
						} ?>	
						</p>
						<?php

					} else { ?>
						<input type="hidden" name="sp_list" id="list" class="sp_list" value="<?php echo $listids; ?>" />

					<?php } 

					if( strlen($postnotification) > 0 ){
						do_action('sendpress_add_post_notification_list', $postnotification, $pnlistid);
					}
					
					?>

					<?php if( filter_var($display_firstname, FILTER_VALIDATE_BOOLEAN)  ): ?>
						<p name="firstname">
							<?php if( !$label ): ?>
								<label for="firstname"><?php echo $firstname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_firstname" orig="<?php echo $firstname_label; ?>" value="<?php if($label){ echo $firstname_label; } ?>"  name="sp_firstname" />
						</p>
					<?php endif; ?>

					<?php if( filter_var($display_lastname, FILTER_VALIDATE_BOOLEAN) ): ?>
						<p name="lastname">
							<?php if( !$label ): ?>
								<label for="lastname"><?php echo $lastname_label; ?>:</label>
							<?php endif; ?>
							<input type="text" class="sp_lastname" orig="<?php echo $lastname_label; ?>" value="<?php if($label){ echo $lastname_label; } ?>" name="sp_lastname" />
						</p>
					<?php endif; ?>

					<p name="email">
						<?php if( !$label ): ?>
							<label for="email"><?php echo $email_label; ?>:</label>
						<?php endif; ?>
						<input type="text" class="sp_email" orig="<?php echo $email_label; ?>" value="<?php if($label){ echo $email_label; } ?>" name="sp_email" />
					</p>
					<p name="extra_fields" class="signup-fields-bottom">
						<?php do_action('sendpress_signup_form_bottom'); ?>
					</p>

					<p class="submit">
						<input value="<?php echo $button_text; ?>" class="sendpress-submit" type="submit"  id="submit" name="submit"><img class="ajaxloader" src="<?php echo SENDPRESS_URL; ?>/img/ajax-loader.gif" />
					</p>
				</div>
			</form>
		</div> 
	
	    <?php

	    $output = ob_get_contents();
	    ob_end_clean();
	    return $output;
	}

}

