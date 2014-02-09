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
    
        $name = $_POST['name'];
        $public = 0;
        if(isset($_POST['public'])){
            $public = $_POST['public'];
        }

        $this->createList( array('name'=> $name, 'public'=>$public ) );
        wp_redirect( '?page='.$_GET['page'] );
    
    break;

    case 'edit-list':
       
        $listid = $_POST['listID'];
        $name = $_POST['name'];
        $public = 0;
        if(isset($_POST['public'])){
            $public = $_POST['public'];
        }
      
        $this->updateList($listid, array( 'name'=>$name, 'public'=>$public ) );

        $page = apply_filters('sendpress_edit_list_redirect',$_GET['page']);
      
        wp_redirect( '?page='.$page );
    
    break;

    case 'account-setup':
    
        /*
        $options =  array();

        $options['sendmethod'] = $_POST['sendmethod'];

        $options['gmailuser'] = $_POST['gmailuser'];
        $options['gmailpass'] = $_POST['gmailpass'];

        $options['sp_user'] = $_POST['sp_user'];
        $options['sp_pass'] = $_POST['sp_pass'];

        $options['phpmailer_error'] = '';
        $options['last_test_debug'] = '';
        SendPress_Option::set($options);

        
        wp_redirect( admin_url('admin.php?page=sp-settings&view=account') );
        */
    
    break;
     case 'test-account-setup':
    
        
        
        $options['testemail'] = $_POST['testemail'];
        
        SendPress_Option::set($options);

        $this->send_test();
        wp_redirect( admin_url('admin.php?page=sp-settings&view=account') );
    
    break;
     case 'feedback-setup':
    
        
        $options =  array();

        $options['feedback'] = $_POST['feedback'];

      

        SendPress_Option::set($options);

        
        wp_redirect( admin_url('admin.php?page=sp-settings&view=feedback') );
    
    break;

    case 'create-subscriber':
        /*
        $email = $_POST['email'];
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $listID = $_POST['listID'];
        $status = $_POST['status'];

        if( is_email($email) ){

            $result = $this->addSubscriber( array('firstname'=> $fname ,'email'=> $email,'lastname'=>$lname ) );

            SendPress_Data::update_subscriber_status($listID, $result, $status ,false);

        }

        wp_redirect( '?page='.$_GET['page']. "&view=subscribers&listID=".$listID );
        */
    
    break;
    case 'edit-subscriber':
        $email = $_POST['email'];
        $fname = $_POST['firstname'];
        $lname = $_POST['lastname'];
        $listID = $_POST['listID'];
        $status = $_POST['status'];
        $subscriberID = $_POST['subscriberID'];
        if( is_email($email) ){

            $result = $this->updateSubscriber($subscriberID, array('firstname'=> $fname ,'email'=> $email,'lastname'=>$lname ) );

            $this->updateStatus($listID,$subscriberID, $status);

            //$this->linkListSubscriber($listID, $result, 1);
        }

        wp_redirect( '?page='.$_GET['page']. "&view=subscribers&listID=".$listID );

    break;
    case 'template-default-style':

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

       

        wp_redirect( admin_url('admin.php?page=sp-settings&view=styles') );

    break;

    case 'template-default-setup':

        $saveid = $_POST['post_ID'];
        

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
        if ( !isset( $fromname ) )
            $fromname = 'WordPress';
        
        if(isset($_POST['fromemail'])){
            $fromemail= $_POST['fromemail'];
        }


        if ( !isset( $fromemail ) ) {
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
        SendPress_Option::set('fromemail', $fromemail );
        SendPress_Option::set('fromname', $fromname );

         wp_redirect( admin_url('admin.php?page=sp-settings') );

    break;

    case 'save-send-confirm':
        $saveid = $_POST['post_ID'];

        update_post_meta( $saveid, 'send_date', date('Y-m-d H:i:s') );

        $email_post = get_post( $saveid );

        $subject = SendPress_Option::get('current_send_subject_'. $saveid);

        $info = SendPress_Option::get('current_send_'.$saveid);
        $slug = $this->random_code();

        $new_id = SendPress_Posts::copy($email_post, $subject, $slug, $this->_report_post_type );
        SendPress_Posts::copy_meta_info($new_id, $saveid);

        $this->log('ADD QUEUE');

        $count = 0;    

        if(isset($info['listIDS'])){
            foreach($info['listIDS'] as $list_id){
                $_email = $this->get_active_subscribers( $list_id );

                foreach($_email as $email){
                   
                     $go = array(
                        'from_name' => 'Josh',
                        'from_email' => 'joshlyford@gmail.com',
                        'to_email' => $email->email,
                        'emailID'=> $new_id,
                        'subscriberID'=> $email->subscriberID,
                        //'to_name' => $email->fistname .' '. $email->lastname,
                        'subject' => $subject,
                        'listID'=> $list_id
                        );
                   
                    $this->add_email_to_queue($go);
                    $count++;

                }


            }
        }


        if(isset($info['testemails'])){
            foreach($info['testemails'] as $email){
                   
                     $go = array(
                        'from_name' => 'Josh',
                        'from_email' => 'joshlyford@gmail.com',
                        'to_email' => $email['email'],
                        'emailID'=> $new_id,
                        'subscriberID'=> 0,
                        'subject' => $subject,
                        'listID' => 0
                        );
                   
                    $this->add_email_to_queue($go);
                    $count++;

                


            }
        }

        update_post_meta($new_id,'_send_count', $count );
        update_post_meta($new_id,'_send_data', $info );

      $this->log('END ADD QUEUE');


        wp_redirect( '?page=sp-queue' );

    break;



    case 'create-subscribers':
    
        $csvadd = "email,firstname,lastname\n" . trim($_POST['csv-add']);
        $listID = $_POST['listID'];

        $newsubscribers = $this->subscriber_csv_post_to_array($csvadd);

        foreach( $newsubscribers as $subscriberx){
            if( is_email( trim($subscriberx['email'] ) ) ){
          
            $result = SendPress_Data::add_subscriber( array('firstname'=> trim($subscriberx['firstname']) ,'email'=> trim($subscriberx['email']),'lastname'=> trim($subscriberx['lastname']) ) );
            SendPress_Data::update_subscriber_status($listID, $result, 2, false);
            }
        }
    
        wp_redirect( '?page='.$_GET['page']. "&view=subscribers&listID=".$listID );
    
    break;

    case 'save-send':
    /*
    $csvadd ="email,firstname,lastname\n" . trim($_POST['test-add']);
    $data=   $this->subscriber_csv_post_to_array($csvadd);
    $listids = isset($_POST['listIDS']) ? $_POST['listIDS'] : array();
        SendPress_Option::set('current_send_'. $_POST['post_ID'], array(
            'listIDS' =>  $listids,
            'testemails'=> $data
            ));
        SendPress_Option::set('current_send_subject_'. $_POST['post_ID'],$_POST['post_subject']);



        $this->save_redirect( $_POST  );
        */
    break;   
    case 'save-style':

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

        $_POST['post_type'] = $this->_email_post_type;
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
        
        $this->save_redirect( $_POST  );
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
        wp_redirect( admin_url('admin.php?page=sp-settings&view=widget') );

    break;

   

}
















