<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Bulk extends SendPress_View_Subscribers {

	function save(){
			$list = SPNL()->validate->_int('listID');
		$start = SPNL()->validate->_int('list-start');
		$end = SPNL()->validate->_int('list-end');
		
		if( $list > 0  && $start > 0 && $end > 0 ){
				
			SendPress_Data::update_subscriber_list_status_bulk($list,  $end, $start);
			//die(start);
			SendPress_Admin::redirect('Subscribers_Subscribers', array('listID' => $list  ) );
				
		}

	}
	
	function admin_init(){
		if( SPNL()->validate->_isset('finished') ){
	        
	    }
	}

	function html() {

	

    	?>
<br>
<form method="post" id="post">
<h2><strong><?php _e('Bulk ','sendpress'); ?><?php _e(' change status for list ','sendpress'); ?>  <?php echo get_the_title($list); ?> </strong></h2>
<br>
	<?php 
		_e('Change status ','sendpress');
		$this->status_select('list-start');
		//$blogusers = get_users( 'role=' . $role );
		//echo count($blogusers);
		_e(' to ','sendpress');
		$this->status_select('list-end');
		?>
<br><br>
 <input type="hidden" name="listID" value="<?php echo SPNL()->validate->_int( 'listID' ); ?>" />
  <input type="hidden" name="view" value="<?php echo esc_html(SPNL()->validate->_string('view')); ?>" />
	<?php wp_nonce_field($this->_nonce_value); ?>
	<p><small>Warning. This function has no rollback ability.</small></p>
<input class="button-primary" type="submit" name="save" id="save" value="Update Status"  />
</form>
<!--
<div class='well' id="sync-wordpress-roles">

<div class="progress progress-striped active">
	<div class="progress-bar sp-queueit" style="width: 0%;"></div>
</div>
<span id="queue-total">0</span> of <span id="list-total"><?php echo $blogusers; ?></span>
</div>
-->
<?php





	}

	function status_select( $id ){
		//$this->security_check();
        $info = SendPress_Data::get_statuses();
        echo '<select name="'. $id .'">';
        echo "<option cls value='-1' >No Status</option>";
        foreach ($info as $list) {
            $cls = '';
            if($status == $list->statusid){
                $cls = " selected='selected' ";
            }

           echo "<option $cls value='".$list->statusid."'>".$list->status."</option>";
        }

        
        echo '</select> ';
    }
 
}