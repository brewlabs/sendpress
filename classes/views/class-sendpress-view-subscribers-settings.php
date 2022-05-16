<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Subscribers_Settings extends SendPress_View_Subscribers {

    function save(){
        $post = $_POST;
        //$this->security_check();
        if(isset( $post['auto_unsubscribe'] )){
            SendPress_Option::set('auto_unsubscribe', 'yes' );
        } else {
            SendPress_Option::set('auto_unsubscribe', 'no' );
        }

        SendPress_Admin::redirect('Subscribers_Settings');
    }


    function html() {
        ?><form method="post" id="post">
        <!--
		<div style="float:right;" >
			<a href="<?php echo SendPress_Admin::link('Settings_Advanced'); ?>" class="btn btn-large btn-default" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
		</div>
		-->
        <br class="clear">
        <br class="clear">
        <div class="sp-row">
            <div class="sp-50 sp-first">
                <?php $this->panel_start( __('Automatic Unsubscribe','sendpress') ); ?>
                <p><?php _e('If a subscriber clicks the unsubscribe link in an email the system will automatically unsubscribe them from the list.','sendpress'); ?>.</p>
                <br>
                <p><?php _e('This feature may need to be disabled if users are getting unsubscribed from the list without clicking the link. Gmail and other email clients will sometimes follow urls in emails','sendpress'); ?>.</p>
                <br>
                <?php $ctype = SendPress_Option::get('auto_unsubscribe', 'yes'); ?>
                <input type="checkbox" name="auto_unsubscribe" value="yes" <?php if($ctype=='yes'){echo "checked='checked'"; } ?> /> <?php _e('Automatic unsubscribe user when unsubscribe link is clicked','sendpress'); ?>.
                <p class="small"><?php _e('If automatic unsubscribe is disable users will be redirect to the manage subscription page to unsubscribe','sendpress'); ?>.

                <?php $this->panel_end(); ?>


            </div>
            <div class="sp-50">



            </div>

        </div>
        <?php wp_nonce_field($this->_nonce_value); ?>
        </form>
        <?php
    }

}