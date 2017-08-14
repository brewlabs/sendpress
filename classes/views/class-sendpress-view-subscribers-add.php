<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Add extends SendPress_View_Subscribers {
	
	function create_subscriber(){
		//$this->security_check();
		$email = SPNL()->validate->_email('email');
        $fname = SPNL()->validate->_string('firstname');
        $lname = SPNL()->validate->_string('lastname');
        $phonenumber = SPNL()->validate->_string('phonenumber');
        $salutation = SPNL()->validate->_string('salutation');
        $listID = SPNL()->validate->_int('listID');
        $status = SPNL()->validate->_string('status');

        if( is_email($email) ){

            $result = SendPress_Data::add_subscriber( array('firstname'=> $fname ,'email'=> $email,'lastname'=>$lname, 'phonenumber'=>$phonenumber, 'salutation'=>$salutation) );

            SendPress_Data::update_subscriber_status($listID, $result, $status ,false);

        }

		SendPress_Admin::redirect( 'Subscribers_Subscribers' , array( 'listID' => $listID ) );

	}


    function create_subscribers(){
        //$this->security_check();
        $csvadd = "email,firstname,lastname\n" . trim( SPNL()->validate->_string('csv-add') );
        $listID = SPNL()->validate->_int('listID');
        if($listID > 0 ){
        $newsubscribers = SendPress_Data::subscriber_csv_post_to_array( $csvadd );

        foreach( $newsubscribers as $subscriberx){
            if( is_email( trim( $subscriberx['email'] ) ) ){
          
            $result = SendPress_Data::add_subscriber( array('firstname'=> trim($subscriberx['firstname']) ,'email'=> trim($subscriberx['email']),'lastname'=> trim($subscriberx['lastname']), 'phonenumber' => trim($subscriberx['phonenumber']), 'salutation' => trim($subscriberx['salutation']) ) );


            	SendPress_Data::update_subscriber_status($listID, $result, 2, false);
            }
        }
    	
    	}
        wp_redirect( esc_url_raw(admin_url( 'admin.php?page='.SPNL()->validate->page(). "&view=subscribers&listID=".$listID )));
        
    }



	function html() { ?>
	<div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Add Subscriber','sendpress'); ?></h2>
	</div>
<div class="boxer">
	<div class="boxer-inner">
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="subscriber-create" method="post">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="action" value="create-subscriber" />
	    <input type="hidden" name="listID" value="<?php echo SPNL()->validate->_int('listID'); ?>" />
	    <span class="sublabel"><?php _e('Email','sendpress') ?>:</span><input type="text" name="email" value="" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Salutation','sendpress'); ?>:</span><input type="text" name="salutation" value="" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Firstname','sendpress'); ?>:</span><input type="text" name="firstname" value="" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Lastname','sendpress'); ?>:</span><input type="text" name="lastname" value="" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Phone Number','sendpress'); ?>:</span><input type="text" name="phonenumber" value="" class="regular-text sp-text" /><br>
	    <span class="sublabel"><?php _e('Status','sendpress'); ?>:</span><select name="status">
	    			<?php 
	    				$results =  SendPress_Data::get_statuses();
	    				foreach($results as $status){
	    					$selected = '';
	    					if($status->status == 'Active'){
	    						$selected = 'selected';
	    					}
	    					echo "<option value='$status->statusid' $selected>$status->status</option>";

	    				}


	    			?>

	    		</select>
	    		<br>
	  <button type="submit" class="btn btn-primary"><?php _e('Submit','sendpress'); ?></button>
	   <?php SendPress_Data::nonce_field(); ?>

	</form>
	</div>
</div>
	
	<h2><?php _e('Add Subscribers','sendpress'); ?></h2>
<div class="boxer">
	<div class="boxer-inner">	

		<div class="subscribers-create-container">

			<form id="subscribers-create" method="post">
					<!-- For plugins, we also need to ensure that the form posts back to our current page -->
				    <input type="hidden" name="action" value="create-subscribers" />
				    <input type="hidden" name="listID" value="<?php echo SPNL()->validate->_int( 'listID' ); ?>" />
				   	<textarea name="csv-add"></textarea>
				   	<button type="submit" class="btn btn-primary"><?php _e('Submit','sendpress'); ?></button>
				   	<?php SendPress_Data::nonce_field(); ?>
			</form>

			<div style="width: 25%; padding: 15px;" class="rounded box float-right">
				<?php _e('Emails shoud be written in separate lines. A line could also include a name, which is separated from the email by a comma','sendpress'); ?>.<br><br>
				<strong><?php _e('Correct formats','sendpress'); ?>:</strong><br>
				john@sendpress.com<br>
				john@sendpress.com, John<br>
				john@sendpress.com, John, Doe<br>
			</div>
		</div>
</div>
</div>
<?php
	
	}

}