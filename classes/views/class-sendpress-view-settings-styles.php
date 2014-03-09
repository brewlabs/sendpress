<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Settings_Styles extends SendPress_View_Settings {

	function save($data,$sp){
		$saveid = $_POST['post_ID'];
        $bodybg = $_POST['body_bg'];
        $bodytext = $_POST['body_text'];
        $bodylink = $_POST['body_link'];
        $contentbg = $_POST['content_bg'];
        $contenttext = $_POST['content_text'];
        $contentlink = $_POST['sp_content_link_color'];
        $contentborder = $_POST['content_border'];
        $upload_image = $_POST['upload_image'];

        
        $headerbg = $_POST['header_bg'];
        $headertextcolor = $_POST['header_text_color'];
        $headertext = $_POST['header_text'];
        $headerlink = $_POST['header_link'];
        $imageheaderurl = $_POST['image_header_url'];
        
        $subheadertext = $_POST['sub_header_text'];

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

       	$canspam= $_POST['can-spam'];
        $linkedin = '';
        if(isset($_POST['linkedin'])){
            $linkedin= $_POST['linkedin'];
        } 

        $twitter = '';
        if(isset($_POST['twitter'])){
            $twitter= $_POST['twitter'];
        }

        $facebook = '';
        if(isset($_POST['facebook'])){
            $facebook= $_POST['facebook'];
        }

        if(isset($_POST['fromname'])){
            $fromname= $_POST['fromname'];
        }

        // From email and name
        // If we don't have a name from the input headers
        if ( !isset( $fromname ) || $fromname == '' ){
            $fromname = get_bloginfo('name'); 
        }
        
        if(isset($_POST['fromemail'])){
            $fromemail= $_POST['fromemail'];
        }


        if ( !isset( $fromemail )  || $fromemail == '') {
            // Get the site domain and get rid of www.
            $sitename = strtolower( $_SERVER['SERVER_NAME'] );
            if ( substr( $sitename, 0, 4 ) == 'www.' ) {
                $sitename = substr( $sitename, 4 );
            }

            $fromemail = 'wordpress@' . $sitename;
        }

        SendPress_Option::set('canspam', $canspam);
        SendPress_Option::set('linkedin', $linkedin);
        SendPress_Option::set('facebook', $facebook);
        SendPress_Option::set('twitter', $twitter);
       
       // SendPress_Option::set('unsubscribetext', $unsubtext);

        SendPress_Admin::redirect('Settings_Styles');
	}
	
	function html($sp) {
		global $post_ID, $post;

		$view = isset($_GET['view']) ? $_GET['view'] : '' ;

		$list ='';

		if(isset($_GET['emailID'])){
			$emailID = $_GET['emailID'];
			$post = get_post( $_GET['emailID'] );
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

<?php wp_nonce_field($sp->_nonce_value); ?>
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
<h4 class="nomargin">Link to browser version</h4>
<p><input type=radio value="" name="browerslink" checked/> Use default&nbsp;&nbsp;&nbsp;<input type=radio value="" name="browerslink"/> Use custom&nbsp;&nbsp;&nbsp;<input type=radio value="" name="browerslink"/> None</p>
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