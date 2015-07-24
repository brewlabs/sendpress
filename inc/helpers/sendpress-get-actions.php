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
                $email_delete = $_GET['qemail'];

                foreach ($email_delete as $qID) {
                    $q = SPNL()->validate->int($qID);
                    if($q > 0 ){
                        $this->delete_queue_email($q);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );

            break;
            case 'queue-delete':
                $email_delete = SPNL()->validate->int($_GET['emailID']);
                if($email_delete > 0){
                    $this->delete_queue_email($email_delete);
                }
                 wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
            case 'requeue':
                $email = SPNL()->validate->int($_GET['emailID']);
                if($email > 0){
                    $this->requeue_email($email);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );

            break;
            case 'delete-list':
                $this->deleteList(SPNL()->validate->int($_GET['listID']));
                 wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
            case 'delete-lists-bulk':
                $list_delete = $_GET['list'];

                foreach ($list_delete as $listID) {
                   $this->deleteList( SPNL()->validate->int($listID));
                }
                 wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;

            case 'delete-subscriber':
                $l = SPNL()->validate->int($_GET['listID']);
                $s = SPNL()->validate->int($_GET['subscriberID']);
                if($l > 0 && $s > 0){
                    $this->unlink_list_subscriber( , $_GET['subscriberID']);
                }
                wp_redirect( esc_url_raw( admin_url( 'admin.php?page='.SPNL()->validate->page($_GET['page']) .'&view=subscribers&listID='.$_GET['listID'] ) ) );
            break;

            case 'delete-subscribers-bulk':
                 $subscriber_delete = $_GET['subscriber'];
                 $list  = SPNL()->validate->int($_GET['listID']); 
                foreach ($subscriber_delete as $subscriberID) {
                    $this->unlink_list_subscriber( $list  , SPNL()->validate->int( $subscriberID ));
                }
                wp_redirect( esc_url_raw( admin_url( 'admin.php?page='.SPNL()->validate->page($_GET['page']) .'&view=subscribers&listID='.$_GET['listID'] ) ) );
            break;

            case 'delete-report':
                $r = SPNL()->validate->int($_GET['reportID']);
                if( $r > 0 ){
                    SendPress_Posts::report_delete($_GET['reportID']);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
            case 'delete-reports-bulk':
           
                $email_delete = $_GET['report'];

                foreach ($email_delete as $emailID) {
                    $email = SPNL()->validate->int($emailID);
                    if( $email > 0 ){
                        SendPress_Posts::report_delete($emailID);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
            case 'delete-email':
                $email = SPNL()->validate->int($emailID);
                if( $email > 0 ){
                    SendPress_Posts::delete($email);
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
            case 'delete-emails-bulk':
                $email_delete = $_GET['email'];

                foreach ($email_delete as $emailID) {
                    $email = SPNL()->validate->int($emailID);
                    if( $email > 0 ){
                        SendPress_Posts::delete($email);
                    }
                }
                wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page($_GET['page']) ) ) );
            break;
          
            case 'export-list':
               
                 $l = SPNL()->validate->int($_GET['listID']);
                 if( $l > 0 ){
                    $items = $this->exportList($_GET['listID']);
                    header("Content-type:text/octect-stream");
                    header("Content-Disposition:attachment;filename=sendpress.csv");
                    print "email,firstname,lastname,status \n";
                    foreach($items as $user) {
                        print  $user->email . ",". $user->firstname.",". $user->lastname.",". $user->status."\n" ;
                    }
                }
                exit;

            
            break;
       

        
        }