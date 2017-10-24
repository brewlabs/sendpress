<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Sync extends SendPress_View_Subscribers {

	function save(){
		
	}
	
	function admin_init(){
		if( SPNL()->validate->_isset('finished') ){
	        SendPress_Admin::redirect('Subscribers');
	    }
	}

	function html() {

		$list = SPNL()->validate->_int('listID');
		if( $list > 0 ){
			$role_to_sync = get_post_meta( $list,'sync_role',true);
			//SendPress_Data::drop_active_subscribers_for_sync( $list );
				SendPress_Data::update_subscribers_for_sync( $list );
			if( $role_to_sync == 'meta' ){
				$meta_key = get_post_meta( $list,'meta-key',true);
				$meta_value = get_post_meta( $list,'meta-value',true);
				$meta_compare = get_post_meta( $list,'meta-compare',true);
				// WP_User_Query arguments
				$args = array (
					'meta_query'     => array(
						array(
							'key'       => $meta_key,
							'value'     => $meta_value,
							'compare'   => $meta_compare,
						),
					),
				);
				$user_query = new WP_User_Query( $args );

				$blogusers = $user_query->get_total();
				

				
			} else {

				$result = count_users();
				foreach($result['avail_roles'] as $role => $count){
					if($role == $role_to_sync){
						$blogusers = $count;
					}
				}
			}
		}
		//$blogusers = get_users( 'role=' . $role );
		//echo count($blogusers);
    	?>
<div id="taskbar" class="lists-dashboard rounded group"> 


</div>
<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $list; ?>" />
<div class='well' id="sync-wordpress-roles">
<h2><strong><?php _e('Syncing ','sendpress'); ?> <?php echo ucwords($role); ?> <?php _e(' Role to List','sendpress'); ?>  <?php echo get_the_title($list); ?> </strong></h2>
<br>

<div class="progress progress-striped active">
	<div class="progress-bar sp-queueit" style="width: 0%;"></div>
</div>
<span id="queue-total">0</span> of <span id="list-total"><?php echo $blogusers; ?></span>
</div>
<?php





	}
 
}