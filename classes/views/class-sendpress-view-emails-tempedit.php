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

	function save(){

		$template = get_post($_POST['post_ID']);
		$template->post_content = $_POST['template-content'];
		$template->post_title = $_POST['post_subject'];
 		wp_update_post( $template );

		SendPress_Admin::redirect('Emails_Tempedit', array('templateID'=>$_GET['templateID'] ));
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
	<?php 
		$template = get_post($_GET['templateID']);
		
	?>
	<form id="template-editor" method="POST">
	<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $template->ID; ?>" />

	
	<h2>Edit Template</h2>
	<br><br>
	<div class="sp-row">
	
	<div class="sp-75 sp-first">
	<div class="alert alert-danger fade hide">
  <?php _e('<strong>Notice!</strong> You must have an {unsubscribe-link} in your template.','sendpress'); ?>
</div>
	<div><iframe id="iframe1" style="width: 100%;"></iframe></div>
	<textarea id="template-content" name="template-content"><?php echo stripcslashes($template->post_content); ?></textarea>
	<script>

		

	jQuery(document).ready(function($){
		var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("template-content"), {
		    lineNumbers: true,
		    styleActiveLine: true,
		    matchBrackets: true,
		    mode: "text/html",
		    extraKeys: {"Tab": "autocomplete"}
		  }); 
		$('#template-editor').submit(function(e){
			myCodeMirror.save();
			var txt = $('#template-content').val();
			var unsub = txt.indexOf('{unsubscribe-link}') != -1;
			if(unsub == false){
					e.preventDefault();
				$('.alert').removeClass('hide').addClass('in');  
   			}
			
		});
		$('#iframe1').hide();
		$("#code-edit").click(function(e){
			reset_buttons();
			$(this).addClass('active');
			e.preventDefault();
			$('.CodeMirror').show();
			$('#iframe1').hide();
		});


		$("#code-preview").click(function(e){
			e.preventDefault();
			reset_buttons();
			$(this).addClass('active');
			$('.CodeMirror').hide();
			$('#iframe1').show();
			myCodeMirror.save();
			$('#iframe1').contents().find('html').html( $('#template-content').val() );
		});

		$("#code-preview-live").click(function(e){
			reset_buttons();
			e.preventDefault();
			$(this).addClass('active');
			$('.CodeMirror').hide();
			$('#iframe1').show();
			myCodeMirror.save();
			$('#iframe1').contents().find('html').html( $('#template-content').val() );
		});

		function reset_buttons(){
			$("#code-edit-buttons button").removeClass('active');
		}

		
		var x = $( window ).height(); //jQuery('#wpbody').height();
		
		$('.btn-group .btn').tooltip({container: 'body'});
		$('#iframe1').height((x - 350));
		myCodeMirror.setSize('100%',(x - 350));
	});
	</script>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <!-- Now we can render the completed list table -->
	    <?php //$testListTable->display(); ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	
	</div>
	<div class="sp-25 ">
		<div class="btn-group btn-group-justified " style="float:right;"  >
      
            </div>
            
		<div  class="btn-toolbar">

			<div class="btn-group btn-group-justified" id="code-edit-buttons" >
				<div class="btn-group">
				<button id="code-edit" class="btn btn-lg btn-default active" data-toggle="tooltip" data-placement="top" title="Code Editor"><span class="glyphicon glyphicon-pencil"></span></button>
				</div>
<div class="btn-group">
				<button id="code-preview" class="btn btn-lg btn-default" data-toggle="tooltip" data-placement="top" title="Preview"><span class="glyphicon glyphicon-eye-close"></span></button>
				<!--<button id="code-preview-live" class="btn btn-lg btn-default" data-toggle="tooltip" data-placement="top" title="Preview with Example Data"><span class="glyphicon glyphicon-eye-open"></span></button>-->
				</div>
				    <a href="<?php echo SendPress_Admin::link('Emails_Templates'); ?>" id="cancel-update" class="btn btn-lg btn-default" data-toggle="tooltip" data-placement="top" title="Cancel"><span class="glyphicon glyphicon-remove"></span></a>
            <div class="btn-group">
            	<button class="btn btn-default btn-lg " type="submit" value="save" name="submit"  data-toggle="tooltip" data-placement="top" title="Save"><span class="glyphicon glyphicon-floppy-disk"></span></button></div>
			</div>
			   
        </div>
	<br><br>
	<?php $this->panel_start('<span class="glyphicon glyphicon-list-alt"></span> '. __('Template Name','sendpress')); ?>

	<input type="text" name="post_subject" id="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( $template->post_title )); ?>"  autocomplete="off" />
		<?php $this->panel_end(); ?>

		<?php $this->panel_start('<span class="glyphicon glyphicon-tags"></span> '. __('Template Tags','sendpress')); ?>
<p><code>[sp-broswer]</code> <small>Link to browser version</small></p>
<p><code>[sp-header]</code> <small>Content from editor</small></p>
<p><code>[sp-content-1]</code> <small>Content from editor</small></p>
<p><code>[sp-content-2]</code> <small>Content from editor</small></p>
<p><code>[sp-content-3]</code> <small>Content from editor</small></p>
<p><code>[sp-content-4]</code> <small>Content from editor</small></p>
<p><code>[sp-content-5]</code> <small>Content from editor</small></p>
<p><code>[sp-content-6]</code> <small>Content from editor</small></p>
<p><code>[sp-canspam]</code> <small>CANSPAM from settings</small></p>
<p><code>[sp-unsubscribe]</code> <small>Link to Unsubscribe</small></p>
<p><code>[sp-manage]</code> <small>Link to Manage Subscription</small></p>


		<?php $this->panel_end(); ?>
	</div>
</div></form>
	<?php
	}

}



SendPress_Admin::add_cap('Emails_Tempedit','sendpress_email');

}