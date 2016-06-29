<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Reports extends SendPress_View{
	
	function admin_init(){
		add_action('load-sendpress_page_sp-reports',array($this,'screen_options'));
	}

	function screen_options(){

		$screen = get_current_screen();
	 	
		$args = array(
			'label' => __('Reports per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_reports_per_page'
		);
		add_screen_option( 'per_page', $args );
	}



 function sub_menu(){
 		?>
		<div class="navbar navbar-default" >
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
       <span class="sr-only"><?php _e('Toggle navigation','sendpress'); ?></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>

    </button>
    <a class="navbar-brand" href="#"><?php _e('Reports','sendpress'); ?></a>
	</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
					<li <?php if(!SPNL()->validate->_isset('view') ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Reports'); ?>"><?php _e('Newsletters','sendpress'); ?></a>
				  	</li>
				  	<li <?php if(SPNL()->validate->_string('view') === 'tests'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Reports_Tests'); ?>"><?php _e('Tests','sendpress'); ?></a>
				  	</li>
				  	
				</ul>

				
			</div>
		</div>
		
		<?php

		do_action('sendpress-reports-sub-menu');
		
	}	

	function html(){
		 SendPress_Tracking::event('Reports Tab');
		//Create an instance of our package class...
		$sp_reports_table = new SendPress_Reports_Table();
		//Fetch, prepare, sort, and filter our data...
		$sp_reports_table->prepare_items();
		?>
		<br>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="email-filter" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page(); ?>" />
		    <!-- Now we can render the completed list table -->
		    <?php $sp_reports_table->display(); ?>
		    <?php wp_nonce_field( $this->_nonce_value ); ?>
		</form>
		<h3><?php _e('Information','sendpress'); ?></h3>
		<div class='well'>
		<span class="label label-success"><?php _e('Unique','sendpress');?></span> <?php _e('The total number of different recipients that have clicked on a link or opened an email.','sendpress');?><br><br>

		<span class="label label-info"><?php _e('Total','sendpress');?></span> <?php _e('The total number of clicks or opens that have happened. Regardless of who clicked or opened the email.','sendpress');?>
		</div>
		<?php
	
	}
}
SendPress_Admin::add_cap('Reports','sendpress_reports');
