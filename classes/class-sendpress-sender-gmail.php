<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if(!class_exists('SendPress_Sender_Gmail')){  

class SendPress_Sender_Gmail extends SendPress_Sender {
	function label(){
		return __('Gmail','sendpress');
	}

	function save(){
		$options =  array();
	 	$options['gmailuser'] = $_POST['gmailuser'];
        $options['gmailpass'] = $_POST['gmailpass'];
        SendPress_Option::set( $options );
	}

	function settings(){ ?>
	 <p><?php _e( 'Gmail is limited to 500 emails a day. We recommend that you open a dedicated Gmail account for this purpose', 'sendpress' ); ?>.</p>
  <?php _e( 'Username' , 'sendpress'); ?>
  <p><input name="gmailuser" type="text" value="<?php echo SendPress_Option::get( 'gmailuser' ); ?>" style="width:100%;" /></p>
  <?php _e( 'Password' , 'sendpress'); ?>
  <p><input name="gmailpass" type="password" value="<?php echo SendPress_Option::get( 'gmailpass' ); ?>" style="width:100%;" /></p>

	<?php

	}


}


}