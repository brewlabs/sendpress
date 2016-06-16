<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Style extends SendPress_View_Emails {
	
	function save(){
        //$this->security_check();
		  $saveid = SPNL()->validate->int( $_POST['post_ID']);
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
        update_post_meta( $my_post['ID'], '_sendpress_subject',  sanitize_text_field($_POST['post_subject'] ));
        update_post_meta( $my_post['ID'], '_sendpress_template', SPNL()->validate->int($_POST['template'] ));
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
            SendPress_Admin::redirect('Emails_Send', array('emailID'=>SPNL()->validate->_int('emailID') ));
        } else if (isset($_POST['submit']) && $_POST['submit'] == 'send-test'){
            $email = new stdClass;
            $email->emailID  = $my_post['ID'];
            $email->subscriberID = 0;
            $email->listID = 0;
            $email->to_email = $_POST['test-email'];
            $d =SendPress_Manager::send_test_email( $email );
            //print_r($d);
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>SPNL()->validate->_int('emailID') ));
        } else {
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>SPNL()->validate->_int('emailID') ));
        }


       
	}
	function admin_init(){
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
	}

	function html() {
		global $post_ID, $post;

		$list ='';

		$emailID = SPNL()->validate->_int('emailID');
        if($emailID  > 0){
            $post = get_post( $emailID );
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
                <a href="?page=<?php echo SPNL()->validate->page(); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
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
            <h2><?php _e('Test This Email','sendpress'); ?></h2>
            <p><input type="text" name="test-email" value="" class="sp-text" placeholder="Email to send test to." /></p>
            <button class="btn btn-success" name="submit" type="submit" value="send-test"><i class=" icon-white icon-inbox"></i> <?php _e('Send Test','sendpress'); ?></button>
        </div>
        </form>
	<?php
	}

}