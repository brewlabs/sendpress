<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails_Tempedit') ){


class SendPress_View_Emails_Tempedit extends SendPress_View_Emails{

	function admin_init(){
add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
/*
wp_register_script('sendpress_codemirror', SENDPRESS_URL .'codemirror/lib/codemirror.js' ,'',SENDPRESS_VERSION);
wp_enqueue_script('sendpress_codemirror');
wp_register_script('sendpress_codemirror_mode', SENDPRESS_URL .'codemirror/mode/htmlmixed/htmlmixed.js' ,'',SENDPRESS_VERSION);
wp_enqueue_script('sendpress_codemirror_mode');
wp_register_style( 'sendpress_codemirror_css', SENDPRESS_URL . 'codemirror/lib/codemirror.css', '', SENDPRESS_VERSION );
wp_enqueue_style( 'sendpress_codemirror_css' );
*/
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
	
	function html($sp){
		 SendPress_Tracking::event('Emails Tab');

		
	//Create an instance of our package class...
	$testListTable = new SendPress_Email_Templates_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
	
<link rel="stylesheet" href="<?php echo SENDPRESS_URL ?>codemirror/lib/codemirror.css">
<link rel="stylesheet" href="<?php echo SENDPRESS_URL ?>codemirror/addon/hint/show-hint.css">
<script src="<?php echo SENDPRESS_URL ?>codemirror/lib/codemirror.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/addon/hint/show-hint.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/addon/hint/xml-hint.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/addon/hint/html-hint.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/mode/xml/xml.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/mode/javascript/javascript.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/mode/css/css.js"></script>
<script src="<?php echo SENDPRESS_URL ?>codemirror/mode/htmlmixed/htmlmixed.js"></script>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<div id="taskbar" class="lists-dashboard rounded group"> 

		<div id="button-area">  
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=create"><?php _e('Create Email','sendpress'); ?></a>
		</div>
		<h2><?php _e('Templates','sendpress'); ?></h2>
	</div>

	<textarea id="asdf">

</textarea>
	<script>
	var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("asdf"), {
    lineNumbers: true,
    styleActiveLine: true,
    matchBrackets: true,
    mode: "text/html",
    extraKeys: {"Tab": "autocomplete"}
  });

	jQuery(document).ready(function(){
		var x = jQuery('#wpbody').height();
		console.log(x);
		myCodeMirror.setSize('100%',(x - 90));
	});
	</script>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php //$testListTable->display(); ?>
	    <?php //wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}



SendPress_Admin::add_cap('Emails_Tempedit','sendpress_email');

}