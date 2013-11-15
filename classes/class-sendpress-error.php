<?php
// SendPress Required Class: SendPress_Helper
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Error {

	static function log($args) {
		//Only Log data if not in production
		if( defined('WP_DEBUG') && WP_DEBUG === true || defined("SENDPRESS_LOG_ERROR") ){
			if ( isset($args) ) {
				if ( !is_array($args) ) {
					error_log(self::getFunc());
					error_log($args);
				} else {
					error_log(self::getFunc());
					error_log(print_r($args,true));
				}
				error_log(' ');
			}	
		}
	}

	static function getFunc() {
		  $stack = debug_backtrace();
		  $n = 0;
		  // Skip over own functions.
		  while($n < count($stack) 
		  && basename($stack[$n]['file']) == basename(__FILE__))
		    $n++;

		  if($n >= count($stack)) {
		    // No other function found.
		    $func = '?';

		  } elseif(isset($stack[$n+1])) {
		    // Function call.
		    $c = $stack[$n+1];
		    if(!empty($c['class']))
		      $class = $c['class'];
		    else
		      $class = $stack[$n]['file'].':'.$stack[$n]['line'].',';
		    $func = $class.@$c['type'].$c['function'];

		  } else {
		    // Direct from script file.
		    $c = $stack[$n];
		    $func = $stack[$n]['file'].':'.$stack[$n]['line'];
		  }

		  return $func;
		}


}