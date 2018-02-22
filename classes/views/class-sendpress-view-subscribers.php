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

	 function delete_list(){
                SendPress_Data::delete_list( SPNL()->validate->_int('listID') );
                 wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
             }
    
            function delete_lists_bulk(){
                $list_delete =  SPNL()->validate->_int_array('list');
                foreach ($list_delete as $listID) {
                   SendPress_Data::delete_list( SPNL()->validate->int($listID));
                }
                 wp_redirect( esc_url_raw( admin_url('admin.php?page='.SPNL()->validate->page() ) ) );
            }


	function export_list(){
		//$this->security_check();
    	$l = SPNL()->validate->_int('listID');
         if( $l > 0 ){
            $items = SendPress_Data::export_subscirbers( $l );
            header("Content-type:text/octect-stream");
            header("Content-Disposition:attachment;filename=sendpress.csv");
            print "email,firstname,lastname,status \n";
            foreach($items as $user) {
                print  $user->email . ",". $user->firstname.",". $user->lastname.",". $user->status."\n" ;
            }
        }
        exit;
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
    <a class="navbar-brand" href="#"><?php _e('Subscribers','sendpress'); ?></a>
</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
					<li <?php if(! SPNL()->validate->_isset('view') ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers'); ?>"><i class="icon-list "></i> <?php _e('Lists','sendpress'); ?></a>
				  	</li>
				  	<?php do_action('sendpress-add-submenu-item', SPNL() );?>
					<li <?php if(SPNL()->validate->_string('view') === 'all'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers_All'); ?>"><i class="icon-user "></i> <?php _e('All Subscribers','sendpress'); ?></a>
				  	</li>
				  	<li <?php if(SPNL()->validate->_string('view') === 'custom'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Subscribers_Customfields'); ?>"><i class="icon-list "></i> <?php _e('Custom Fields','sendpress'); ?></a>
				  	</li>
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
	
	function html() {
	 SendPress_Tracking::event('Lists Tab');
		//Create an instance of our package class...
		$testListTable = new SendPress_Lists_Table();
		//Fetch, prepare, sort, and filter our data...
		$testListTable->prepare_items();

		?>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<div id="button-area">  
			<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=listcreate"><?php _e('Create List','sendpress'); ?></a>
		</div>
		<h2><?php _e('Lists','sendpress'); ?></h2>
		<form id="sendpress-lists" method="get">
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page() ?>" />
		    <!-- Now we can render the completed list table -->
		    <?php $testListTable->display(); ?>
		    <?php wp_nonce_field($this->_nonce_value); ?>
		</form>
	<?php 
	}

}
SendPress_Admin::add_cap('Subscribers','sendpress_subscribers');