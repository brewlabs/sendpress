<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Send extends SendPress_View_Emails {

	function save(){

        if(isset($_POST['send-date']) && $_POST['send-date'] == 'later'){
            $send_at = $_POST['date-pickit'] . " " . $_POST['send-later-time'];
        } else {
            $send_at = '0000-00-00 00:00:00';
        }

		$csvadd ="email,firstname,lastname\n" . trim($_POST['test-add']);
    	$data=  SendPress_Data::subscriber_csv_post_to_array($csvadd);
        $listids = isset($_POST['listIDS']) ? $_POST['listIDS'] : array();
        SendPress_Option::set('current_send_'. $_POST['post_ID'], array(
            'listIDS' =>  $listids,
            'testemails'=> $data,
            'send_at' => $send_at
            ));
        SendPress_Option::set('current_send_subject_'. $_POST['post_ID'],$_POST['post_subject']);
        if(isset($_POST['test_report'])){
            update_post_meta($_POST['post_ID'],'istest', true);
        } else {
             update_post_meta($_POST['post_ID'],'istest', false);
        }
        if(isset($_POST['submit']) && $_POST['submit'] == 'save-next'){
        	 SendPress_Admin::redirect('Emails_Send_Confirm', array('emailID'=>$_GET['emailID'] ));
        } else {
        	SendPress_Admin::redirect('Emails_Style', array('emailID'=>$_GET['emailID'] ));
        }
       
	}
	
	function admin_init(){
		wp_enqueue_script('jquery-ui-datepicker');
		wp_register_style( 'sendpress_css_jquery-ui', SENDPRESS_URL . 'css/smoothness/jquery-ui-1.10.3.custom.min.css', false, SENDPRESS_VERSION );
    		wp_enqueue_style( 'sendpress_css_jquery-ui' );
	}

	function html($sp) {

global $current_user;
global $post_ID, $post;

$view = isset($_GET['view']) ? $_GET['view'] : '' ;

$list ='';

if(isset($_GET['emailID'])){
	$emailID = $_GET['emailID'];
	$post = get_post( $_GET['emailID'] );
	$post_ID = $post->ID;
}


$post_type = $sp->_email_post_type;
$post_type_object = get_post_type_object($sp->_email_post_type);

?>
<div class="alert alert-danger fade hide">
  <?php _e('<strong>Notice!</strong> You must select a list below before an email can be sent.','sendpress'); ?>
</div>
<form method="POST" name="sendpress_post" id="sendpress_post">
<div style="float:right;"  class="btn-toolbar">
<div id="sp-cancel-btn" class="btn-group">
<a href="?page=<?php echo $_GET['page']; ?>"  class="btn btn-default "><?php echo __('Cancel','sendpress'); ?></a>
</div> 

<div class="btn-group">
    

    <button class="btn btn-default " type="submit" value="save" name="submit"><i class="icon-white icon-ok"></i> <?php echo __('Edit','sendpress'); ?></button>
    <button class="btn btn-primary " type="submit" value="save-next" id="sp-send-next" name="submit"><i class="icon-envelope icon-white"></i> <?php echo __('Send','sendpress'); ?></button>
</div>
</div>

<input type="hidden" id="user-id" name="user_ID" value="<?php echo $current_user->ID; ?>" />
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $post->ID; ?>" />
<input type="hidden" id="post_type" name="post_type" value="sp_newsletters" />

<h2><?php _e('Send Email','sendpress'); ?></h2>
<br>
<div class="boxer">
<div class="boxer-inner">

<?php $this->panel_start('<span class="glyphicon glyphicon-inbox"></span> '. __('Subject','sendpress')); ?>
<input type="text" class="form-control" name="post_subject" size="30" tabindex="1" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
<?php $this->panel_end(); ?>
<div class="leftcol">
<?php $this->panel_start( '<span class="glyphicon glyphicon-calendar"></span> '. __('Date & Time','sendpress')); ?>
<input type="radio" name="send-date" value="now" checked/> Start Sending Now<br>
<input type="radio" name="send-date" value="later"/> Send Later<br>
<div class="date-holder" style="display:none">
	<br>
<input type="text" name="date-pickit" id="date-pickit" class=" fifty float-left" value="<?php echo date_i18n('Y/m/d'); ?>"/>&nbsp;at
<script type="text/javascript">
jQuery(document).ready(function($) {
$(".date-holder").hide();

$('input[type=radio][name=send-date]').change(function() {
        if (this.value == 'now') {
            $(".date-holder").hide();
        }
        else if (this.value == 'later') {
           $(".date-holder").show();
        }
    });
$('#date-pickit').datepicker({
dateFormat : 'yy/mm/dd'
});
});
</script>
<select name="send-later-time" id="datepicker-time" class="fifty">
<option value="00:00:00">12:00 am</option>
<option value="01:00:00">1:00 am</option>
<option value="02:00:00">2:00 am</option>
<option value="03:00:00">3:00 am</option>
<option value="04:00:00">4:00 am</option>
<option value="05:00:00">5:00 am</option>
<option value="06:00:00">6:00 am</option>
<option value="07:00:00">7:00 am</option>
<option value="08:00:00">8:00 am</option>
<option value="09:00:00">9:00 am</option>
<option value="10:00:00">10:00 am</option>
<option value="11:00:00">11:00 am</option>
<option value="12:00:00">12:00 pm</option>
<option value="13:00:00">1:00 pm</option>
<option value="14:00:00">2:00 pm</option>
<option value="15:00:00">3:00 pm</option>
<option value="16:00:00">4:00 pm</option>
<option value="17:00:00">5:00 pm</option>
<option value="18:00:00">6:00 pm</option>
<option value="19:00:00">7:00 pm</option>
<option value="20:00:00">8:00 pm</option>
<option value="21:00:00">9:00 pm</option>
<option value="22:00:00">10:00 pm</option>
<option value="23:00:00">11:00 pm</option>
</select>
</div>
<?php 
$this->panel_end();

$this->panel_start('<span class="glyphicon glyphicon-list"></span> '. __('Lists','sendpress'));
$post_args = array( 'post_type' => 'sendpress_list','numberposts'     => -1,
    	'offset'          => 0,
    	'orderby'         => 'post_title',
    	'order'           => 'DESC', );
		
$current_lists = get_posts( $post_args );
foreach($current_lists as $list){

     $t = '';
     $tlist = '';
        if( get_post_meta($list->ID,'_test_list',true) == 1 ){ 
           $t = '  <span class="label label-info">Test List</span>';
           $tlist = ' test-list-add';
        } 
	echo "<input name='listIDS[]' type='checkbox' id='listIDS' class='sp-send-lists ". $tlist ."' value=" . $list->ID. "> ".$list->post_title . " <small>(".SendPress_Data::get_count_subscribers($list->ID). ")</small>$t<br>";
}

$this->panel_end();


$this->panel_start('<span class="glyphicon glyphicon-tag"></span> '. __('Mark as Test','sendpress'));

    echo "<input name='test_report' type='checkbox' id='test_report' value='1'> Test<br>";
    echo "<small class='text-muted'>This puts the report into the Test tab on the Reports screen.</small>";

$this->panel_end();

?>

<!--
<div class="style-unit">
<h4><?php _e('Settings','sendpress'); ?></h4>
<input type="checkbox" name="test-send" value="1" /> Mark as Test

<textarea name="test-add" cols='26' rows='6'></textarea>


</div>
-->
<?php wp_nonce_field($sp->_nonce_value); ?>
</div>
<div style="margin-left: 250px;">
<div class="widerightcol">
<?php
$url = get_site_url();
//$link =  get_permalink( $post->ID ); 
$open_info = array(
	"id"=>$post->ID,

	"view"=>"email"
);
$code = SendPress_Data::encrypt( $open_info );
$url = SendPress_Manager::public_url($code);


$sep = strstr($url, '?') ? '&' : '?';
$link = $url.$sep.'inline=true';
?>
<iframe src="<?php echo $link; ?>" width="100%" height="600px"></iframe>
<small>Displaying a 404? Please try saving your permalinks <a href="<?php echo admin_url('options-permalink.php'); ?>">here</a>.</small>

</div>
</div>
<br class="clear" />
</div>
</form>
<?php
	}

}
SendPress_Admin::add_cap('Emails_Send','sendpress_email_send');