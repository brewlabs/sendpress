<?php
// SendPress Required Class: SendPress_Helper
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Helper {

	function log($args, $tofile = false) {

		 if( defined('WP_DEBUG') && WP_DEBUG === true ){

			if ( isset($args) ) {
				$log_message = '>--- '.date('r').'  ';

				if ( !is_array($args) ) {
					$log_message .= $args;
				} else {
					$count = 0;
					foreach ( $args as $a ) {
						if ( !is_array($a) ) {
							if ( strlen($a) > 30 )
								$out = substr($a,0,30 ).'...';
							else
								$out = $a;

							if ( $count > 0 )
								$log_message .=  ', ';

							$log_message .= $out;
							$count += 1;
						}
					}
				}

				if($tofile){
					$log_message .= '\n';
					SELF::append_log($log_message);
				}else{
					
				}
				
			}
		}
		return $args;
	}


		/**
		 * 
		 * writes a log message into a file
		 * @param unknown_type $msg : the messages to write to the file
		 * @param unknown_type $queueid : an optional queue id that the message is applied to, changes the output file name
		 */
	function append_log($msg, $queueid = -1 ) {
		if( defined('WP_DEBUG') && WP_DEBUG === true ){
			if ( !isset($queueid) || $queueid == -1 ) {
					$logfile = WP_CONTENT_DIR . '/mail.log';
			} else {
					$logfile = WP_CONTENT_DIR . '/mail-'.$queueid.'.log';
			}

		    $fp = fopen($logfile, 'a+');
		    fwrite($fp, $msg."\n");
		    fclose($fp);
		}
	}




}