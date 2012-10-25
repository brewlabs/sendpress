<?php
// SendPress Required Class: SP_Helper
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;
class SP_Helper {

	function log($args) {

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

				$log_message .= '\n';
				$this->append_log($log_message);
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