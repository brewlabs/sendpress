<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Email extends SendPress_Public_View{
	function prerender(){
	
		
	}


	function html(){
		$email_id = get_query_var( 'spemail' );
		global $post;
		if($this->data()->id){
			$email_id = $this->data()->id;
		} 
		$post = get_post($email_id);
		$inline = false;
		if(isset($_GET['inline']) ){
			$inline = true;
		}
		echo SendPress_Template::get_instance()->render(false, true, $inline );
	}

	function page_start(){

	}
	function page_end(){

	}

}