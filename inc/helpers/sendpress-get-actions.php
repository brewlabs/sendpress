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

            case 'delete-email-queue':
                $email_delete = SPNL()->validate->_int_array('qemail');
                foreach ($email_delete as $qID) {
                    $q = SPNL()->validate->int($qID);
                    if($q > 0 ){
                        SendPress_Data::remove_email_from_queue($q);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );

            break;
            case 'queue-delete':
                $email_delete = SPNL()->validate->_int('emailID');
                
                if($email_delete > 0){
                    SendPress_Data::remove_email_from_queue($email_delete);
                }
                
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            break;
            case 'requeue':
                $email = SPNL()->validate->_int('emailID');
                if($email > 0){
                   SendPress_Data::requeue_email($email);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );

            break;
           
            case 'delete-subscriber':
                $l = SPNL()->validate->_int('listID');
                $s = SPNL()->validate->_int('subscriberID');
                if($l > 0 && $s > 0){
                    SendPress_Data::remove_subscriber_status($l , $s);
                }
                wp_redirect( esc_url_raw( admin_url( 'admin.php?page='.SPNL()->validate->page() .'&view=subscribers&listID='.$l) ) );
            break;

            case 'delete-subscribers-bulk':
                 $subscriber_delete = SPNL()->validate->_int_array('subscriber');
                 $list  = SPNL()->validate->_int('listID'); 
                foreach ($subscriber_delete as $subscriberID) {
                    SendPress_Data::remove_subscriber_status( $list  , SPNL()->validate->int( $subscriberID ));
                }
                wp_redirect( esc_url_raw( admin_url( 'admin.php?page='.SPNL()->validate->page() .'&view=subscribers&listID='. $list ) ) );
            break;

            case 'delete-report':
                $r = SPNL()->validate->_int('reportID');
                if( $r > 0 ){
                    SendPress_Posts::report_delete($r);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            break;
            case 'delete-reports-bulk':
           
                $email_delete =SPNL()->validate->_int_array('report');

                foreach ($email_delete as $emailID) {
                    $email = SPNL()->validate->int($emailID);
                    if( $email > 0 ){
                        SendPress_Posts::report_delete($emailID);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            break;
            case 'delete-email':
                $email = SPNL()->validate->int($emailID);
                if( $email > 0 ){
                    SendPress_Posts::delete($email);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            break;
            case 'delete-emails-bulk':
                $email_delete = SPNL()->validate->_int_array('email');

                foreach ($email_delete as $emailID) {
                    $email = SPNL()->validate->int($emailID);
                    if( $email > 0 ){
                        SendPress_Posts::delete($email);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            break;
          
          
       

        
        }