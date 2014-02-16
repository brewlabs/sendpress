<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Subscribers extends SendPress_View_Subscribers {
	
	function admin_init(){
		add_action('load-sendpress_page_sp-subscribers',array($this,'screen_options'));
	}

	function screen_options(){

		$screen = get_current_screen();
	 	
	 
		$args = array(
			'label' => __('Subscribers per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_subscribers_per_page'
		);
		add_screen_option( 'per_page', $args );
	}
	

	function remove_subscribers( $get, $sp ){
		SendPress_Data::remove_all_subscribers( $get['listID'] );
		SendPress_Admin::redirect('Subscribers_Subscribers', array('listID'=> $get['listID'] ));
	}


	function html($sp) {
	
		$list ='';
if(isset($_GET['listID']) && $_GET['listID'] > 0 ){
	//$listinfo = $this->getDetail( $this->lists_table(),'listID', $_GET['listID'] );	
	$listinfo = get_post($_GET['listID']);
	$list = '&listID='.$_REQUEST['listID'];
	$listname = 'for '. $listinfo->post_title;
}
	//Create an instance of our package class...
	$testListTable = new SendPress_Subscribers_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
		<div id="button-area">  
			
			<a class="btn btn-primary btn-large" href="?page=<?php echo $_REQUEST['page']; ?>&view=add<?php echo $list; ?>"><?php _e('Add Subscriber','sendpress'); ?></a>
		</div>
		<h2><?php _e('Subscribers','sendpress'); ?> <?php echo $listname; ?> </h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="movies-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	    <?php if(isset($_GET['listID']) && $_GET['listID'] > 0 ){ ?>
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <?php  } ?>
	    <input type="hidden" name="view" value="<?php echo $_GET['view']; ?>" />

	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
	<form  method='get'>
		<input type='hidden' value="<?php echo $_GET['page']; ?>" name="page" />
		
		<input type='hidden' value="unlink-lisk" name="action" />
		<input type='hidden' name="listid" value="<?php echo $_GET['listID'] ?>" />

		<br>
		<a class="btn btn-danger " data-toggle="modal" href="#sendpress-empty-list" ><i class="icon-warning-sign "></i> <?php _e('Remove All Subscribers from List','sendpress'); ?></a>
		<?php wp_nonce_field($sp->_nonce_value); ?>
	</form>
<div class="modal  fade" id="sendpress-empty-list" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>
				<h3><?php _e('Really? Remove All Subscribers from this list.','sendpress');?></h3>
			</div>
			<div class="modal-body">
				<p><?php _e('This will remove all subscribers from the list','sendpress');?>.</p>
			</div>
			<div class="modal-footer">
			<a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('No! I was Joking','sendpress');?></a><a href="<?php echo SendPress_Admin::link('Subscribers_Subscribers') . $list ; ?>&action=remove-subscribers" id="confirm-delete" class="btn btn-danger" ><?php _e('Yes! Remove All Subscribers','sendpress');?></a>
			</div>
		</div>
	</div>
</div>
	<?php

	}

}