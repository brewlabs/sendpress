<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails') ){


class SendPress_View_Emails extends SendPress_View{

	function admin_init(){
		add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
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

 	function sub_menu($sp = false){

		?>
		<div class="navbar navbar-default" >
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>

    </button>
    <a class="navbar-brand" href="#">Emails</a>
	</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
					<li <?php if(!isset($_GET['view']) || isset($_GET['view']) && $_GET['view'] === 'style' || isset($_GET['view']) && $_GET['view'] === 'create' || isset($_GET['view']) && $_GET['view'] === 'send' || isset($_GET['view']) && $_GET['view'] === 'send-confirm'  || isset($_GET['view']) && $_GET['view'] === 'send-queue'  ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails'); ?>"><?php _e('Newsletters','sendpress'); ?></a>
				  	</li>
				  	<?php if(SendPress_Option::get('prerelease_templates') == 'yes') { ?>
				  	<!--
				  	<li <?php if(isset($_GET['view']) && $_GET['view'] === 'all'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Auto'); ?>"><?php _e('Autoresponders','sendpress'); ?></a>
				  	</li>
				  	 	-->
				  	<li <?php if(isset($_GET['view']) && ( $_GET['view'] === 'templates' || $_GET['view'] === 'tempedit' )){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Templates'); ?>"><?php _e('Templates','sendpress'); ?></a>
				  	</li>

				  	<li <?php if(isset($_GET['view']) && ( $_GET['view'] === 'temp' || $_GET['view'] === 'tempstyle' )){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Temp'); ?>"><span class="sp-label">SP</span>   <?php _e('Templates','sendpress'); ?></a>
				  	</li>
				  	<?php } ?>
				  	<li <?php if(isset($_GET['view']) &&  $_GET['view'] === 'social'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Social'); ?>"><?php _e('Social Icons','sendpress'); ?></a>
				  	</li>
				</ul>
			</div>
		</div>

		<?php



	}


	function prerender($sp= false){



	}

	function html($sp){
		 SendPress_Tracking::event('Emails Tab');
	//Create an instance of our package class...
	$testListTable = new SendPress_Emails_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<div id="taskbar" class="lists-dashboard rounded group">

		<div id="button-area">
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=create"><?php _e('Create Email','sendpress'); ?></a>
		</div>

	</div>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display(); ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}



SendPress_Admin::add_cap('Emails','sendpress_email');

}
