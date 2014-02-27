<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Style extends SendPress_View_Emails {
	
	function save(){
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

        $_POST['post_type'] = 'sp_newsletters';
        // Update post 37

        $my_post = _wp_translate_postdata(true);
        /*            
        $my_post['ID'] = $_POST['post_ID'];
        $my_post['post_content'] = $_POST['content'];
        $my_post['post_title'] = $_POST['post_title'];
        */
       /*
        $str = $my_post['post_content'];
        $DOM = new DOMDocument;
        $DOM->loadHTML($str);
           //get all H1
        $aTags = $DOM->getElementsByTagName('a');

        foreach ($aTags as $aElement) {
            $style = $aElement->getAttribute('style');

                if($style == ""){
                    $aElement->setAttribute('style', 'color: '. $contentlink);
                }
        }

        $body_html = $DOM->saveHtml();
        $my_post['post_content']  = $body_html;
    */
        $my_post['post_status'] = 'publish';
        // Update the post into the database
        wp_update_post( $my_post );
        update_post_meta( $my_post['ID'], '_sendpress_subject', $_POST['post_subject'] );
        update_post_meta( $my_post['ID'], '_sendpress_template', $_POST['template'] );
        update_post_meta( $my_post['ID'], '_sendpress_status', 'private');

        SendPress_Email::set_default_style($my_post['ID']);
        //clear the cached file.
        delete_transient( 'sendpress_email_html_'. $my_post['ID'] );

        update_post_meta($saveid ,'body_bg', $bodybg);
        update_post_meta($saveid ,'body_text', $bodytext );
        update_post_meta($saveid ,'body_link', $bodylink );
        update_post_meta($saveid ,'content_bg', $contentbg );
        update_post_meta($saveid ,'content_text', $contenttext );
        update_post_meta($saveid ,'sp_content_link_color', $contentlink );
        update_post_meta($saveid ,'content_border', $contentborder );
        update_post_meta($saveid ,'upload_image', $upload_image );

        update_post_meta($saveid ,'header_bg', $headerbg );
        update_post_meta($saveid ,'header_text_color', $headertextcolor );
        update_post_meta($saveid ,'header_text', $headertext );

        update_post_meta($saveid ,'header_link', $headerlink );
        update_post_meta($saveid ,'image_header_url', $imageheaderurl );
        update_post_meta($saveid ,'sub_header_text', $subheadertext );

        update_post_meta($saveid ,'active_header', $activeHeader );
        
        if(isset($_POST['submit']) && $_POST['submit'] == 'save-next'){
            SendPress_Admin::redirect('Emails_Send', array('emailID'=>$_GET['emailID'] ));
        } else if (isset($_POST['submit']) && $_POST['submit'] == 'send-test'){
            $email = new stdClass;
            $email->emailID  = $my_post['ID'];
            $email->subscriberID = 0;
            $email->listID = 0;
            $email->to_email = $_POST['test-email'];
            $d =SendPress_Manager::send_test_email( $email );
            //print_r($d);
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>$_GET['emailID'] ));
        } else {
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>$_GET['emailID'] ));
        }


       
	}
	function admin_init(){
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
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
	

        if($post->post_type !== 'sp_newsletters'){
            SendPress_Admin::redirect('Emails');
        }


		?>
		<form method="POST" name="post" id="post">
		<!--
		<div style="float:left">
			<a href="?page=sp-emails" class="spbutton supersize" >Edit Content</a>
		</div>
		-->
		<div style="float:right;" class="btn-toolbar">
            <div id="sp-cancel-btn" class="btn-group">
                <a href="?page=<?php echo $_GET['page']; ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
            </div>
            <div class="btn-group">
            <button class="btn btn-default " type="submit" value="save" name="submit"><i class="icon-white icon-ok"></i> <?php echo __('Update','sendpress'); ?></button>
            <?php if( SendPress_Admin::access('Emails_Send') ) { ?>
            <button class="btn btn-primary " type="submit" value="save-next" name="submit"><i class="icon-envelope icon-white"></i> <?php echo __('Send','sendpress'); ?></button>
            <?php } ?>
            </div>
        </div>
		<?php require_once( SENDPRESS_PATH. 'inc/forms/email-style.2.0.php' ); ?>
		
        <div class="well clear">
            <h2>Test This Email</h2>
            <p><input type="text" name="test-email" value="" class="sp-text" placeholder="Email to send test to." /></p>
            <button class="btn btn-success" name="submit" type="submit" value="send-test"><i class=" icon-white icon-inbox"></i> Send Test</button>
        </div>
        </form>
	<?php
	}

}