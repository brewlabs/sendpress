<?php


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_View_Settings_Account extends SendPress_View_Settings {
	
	function html($sp) {?>
<form method="post" id="post">
You are currently sending emails with: <strong>Your Website</strong>
<div class="tabbable tabs-left">
  <ul class="nav nav-tabs">
    <li class="active"><a href="#tab1" data-toggle="tab">Your Website</a></li>
    <li><a href="#tab2" data-toggle="tab">Gmail</a></li>
    <li><a href="#tab3" data-toggle="tab">Custom SMTP</a></li>
    <li><a href="#tab4" data-toggle="tab">SendGrid</a></li>
    <li><a href="#tab5" data-toggle="tab">DYN</a></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane active" id="tab1">
      <p>I'm in Section 1.</p>
    </div>
    <div class="tab-pane" id="tab2">
      <p>Howdy, I'm in Section 2.</p>
    </div>
    <div class="tab-pane" id="tab3">
      <p>Howdy, I'm in Section 2.</p>
    </div>
    <div class="tab-pane" id="tab4">
      <p>Howdy, I'm in Section 2.</p>
    </div>
    <div class="tab-pane" id="tab5">
      <p>Howdy, I'm in Section 2.</p>
    </div>
  </div>
</div>

<br class="clear">
<div style="float:right;" >
	<a href="?page=sp-templates&view=account" class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
<input type="hidden" name="action" value="account-setup" />
<br class="clear">
<div class="boxer form-box">
<div class="sendpress-panel-column-container">
<div class="sendpress-panel-column">
	<h4>
		<input name="sendmethod" type="radio"  <?php if(SendPress_Option::get('sendmethod') == 'website' ) { ?>checked="checked"<?php } ?>   id="website" value="website" >
		<?php _e( 'Your Website','sendpress' ); ?>
	</h4>
	<p><?php _e('Although easy to setup your host may set limits on the number of emails per day','sendpress'); ?>.</p>
</div>
<div class="sendpress-panel-column">
	
	<h4>
		<input name="sendmethod" type="radio" id="gmail" <?php if(SendPress_Option::get('sendmethod') == 'gmail' ) { ?>checked="checked"<?php } ?> value="gmail" >
		<?php _e( 'Gmail Account' , 'sendpress'); ?>
	</h4>
	<p><?php _e('Gmail is limited to 500 emails a day. We recommend that you open a dedicated Gmail account for this purpose','sendpress'); ?>.</p>
	Username
	<p><input name="gmailuser" type="text" value="<?php echo SendPress_Option::get('gmailuser'); ?>" style="width:100%;" /></p>
	Password
	<p><input name="gmailpass" type="password" value="<?php echo SendPress_Option::get('gmailpass'); ?>" style="width:100%;" /></p>
</div>
<div class="sendpress-panel-column sendpress-panel-last">
	<h4>
		<input name="sendmethod" type="radio" id="sp" <?php if(SendPress_Option::get('sendmethod') == 'sendpress' ) { ?>checked="checked"<?php } ?>  value="sendpress" >
		<?php _e( 'SendPress Account', 'sendpress' ); ?>
	</h4>
	<p><?php _e('With a SendPress account your emails get delivered via our enterprise delivery system','sendpress'); ?></p>
	<p><?php _e('All Premium features are unlocked when using a <a href="http://sendpress.com">SendPress account</a> even if it is the free one','sendpress'); ?>.</p>
	<?php _e('Username','sendpress'); ?>
	<p><input name="sp_user" type="text" value="<?php echo SendPress_Option::get('sp_user'); ?>" style="width:100%;" /></p>
	<?php _e('Password','sendpress'); ?>
	<p><input name="sp_pass" type="password" value="<?php echo SendPress_Option::get('sp_pass'); ?>" style="width:100%;" /></p>

</div>
<div>

</div>	
</div>


</div>
<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<form method="post" id="post" class="form-horizontal">
<input type="hidden" name="action" value="test-account-setup" />
<br class="clear">
<div class="alert alert-success">
	<?php _e('<b>NOTE: </b>Remeber to check your spam folder if you do not seem to be recieving emails','sendpress'); ?>.
</div>

<h3><?php _e('Send Test Email','sendpress'); ?></h3>
<input name="testemail" type="text" id="appendedInputButton" value="<?php echo SendPress_Option::get('testemail'); ?>" style="width:100%;" />
<button class="btn btn-primary" type="submit"><?php _e('Send Test!','sendpress'); ?></button><button class="btn" data-toggle="modal" data-target="#debugModal" type="button"><?php _e('Debug Info','sendpress'); ?></button>
<br class="clear">



<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
<?php
$error= 	SendPress_Option::get('phpmailer_error');
$hide = 'hide';
if(!empty($error)){
	$hide = '';
	$phpmailer_error = '<pre>'.$error.'</pre>';
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('#debugModal').modal('show');
	});
	</script>

	<?php
}


?>

<div class="modal hide fade" id="debugModal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3><?php _e('SMTP Debug Info','sendpress'); ?></h3>
  </div>
  <div class="modal-body">
  	<?php 
  	if(!empty($phpmailer_error)){
  	$server  = "smtp.sendgrid.net";
  	$port   = "25";
  	$port2   = "465";
  	$port3   = "587";
  	$timeout = "1";

  if ($server and $port and $timeout) {
    $port25 =  @fsockopen("$server", $port, $errno, $errstr, $timeout);
    $port465 =  @fsockopen("$server", $port2, $errno, $errstr, $timeout);
    $port587 =  @fsockopen("$server", $port3, $errno, $errstr, $timeout);
  }	
  if(!$port25){
  	echo '<div class="alert alert-error">';
  	 _e('Port 25 seems to be blocked.','sendpress');
	echo '</div>';

  }
  if(!$port465){
  	echo '<div class="alert alert-error">';
  	_e('Port 465 seems to be blocked. Gmail may have trouble','sendpress');
	echo '</div>';

  }
  if(!$port587){
  	echo '<div class="alert alert-error">';
  	_e('Port 587 seems to be blocked.','sendpress');
	echo '</div>';

  }

  	echo $phpmailer_error;
  	} ?>
   	

    <pre>
<?php 




	$whoops = SendPress_Option::get('last_test_debug');
	if(empty( $whoops ) ){
		_e('No Debug info saved.','sendpress');
	} else {
		echo $whoops; 
	}
?>
</pre>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal"><?php _e('Close','sendpress'); ?></a>
  </div>
</div>

<?php
	}

}