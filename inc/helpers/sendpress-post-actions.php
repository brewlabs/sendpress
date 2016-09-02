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
        $public = SPNL()->validate->_int('public');
       
        SendPress_Data::create_list( array('name'=> $name, 'public'=>$public ) );
         wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
    
    break;

    case 'create-custom-field':
        $name =  sanitize_text_field($_POST['name']);
        $public = SPNL()->validate->_int('public');
       
        
         wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
    
    break;

    case 'edit-list':
       
        $listid = SPNL()->validate->_int('listID');
        $name =  sanitize_text_field(SPNL()->validate->_srting('name'));
        $public = SPNL()->validate->_int('public');
       
      
        SendPress_Data::update_list($listid, array( 'name'=>$name, 'public'=>$public ) );

        $page = apply_filters('sendpress_edit_list_redirect', SPNL()->validate->page());
      
        wp_redirect( esc_url_raw( admin_url('admin.php?page='. $page  ) ) );
    
    break;
   
    case 'save-email':
        $_POST['post_type'] = $this->_email_post_type;
        // Update post 37

        $my_post = _wp_translate_postdata(true);
       
        $my_post['post_status'] = 'publish';
        // Update the post into the database
        wp_update_post( $my_post );
        update_post_meta( $my_post['ID'], '_sendpress_subject', SPNL()->validate->_srting('post_subject') );
        update_post_meta( $my_post['ID'], '_sendpress_template', SPNL()->validate->_srting('template') );
        update_post_meta( $my_post['ID'], '_sendpress_status', 'private');

        SendPress_Email::set_default_style( $my_post['ID'] );
        //clear the cached file.
        delete_transient( 'sendpress_email_html_'. $my_post['ID'] );

        $this->save_redirect();


    break;

    case 'temaplte-widget-settings':

        $widget_options =  array();

        $widget_options['widget_options']['load_css'] = 0;
        $widget_options['widget_options']['load_ajax'] = 0;
        $widget_options['widget_options']['load_scripts_in_footer'] = 0;
        if(SPNL()->validate->_isset('load_css') ){
            $widget_options['widget_options']['load_css'] = SPNL()->validate->_string('load_css');
        }
        if( SPNL()->validate->_isset('load_ajax') ){
            $widget_options['widget_options']['load_ajax'] =  SPNL()->validate->_string('load_ajax');
        }
        if(SPNL()->validate->_isset('load_scripts_in_footer')){
            $widget_options['widget_options']['load_scripts_in_footer'] = SPNL()->validate->_string('load_scripts_in_footer');
        }

        SendPress_Option::set($widget_options);        
        wp_redirect( esc_url_raw( admin_url('admin.php?page=sp-settings&view=widget') ) );

    break;

   

}
















