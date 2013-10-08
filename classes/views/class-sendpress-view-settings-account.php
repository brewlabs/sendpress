<?php


// Prevent loading this file directly
if ( !defined( 'SENDPRESS_VERSION' ) ) {
  header( 'HTTP/1.0 403 Forbidden' );
  die;
}


class SendPress_View_Settings_Account extends SendPress_View_Settings {

  function account_setup(){

    //if(  wp_verify_nonce( $_POST['_spnonce'] , basename(__FILE__) )){

        $options =  array();

       
        $options['sendmethod'] = $_POST['sendpress-sender'];

        $options['emails-per-day'] = $_POST['emails-per-day'];
        $options['emails-per-hour'] = $_POST['emails-per-hour'];
         $options['email-charset'] = $_POST['email-charset'];
        $options['email-encoding'] = $_POST['email-encoding'];

        $options['phpmailer_error'] = '';
        $options['last_test_debug'] = '';
        SendPress_Option::set( $options );

          global  $sendpress_sender_factory;

          $senders = $sendpress_sender_factory->get_all_senders(); 

          foreach ( $senders as $key => $sender ) {
            $sender->save();
          }
       // }

        SendPress_Admin::redirect('Settings_Account');


  }

  function send_test_email(){
        $options = array();
        $options['testemail'] = $_POST['testemail'];
        
        SendPress_Option::set($options);
        SendPress_Manager::send_test();
       // $this->send_test();
       // $this->redirect();
  }


