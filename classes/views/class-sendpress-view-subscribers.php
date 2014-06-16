<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers extends SendPress_View {
	function _get_called_class(){
		return "SendPress_View_Subscribers";
	}

	function admin_init(){
		add_action('load-sendpress_page_sp-subscribers',array($this,'screen_options'));
		//add_action('sendpress-subscribers-sub-menu', array('SendPress_View_Subscribers','default_header'));
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
    <a class="navbar-brand" href="#">Subscribers</a>
</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
					<li <?php if(!isset($_GET['view']) ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers'); ?>"><i class="icon-list "></i> <?php _e('Lists','sendpress'); ?></a>
				  	</li>
				  	<?php do_action('sendpress-add-submenu-item',$sp);?>
					<li <?php if(isset($_GET['view']) && $_GET['view'] === 'all'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers_All'); ?>"><i class="icon-user "></i> <?php _e('All Subscribers','sendpress'); ?></a>
				  	</li>
				  	<?php if(SendPress_Option::get('beta')){ ?>
				  	<li <?php if(isset($_GET['view']) && $_GET['view'] === 'custom'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers_Custom'); ?>"><i class="icon-list "></i> <?php _e('Custom Fields','sendpress'); ?></a>
				  	</li>
				  	<?php } ?>

				</ul>
			</div>
		</div>
		
		<?php

		do_action('sendpress-subscribers-sub-menu');
	}

	function screen_options(){

		$screen = get_current_screen();
	 	
		$args = array(
			'label' => __('Lists per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_lists_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

	function default_header(){
	?>
		
	<?php
	}
	
	function html($sp) {
	 SendPress_Tracking::event('Lists Tab');
		//Create an instance of our package class...
		$testListTable = new SendPress_Lists_Table();
		//Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items();

		?>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<div id="button-area">  
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=listcreate"><?php _e('Create List','sendpress'); ?></a>
		</div>
		<h2><?php _e('Lists','sendpress'); ?></h2>
		<form id="sendpress-lists" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		    <!-- Now we can render the completed list table -->
		    <?php $testListTable->display() ?>
		    <?php wp_nonce_field($sp->_nonce_value); ?>
		</form>
	<?php 
	}

}
SendPress_Admin::add_cap('Subscribers','sendpress_subscribers');