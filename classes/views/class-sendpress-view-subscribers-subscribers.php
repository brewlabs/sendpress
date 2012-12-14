<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Subscribers extends SendPress_View_Subscribers {
	
	function html($sp) {
	
		$list ='';
if(isset($_GET['listID'])){
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
	    <input type="hidden" name="listID" value="<?php echo $_GET['listID']; ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($sp->_nonce_value); ?>
	</form>

	<?php

	}

}