  function html( $sp ) {
    global  $sendpress_sender_factory;
    $senders = $sendpress_sender_factory->get_all_senders();
    ksort($senders);
    $method = SendPress_Option::get( 'sendmethod' );
?>
<div style="float:right;" >
  <a href="" class="btn btn-large" ><i class="icon-remove"></i> <?php _e( 'Cancel', 'sendpress' ); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e( 'Save', 'sendpress' ); ?></a>
</div>
  <h2><?php _e('Sending Account Setup', 'sendpress'); ?></h2>
  <br class="clear">

<form method="post" id="post">
  <input type="hidden" name="action" value="account-setup" />

  <?php if( count($senders) < 3 ){
      $c= 0;
     foreach ( $senders as $key => $sender ) {
      $class ='';
      if ( $c >= 1 ) { $class = "margin-left: 4%"; }
      echo "<div style=' float:left; width: 48%; $class' id='$key'>";
      ?>      
        <p>&nbsp;<input name="sendpress-sender" type="radio"  <?php if ( $method == $key || strpos(strtolower($key) , $method) > 0 ) { ?>checked="checked"<?php } ?> id="website" value="<?php echo $key; ?>" /> <?php _e('Send Emails via', 'sendpress'); ?>
        <?php
        echo $sender->label();
        echo "</p><div class='well'>";
        echo $sender->settings();
      echo "</div></div>";
      $c++;
    }  



  } else { ?>
  <div class="tabbable tabs-left">
    <ul class="nav nav-tabs">
    <?php
    foreach ( $senders as $key => $sender ) {
      $class ='';
      if ( $method == $key || strpos(strtolower($key) , $method) > 0 ) { $class = "class='active green'"; }
      echo "<li $class><a href='#$key' data-toggle='tab'>";
      if ( $method == $key || strpos(strtolower($key) , $method) > 0 ) { echo '<i class="icon-ok"></i> '; }
      echo $sender->label();
      echo "</a></li>";
    }
?>
    </ul>
    <div class="tab-content">
      <?php
    foreach ( $senders as $key => $sender ) {
      $class ='';
      if ( $method == $key || strpos(strtolower($key) , $method) > 0 ) { $class = "active"; }
      echo "<div class='tab-pane $class' id='$key'>";
?>      
        <p>&nbsp;<input name="sendpress-sender" type="radio"  <?php if ( $method == $key || strpos(strtolower($key) , $method) > 0 ) { ?>checked="checked"<?php } ?> id="website" value="<?php echo $key; ?>" /> <?php _e('Activate', 'sendpress'); ?>
        <?php
        echo $sender->label();
        echo "</p><div class='well'>";
        echo $sender->settings();
      echo "</div></div>";
    }
?>

    </div>
</div>


<p ><i class="icon-ok"></i> = <?php _e('Currently Active', 'sendpress'); ?></p>
<?php } ?>
<br class="clear">
<h3><?php _e('Advanced Sending Options', 'sendpress'); ?></h3>
<div class="boxer form-box">
  <div style="float: right; width: 45%;">
    <h2><?php _e('Email Sending Limits', 'sendpres'); ?></h2>
    
<?php
  $emails_per_day = SendPress_Option::get('emails-per-day');
  $emails_per_hour =  SendPress_Option::get('emails-per-hour');
  $emails_today = SendPress_Option::get('emails-today');
  $count_today = isset( $emails_today[date("z")] ) ? $emails_today[date("z")] : 0 ;
?><?php
$offset = get_option( 'gmt_offset' ) * 60 * 60; // Time offset in seconds
$local_timestamp = wp_next_scheduled('sendpress_cron_action') + $offset;
//print_r(wp_get_schedules());

printf( __('You have sent <strong>%s</strong> emails so far today.'), $count_today);
?>
<br><br>
<input type="text" size="6" name="emails-per-day" value="<?php echo $emails_per_day; ?>" /> <?php _e('Emails Per Day', 'sendpress'); ?><br><br>
<input type="text" size="6" name="emails-per-hour" value="<?php echo $emails_per_hour; ?>" /> <?php _e('Emails Per Hour', 'sendpress'); ?>
<br><br>
<h2><?php _e('Email Encoding', 'sendpress'); ?></h2>
<?php
  $charset = SendPress_Option::get('email-charset','UTF-8');
 ?>Charset: 
<select name="email-charset" id="">

<?php
$charsete = SendPress_Data::get_charset_types();
  foreach ( $charsete as $type) {
     $select="";
    if($type == $charset){
      $select = " selected ";
    }
    echo "<option $select value=$type>$type</option>";

  }
?>
</select><br>
<?php _e('Squares or weird characters displaying in your emails select the charset for your language.', 'sendpress'); ?>
<br><br>
<?php _e('Encoding:', 'sendpress'); ?> <select name="email-encoding" id="">
<?php
 $charset = SendPress_Option::get('email-encoding','8bit');
$charsete = SendPress_Data::get_encoding_types();
  foreach ( $charsete as $type) {
     $select="";
    if($type == $charset){
      $select = " selected ";
    }
    echo "<option $select value=$type>$type</option>";

  }
?>
</select><br>
<?php _e('Older versions of SendPress used "quoted-printable"', 'sendpress'); ?>

  <br class="clear">
  </div>  
  <div style="width: 45%; margin-right: 10%">
    <?php $tl =  SendPress_Option::get('autocron','no'); ?>
    <h2>SendPress Pro Auto Cron</h2>
    <p><?php _e('At least once every hour we visit your site, just like a "cron" job.<br>There&rsquo;s no setup involved. Easy and hassle free.', 'sendpress'); ?></p>
    <button id="sp-enable-cron" <?php if($tl == 'yes'){ echo "style='display:none;'";} ?> class="btn  btn-success"><?php _e('Enable', 'sendpress'); ?>  Pro Auto Cron</button><button id="sp-disable-cron" <?php if($tl == 'no'){ echo "style='display:none;'";} ?> class="btn  btn-danger"><?php _e('Disable', 'sendpress'); ?>  Pro Auto Cron</button>
    <p><?php _e('Get <a href="http://sendpress.com">SendPress Pro</a> to send even faster with connections every 15 minutes!', 'sendpress'); ?></p>
    <p><?php _e('Pro Auto Cron "Free" does collect some data about your website and usage of SendPress. It will not track any user details, so your security and privacy are safe with us.', 'sendpress'); ?></p>



<!--
  WordPress Cron: Next run @ <?php
echo date_i18n( get_option('date_format') .' '. get_option('time_format'), $local_timestamp);
?><br><br>-->

  <br class="clear">
  </div>

</div>


<?php 
//Page Nonce
//wp_nonce_field(  basename(__FILE__) ,'_spnonce' );
wp_nonce_field( $sp->_nonce_value );
?>
<input type="submit" class="btn btn-primary btn-large" value="<php _e('Save'); ?>"/> <a href="" class="btn btn-large"><i class="icon-remove"></i> <?php _e('Cancel'); ?></a>
</form>
<form method="post" id="post" class="form-horizontal">
<input type="hidden" name="action" value="send-test-email" />
<br class="clear">
<div class="alert alert-success">
  <?php _e( '<b>NOTE: </b>Remember to check your Spam folder if you do not seem to be receiving emails', 'sendpress' ); ?>.
</div>

<h3><?php _e( 'Send Test Email', 'sendpress' ); ?></h3>
<input name="testemail" type="text" id="appendedInputButton" value="<?php echo SendPress_Option::get( 'testemail' ); ?>" style="width:100%;" />
<button class="btn btn-primary" type="submit"><?php _e( 'Send Test!', 'sendpress' ); ?></button><button class="btn" data-toggle="modal" data-target="#debugModal" type="button"><?php _e( 'Debug Info', 'sendpress' ); ?></button>
<br class="clear">



<?php 
//Page Nonce
//wp_nonce_field(  basename(__FILE__) ,'_spnonce' );
//SendPress General Nonce
wp_nonce_field( $sp->_nonce_value );
?>
</form>
<?php
    $error=  SendPress_Option::get( 'phpmailer_error' );
    $hide = 'hide';
    if ( !empty( $error ) ) {
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
    <h3><?php _e( 'SMTP Debug Info', 'sendpress' ); ?></h3>
  </div>
  <div class="modal-body">
    <?php
    if ( !empty( $phpmailer_error ) ) {
      $server  = "smtp.sendgrid.net";
      $port   = "25";
      $port2   = "465";
      $port3   = "587";
      $timeout = "1";

      if ( $server and $port and $timeout ) {
        $port25 =  @fsockopen( "$server", $port, $errno, $errstr, $timeout );
        $port465 =  @fsockopen( "$server", $port2, $errno, $errstr, $timeout );
        $port587 =  @fsockopen( "$server", $port3, $errno, $errstr, $timeout );
      }
      if ( !$port25 ) {
        echo '<div class="alert alert-error">';
        _e( 'Port 25 seems to be blocked.', 'sendpress' );
        echo '</div>';

      }
      if ( !$port465 ) {
        echo '<div class="alert alert-error">';
        _e( 'Port 465 seems to be blocked. Gmail may have trouble', 'sendpress' );
        echo '</div>';

      }
      if ( !$port587 ) {
        echo '<div class="alert alert-error">';
        _e( 'Port 587 seems to be blocked.', 'sendpress' );
        echo '</div>';

      }

      echo $phpmailer_error;
    } ?>


    <pre>
<?php




    $whoops = SendPress_Option::get( 'last_test_debug' );
    if ( empty( $whoops ) ) {
      _e( 'No Debug info saved.', 'sendpress' );
    } else {
      echo $whoops;
    }
?>
</pre>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" data-dismiss="modal"><?php _e( 'Close', 'sendpress' ); ?></a>
  </div>
</div>

<?php
  }

}
