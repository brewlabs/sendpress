<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails_Temp') ){


class SendPress_View_Emails_Temp extends SendPress_View_Emails{

	function admin_init(){
		add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
	}

	function delete(){
		//$this->security_check();
		$p = SPNL()->validate->_int('templateID');
		//$type = get_post_meta( $p , "_template_type", true);
		//if($type == 'clone'){
			wp_delete_post($p, true);
		//}
		SendPress_Admin::redirect('Emails_Temp');
	}

	function install(){
		//$this->security_check();
		SendPress_Template_Manager::install_template_content();
		SendPress_Admin::redirect('Emails_Temp');
	}

	function screen_options(){

		$screen = get_current_screen();
	 	
		$args = array(
			'label' => __('Emails per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_emails_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

 	

	function prerender($sp= false){
	
	

	}
	
	function html(){
		SendPress_Tracking::event('Emails Tab');
		//SendPress_Template_Manager::update_template_content();
		//Create an instance of our package class...
		$testListTable = new SendPress_Email_Local_Table();
		//Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items();
	?>
	
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<div id="taskbar" class="lists-dashboard rounded group"> 

		<h2><?php _e('Templates','sendpress'); ?></h2>
		<small><?php _e('Help','sendpress'); ?>: <a target="_blank" href="http://docs.sendpress.com/article/58-setting-up-a-newsletter-template/"><?php _e('Getting Started with Templates','sendpress'); ?></a></small>
	</div>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page(); ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display(); ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form><br>
	<a href="<?php echo SendPress_Admin::link('Emails_Temp',array('action'=>'install')); ?>" class="btn btn-primary">Install Starter Templates</a>


	<?php

	//echo '<a class="btn btn-primary" href="'.	SPNL()->get_customizer_link() .'">Customizer</a>';
	}

}



SendPress_Admin::add_cap('Emails_Templates','sendpress_email');

}