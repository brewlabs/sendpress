<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Settings_Styles extends SendPress_View_Settings {

	function save(){
        //$this->security_check();
        $saveid = SPNL()->validate->int( $_POST['post_ID']);
		if( $saveid > 0){
        $bodybg = SPNL()->validate->hex( $_POST['body_bg'] );
        $bodytext = SPNL()->validate->hex( $_POST['body_text']);
        $bodylink = SPNL()->validate->hex( $_POST['body_link']);
        $contentbg = SPNL()->validate->hex( $_POST['content_bg']);
        $contenttext = SPNL()->validate->hex( $_POST['content_text']);
        $contentlink = SPNL()->validate->hex( $_POST['sp_content_link_color']);
        $contentborder = SPNL()->validate->hex( $_POST['content_border']);
        $upload_image = $_POST['upload_image'];

        
        $headerbg = SPNL()->validate->hex( $_POST['header_bg'] );
        $headertextcolor =  SPNL()->validate->hex($_POST['header_text_color']);
        $headertext = sanitize_text_field($_POST['header_text']);
        $headerlink = esc_url_raw( $_POST['header_link'] );
        $imageheaderurl = esc_url_raw( $_POST['image_header_url'] );
        
        $subheadertext = sanitize_text_field( $_POST['sub_header_text'] );

        $activeHeader = $_POST['active_header'];

        update_post_meta($saveid ,'upload_image', $upload_image );

        update_post_meta($saveid ,'body_bg', $bodybg);
        update_post_meta($saveid ,'body_text', $bodytext );
        update_post_meta($saveid ,'body_link', $bodylink );
        update_post_meta($saveid ,'content_bg', $contentbg );
        update_post_meta($saveid ,'content_text', $contenttext );
        update_post_meta($saveid ,'sp_content_link_color', $contentlink );
        update_post_meta($saveid ,'content_border', $contentborder );

        update_post_meta($saveid ,'header_bg', $headerbg );
        update_post_meta($saveid ,'header_text_color', $headertextcolor );
        update_post_meta($saveid ,'header_text', $headertext );
        update_post_meta($saveid ,'header_link', $headerlink );
        update_post_meta($saveid ,'image_header_url', $imageheaderurl );
        update_post_meta($saveid ,'sub_header_text', $subheadertext );

        update_post_meta($saveid ,'active_header', $activeHeader );

       	$canspam = SPNL()->validate->_html('can-spam');

        $linkedin = '';
        if(isset($_POST['linkedin'])){
            $linkedin= esc_url_raw($_POST['linkedin']);
        } 

        $twitter = '';
        if(isset($_POST['twitter'])){
            $twitter= esc_url_raw($_POST['twitter']);
        }

        $facebook = '';
        if(isset($_POST['facebook'])){
            $facebook= esc_url_raw($_POST['facebook']);
        }

        if(isset($_POST['fromname'])){
            $fromname= sanitize_text_field($_POST['fromname']);
        }

        
        SendPress_Option::set('canspam', $canspam);
        SendPress_Option::set('linkedin', $linkedin);
        SendPress_Option::set('facebook', $facebook);
        SendPress_Option::set('twitter', $twitter);
        }
       // SendPress_Option::set('unsubscribetext', $unsubtext);

        SendPress_Admin::redirect('Settings_Styles');
	}
	
	function html() {
		global $post_ID, $post;
    	$list ='';
        $emailID = SPNL()->validate->_int('emailID');
		if( $emailID > 0 ){
			$post = get_post( $emailID );
			$post_ID = $post->ID;
		}

		?>
		<form method="post" id="post">
	<br class="clear">
    <!--
<div style="float:right;" >
	<a href="<?php echo SendPress_Admin::link('Settings_Styles'); ?>" class="btn btn-default btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
-->

<br class="clear">

		<?php 
        define('SENDPRESS_STYLER_PAGE',true);
        require_once( SENDPRESS_PATH. 'inc/forms/email-style.2.0.php' ); ?>
		
<br class="clear">

<?php wp_nonce_field($this->_nonce_value); ?>
		</form>
	<?php
	}
	function text_settings(){
		?>

<br>
<div class="well">
<?php
$display_correct = __("Is this email not displaying correctly?","sendpress");
$view = __("View it in your browser","sendpress");

if( SendPress_Option::get('beta') ) {
?>
<h4 class="nomargin"><?php _e('Link to browser version','sendpress'); ?></h4>
<p><input type=radio value="" name="browerslink" checked/> <?php _e('Use default','sendpress'); ?>&nbsp;&nbsp;&nbsp;<input type=radio value="" name="browerslink"/> <?php _e('Use custom','sendpress'); ?>&nbsp;&nbsp;&nbsp;<input type=radio value="" name="browerslink"/> <?php _e('None','sendpress'); ?></p>
<p><input name="inbrowser" type="text" id="inbrowser" value="<?php echo SendPress_Option::get('inbrowser'); ?>" class="regular-text sp-text"></p>
<br>
<?php } ?>


<div style="float: right; width: 45%;">
	
</div>	
<div style="width: 45%; margin-right: 10%">
<h4 class="nomargin"><?php _e('CAN-SPAM','sendpress'); ?>: <small><?php _e('required in the US.','sendpress'); ?></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('This area displays in Email Footer','sendpress'); ?></h4>
<textarea cols="20" rows="10" class="large-text code" name="can-spam"><?php echo SendPress_Option::get('canspam'); ?></textarea>
<p><?php _e('Your message must include your valid physical postal address. This can be your current street address, a post office box youve registered with the U.S. Postal Service, or a private mailbox youve registered with a commercial mail receiving agency established under Postal Service regulations.','sendpress'); ?></p>
<?php _e('This is dictated under the <a href="http://business.ftc.gov/documents/bus61-can-spam-act-compliance-guide-business" target="_blank">Federal CAN-SPAM Act of 2003</a>.','sendpress'); ?>
					</p>
</div>
</div>




		<?php
	}



}