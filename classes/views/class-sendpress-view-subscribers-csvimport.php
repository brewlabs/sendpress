<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Csvimport extends SendPress_View_Subscribers {
	
 
	function save(){
    //$this->security_check();
		$uploadfiles = $_FILES['uploadfiles'];
	if (is_array($uploadfiles)) {
  	foreach ($uploadfiles['name'] as $key => $value) {

      // look only for uploded files
      if ($uploadfiles['error'][$key] == 0) {

        $filetmp = $uploadfiles['tmp_name'][$key];

        //clean filename and extract extension
        $filename = $uploadfiles['name'][$key];

        // get file info
        // @fixme: wp checks the file extension....
        $filetype = wp_check_filetype( basename( $filename ), null );
        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
        $filename = $filetitle . '.' . $filetype['ext'];
        $upload_dir = wp_upload_dir();
        if( $filetype['ext'] != 'csv' ){
          SendPress_Admin::redirect('Subscribers_Csvimport',array('listID'=> SPNL()->validate->_int( 'listID' )));
        }

        /**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
         */
        $i = 0;
        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
          $i++;
        }
        $filedest = $upload_dir['path'] . '/' . $filename;

        $filedest = str_replace('\\','/', $filedest);
        /**
         * Check write permissions
         */
        if ( !is_writeable( $upload_dir['path'] ) ) {
          SendPress_Option::set('import_error', true);  
          //$this->_error = true;
          //$this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
          //return;
        }

        /**
         * Save temporary file to uploads dir
         */
        if ( !@move_uploaded_file($filetmp, $filedest) ){
          SendPress_Option::set('import_error', true);
          //$this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
          //continue;
        }

        update_post_meta(SPNL()->validate->_int( 'listID' ),'csv_import',$filedest);
        if(SendPress_Option::get('import_error', false) == false  ){
		      SendPress_Admin::redirect('Subscribers_Csvprep',array('listID'=> SPNL()->validate->_int( 'listID' )));
        }
        /*
        $attachment = array(
          'post_mime_type' => $filetype['type'],
          'post_title' => $filetitle,
          'post_content' => '',
          'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $filedest );
        require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        */
      }
    }
  }
	}

	function html() { ?>
  <?php 
  if( SendPress_Option::get('import_error', false) == true ) { ?>
	<div class="alert alert-danger">
  <?php _e('We had a problem saving your upload','sendpress'); ?>.
  </div>
  <?php } ?>
  <div id="taskbar" class="lists-dashboard rounded group"> 
	<h2><?php _e('Import CSV to ','sendpress'); ?><?php echo get_the_title(SPNL()->validate->_int( 'listID' )); ?></h2>
	</div>
<div class="boxer">
	<div class="boxer-inner">
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form method="post" enctype="multipart/form-data" accept-charset="utf-8" >
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="listID" value="<?php echo SPNL()->validate->_int( 'listID' ); ?>" />
	   	<table>
	   	<tr>
    <td class="left_label"> <?php
      //echo $label; ?>
    </td>
    <td>
        <input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" />
        <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Upload"  />
     
    </td>
  </tr> 
 	</table>
   
<?php 
SendPress_Option::set('import_error', false);
SendPress_Data::nonce_field(); ?>
	   </form>
</div>
</div>
<?php
	
	}

}