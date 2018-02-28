<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_All extends SendPress_View_Subscribers {
	
	function admin_init(){
		add_action('load-sendpress_page_sp-subscribers',array($this,'screen_options'));
	}

	function export_all(){
		//$this->security_check();
		$items = SendPress_Data::export_subscirbers();

		$customfields = SendPress_Data::export_customfields();
        $cf_headers = "";
        $cf = SPNL()->load('Customfields')->get_all();
        $default_cf_output = '';

        foreach ($cf as $key => $f) {
        	$cf_headers .= ','.$f['slug'];
        	$default_cf_output .= ',';
        }

        header("Content-type:text/octect-stream");
        header("Content-Disposition:attachment;filename=SendPressAll.csv");
        print "email,firstname,lastname".$cf_headers." \n";
        foreach($items as $user) {
        	//build string for custom fields
        	$cf_output = $default_cf_output;

        	if(array_key_exists($user->subscriberID, $customfields)){
        		$a = $customfields[$user->subscriberID];
        		$cf_output = "";

        		foreach ($cf as $key => $field) {
        			if(array_key_exists($field['slug'], $a)){
        				$cf_output .= ','.$a[$field['slug']];
        			}else{
        				$cf_output .= ',';
        			}
        		}
        	}

            print  $user->email . ",". $user->firstname.",". $user->lastname.$cf_output."\n" ;
        }
        exit;
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
	

	function remove_subscribers( ){
		//$this->security_check();
		SendPress_Data::delete_all_subscribers( );
		SendPress_Admin::redirect('Subscribers_All' );
	}

	function delete_subscribers_bulk_all(){
		//$this->security_check();
		$dt =  SPNL()->validate->_int_array('subscriber');
		if( is_array($dt) ) {
			foreach ($dt as $value) {
				SendPress_Data::delete_subscriber( $value );
			}
		}
		SendPress_Admin::redirect('Subscribers_All' );
	}


	function html() {
	
		$list ='';
		$listID = SPNL()->validate->int('listID');
	if($listID > 0 ){
		
		$listinfo = get_post($listID);
		$list = '&listID='.$listID;
		$listname = 'for '. $listinfo->post_title;
	}
	//Create an instance of our package class...
	$testListTable = new SendPress_Subscribers_All_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
		<div id="button-area">  
			
			<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=add<?php echo $list; ?>"><?php _e('Add Subscriber','sendpress'); ?></a>
		</div>
		<h2><?php _e('Subscribers','sendpress'); ?></h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="movies-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page() ?>" />
	    <?php if($listID > 0 ){ ?>
	    <input type="hidden" name="listID" value="<?php echo $listID; ?>" />
	    <?php  } ?>
	    <input type="hidden" name="view" value="<?php echo esc_html(SPNL()->validate->_string('view')); ?>" />

	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display() ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>

	<form  method='get'>
		<input type='hidden' value="<?php echo SPNL()->validate->page(); ?>" name="page" />
		<br>
		<input type='hidden' value="unlink-lisk" name="action" />
		<div class="btn-group">
		<a class="btn btn-danger " data-toggle="modal" href="#sendpress-empty-list" ><i class="icon-warning-sign "></i> <?php _e('Remove All Subscribers','sendpress'); ?></a>
		<a class="btn btn-primary " data-toggle="modal" href="<?php echo SendPress_Admin::link('Subscribers_All'); ?>&action=export_all" ><i class="icon-warning-sign "></i> <?php _e('Export All Subscribers','sendpress'); ?></a>
	</div>
		<?php wp_nonce_field($this->_nonce_value); ?>
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
	<a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('No! I was Joking','sendpress');?></a><a href="<?php echo SendPress_Admin::link('Subscribers_All'); ?>&action=remove-subscribers" id="confirm-delete" class="btn btn-danger" ><?php _e('Yes! Remove All Subscribers','sendpress');?></a>
	</div>
</div>
</div>
</div>
	<?php

	}

}