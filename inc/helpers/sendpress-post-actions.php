<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}
/**
*
*   SENDPRESS ACTIONS 
*   
*   see @sendpress class line 101
*   Handles saving data and other user actions.
*
**/

switch ( $this->_current_action ) {
            
    case 'create-list':
    
        $name =  sanitize_text_field($_POST['name']);
        $public = 0;
        if(isset($_POST['public'])){
            $public = SPNL()->validate->int($_POST['public']);
        }

        SendPress_Data::create_list( array('name'=> $name, 'public'=>$public ) );
         wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
    
    break;

    case 'edit-list':
       
        $listid = SPNL()->validate->int($_POST['listID']);
        $name =  sanitize_text_field($_POST['name']);
        $public = 0;
        if(isset($_POST['public'])){
            $public = $_POST['public'];
        }
      
        SendPress_Data::update_list($listid, array( 'name'=>$name, 'public'=>$public ) );

        $page = apply_filters('sendpress_edit_list_redirect', SPNL()->validate->page($_GET['page']));
      
        wp_redirect( esc_url_raw( admin_url('admin.php?page='. $page  ) ) );
    
    break;
   
    case 'save-email':
        $_POST['post_type'] = $this->_email_post_type;
        // Update post 37

        $my_post = _wp_translate_postdata(true);
        /*            
        $my_post['ID'] = $_POST['post_ID'];
        $my_post['post_content'] = $_POST['content'];
        $my_post['post_title'] = $_POST['post_title'];
        */
        $my_post['post_status'] = 'publish';
        // Update the post into the database
        wp_update_post( $my_post );
        update_post_meta( $my_post['ID'], '_sendpress_subject', $_POST['post_subject'] );
        update_post_meta( $my_post['ID'], '_sendpress_template', $_POST['template'] );
        update_post_meta( $my_post['ID'], '_sendpress_status', 'private');

        SendPress_Email::set_default_style( $my_post['ID'] );
        //clear the cached file.
        delete_transient( 'sendpress_email_html_'. $my_post['ID'] );

        $this->save_redirect( $_POST  );


    break;

    case 'temaplte-widget-settings':

        $widget_options =  array();

        $widget_options['widget_options']['load_css'] = 0;
        $widget_options['widget_options']['load_ajax'] = 0;
        $widget_options['widget_options']['load_scripts_in_footer'] = 0;
        if(isset($_POST['load_css'])){
            $widget_options['widget_options']['load_css'] = $_POST['load_css'];
        }
        if(isset($_POST['load_ajax'])){
            $widget_options['widget_options']['load_ajax'] = $_POST['load_ajax'];
        }
        if(isset($_POST['load_scripts_in_footer'])){
            $widget_options['widget_options']['load_scripts_in_footer'] = $_POST['load_scripts_in_footer'];
        }

        SendPress_Option::set($widget_options);        
        wp_redirect( esc_url_raw( admin_url('admin.php?page=sp-settings&view=widget') ) );

    break;

   

}
















