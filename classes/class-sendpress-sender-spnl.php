<?php 


// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Sender_SPNL extends SendPress_Sender {
	
	function label(){
		return __('WP Email Delivery (Beta) ','sendpress');
	}

	function save(){
		
		$options =  array();
	 	$options['sendpress-key'] = $_POST['sendpress-key'];
	 	if( isset($_POST['sendpress-verifyssl']) ){
	 		$options['verifyssl'] = $_POST['sendpress-verifyssl'];
	 	} 
        
        SendPress_Option::set_sender('sendpress', $options );

	}

	function settings(){ 

		$m = SendPress_Option::get_sender( 'sendpress' );
		?>
		<p><?php _e( '<b>Access Key</b>', 'sendpress' ); ?>.</p>
		<?php _e( 'API Key' , 'sendpress'); ?>
		<p><input name="sendpress-key" type="text" value="<?php echo $m['sendpress-key']; ?>" style="width:100%;" /></p>
		<br>
		<?php _e( 'Disable SSL Verification' , 'sendpress'); ?>
		<?php $ctype = isset( $m['verifyssl'] ) ? true : false ; ?>
		<p><input name="sendpress-verifyssl" type="checkbox"  <?php if($ctype=='donotverify'){echo "checked='checked'"; } ?>  value="donotverify" /> <small>Not Recommended but required on some hosts.</small></p>
		<?php


	}

	function send_email($to, $subject, $html, $text, $istest = false ,$sid , $list_id, $report_id ){
		
		//$user = SendPress_Option::get( 'mandrilluser' );
		//$pass = SendPress_Option::get( 'mandrillpass' );
		$from_email = SendPress_Option::get('fromemail');
		//$hdr = new SendPress_SendGrid_SMTP_API();
		$m = SendPress_Option::get_sender( 'sendpress' );
		//$hdr->addFilterSetting('dkim', 'domain', SendPress_Manager::get_domain_from_email($from_email) );
		//$phpmailer->AddCustomHeader(sprintf( 'X-SMTPAPI: %s', $hdr->asJSON() ) );
			$info = array(
				"X-SP-METHOD"=>"WPED.co",
				"X-SP-LIST"=> $list_id,
				"X-SP-REPORT"=> $report_id ,
				"X-SP-SUBSCRIBER"=>$sid,
				"X-SP-DOMAIN"=> home_url()
			);

			$url = 'https://gateway.wped.co/send/';
			//$url = 'http://spnl.dev/';
			$verify_ssl = true;
			if( isset( $m['verifyssl'] ) && $m['verifyssl'] == 'donotverify' ){
				$verify_ssl = false;
				$url = 'http://api.wped.co/send';
			}

		    $message = array(
			    'to'        => array( 
			    	array( 'email' => $to)
			    ),
			    'subject'   => $subject,
			    'html'      => $html,
			    'text'      => $text,
			    'from_email'  => $from_email,
			    'from_name'=>SendPress_Option::get('fromname'),
			    //'x-smtpapi'=>$hdr->asJSON(),
			    'headers'=> $info,
			    'inline_css' =>true,
			    'subaccount' => $m['sendpress-key'],
			    'metadata' => array(
			    	'return'=> home_url()
			    	)
		     );
		    
		  
			
			$response = wp_remote_post( $url , array(
				'method' => 'POST',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array('Content-Type' => 'application/json'),
				'body' => json_encode( $message ),
				'sslverify' => $verify_ssl,
				'cookies' => array()
			    )
			);
			error_log( print_r( $response , true ) );
			if( is_wp_error( $response ) ) {
			   	$error_message = $response->get_error_message();
			  	SPNL()->log->add( 'WPED Sending' , $error_message , 0 , 'sending' );
			   	return false;
			} else {
				return true;
			}

			return false;   
			  
	}


}


