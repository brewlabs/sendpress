<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
* SendPress_View_Emails_Create
*
* @uses     SendPress_View
*
* @package  SendPress
* @since 0.8.7
*
*/
class SendPress_View_Emails_Tempcreate extends SendPress_View_Emails {

	function save(){
		//$this->security_check();
		$post = get_default_post_to_edit( 'sp_template' , true );
		$post_ID = $post->ID;
	
		global $current_user;
		$content = '';
		switch($_POST['starter']){
			case 'blank':
				$content = '';
			break;
			default:
				$content = 'Default HTML';
			break;



		}

        /*            
        $my_post['ID'] = $_POST['post_ID'];
        $my_post['post_content'] = $_POST['content'];
        $my_post['post_title'] = $_POST['post_title'];
        */
      	$post->post_title = $_POST['post_title'];
        $post->post_status = 'sp-custom';
        $post->post_content = $content;
        // Update the post into the database
        wp_update_post( $post );
       
        //SendPress_Email::set_default_style( $my_post['ID'] );
        //clear the cached file.
       
        
        SendPress_Admin::redirect( 'Emails_Tempedit' , array('templateID' =>  $post->ID  )   );
        //$this->save_redirect( $_POST  );

	}
	
	function html() {
		
	}

}
