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
                
                   $this->delete_queue_email($qID);
                }
                wp_redirect( '?page='.$_GET['page'] );

            break;
            case 'queue-delete':
                $email_delete = $_GET['emailID'];
                $this->delete_queue_email($email_delete);
                wp_redirect( '?page='.$_GET['page'] );
            break;
            case 'requeue':
                $email = $_GET['emailID'];
                $this->requeue_email($email);
                wp_redirect( '?page='.$_GET['page'] );

            break;
            case 'delete-list':
                $this->deleteList($_GET['listID']);
                wp_redirect( '?page='.$_GET['page'] );
            break;
            case 'delete-lists-bulk':
                $list_delete = $_GET['list'];

                foreach ($list_delete as $listID) {
                   $this->deleteList($listID);
                }
                wp_redirect( '?page='.$_GET['page'] );
            break;

            case 'delete-subscriber':
                $this->unlink_list_subscriber($_GET['listID'] , $_GET['subscriberID']);
                wp_redirect( '?page='.$_GET['page'] .'&view=subscribers&listID='.$_GET['listID']);
            break;

            case 'delete-subscribers-bulk':
                 $subscriber_delete = $_GET['subscriber'];

                foreach ($subscriber_delete as $subscriberID) {
                    $this->unlink_list_subscriber($_GET['listID'] , $subscriberID);
                }
               wp_redirect( '?page='.$_GET['page'] .'&view=subscribers&listID='.$_GET['listID']);
            break;

            case 'delete-report':

                SendPress_Posts::delete($_GET['reportID']);
                wp_redirect( '?page='.$_GET['page'] );
            break;
            case 'delete-reports-bulk':
           
                $email_delete = $_GET['report'];

                foreach ($email_delete as $emailID) {
                    SendPress_Posts::delete($emailID);
                }
                wp_redirect( '?page='.$_GET['page'] );
            break;
            case 'delete-email':
            
                SendPress_Posts::delete($_GET['emailID']);
                wp_redirect( '?page='.$_GET['page'] );
            break;
            case 'delete-emails-bulk':
                $email_delete = $_GET['email'];

                foreach ($email_delete as $emailID) {
                    SendPress_Posts::delete($emailID);
                }
                wp_redirect( '?page='.$_GET['page'] );
            break;
          
            case 'export-list':
               
                
                $items = $this->exportList($_GET['listID']);
                    
                header("Content-type:text/octect-stream");
                header("Content-Disposition:attachment;filename=sendpress.csv");
                print "email,firstname,lastname,status \n";
                foreach($items as $user) {
                    print  $user->email . ",". $user->firstname.",". $user->lastname.",". $user->status."\n" ;
                }
                exit;

            
            break;
       

        
        }