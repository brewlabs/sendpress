<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Footerpage extends SendPress_View_Emails {
	
	function save(){
        //$this->security_check();
		    $saveid = SPNL()->validate->_int( 'templateID' );
        if( $saveid > 0 ){
        update_post_meta( $saveid, '_footer_page', SPNL()->validate->_html('footer-content') );
        SendPress_Admin::redirect('Emails_Footerpage',array('templateID' => $saveid));
        }
    }
   
   function html() { 
    global $sendpress_html_templates;

    $list = SPNL()->validate->_int( 'templateID' );
    $postdata = get_post( $list );


        //print_r( $postdata );
    ?>
    <form method="post" name="post" >
    <input type="hidden" value="<?php echo $list;  ?>" name="templateID" />
     
   	<div class="pull-right">
     <a href="<?php echo SendPress_Admin::link('Emails_Tempstyle', array('templateID' => $list  ) ); ?>">Back to Template</a>&nbsp;&nbsp;&nbsp;<button class="btn btn-primary " type="submit" value="save" name="submit"><i class="icon-white icon-ok"></i> <?php echo __('Save','sendpress'); ?></button>
   	</div>
   <h2><?php echo $postdata->post_title; ?> <?php _e('Template Page Footer','sendpress'); ?></h2><br>
     <div class="tab-pane fade in active" id="home"><?php wp_editor( get_post_meta( $postdata->ID , '_footer_page' , true) , 'footer-content'); ?></div>
     <br><br>
     <?php _e('Default Content','sendpress'); ?>
<textarea class="form-control" readonly rows="3">
<?php 
$sys = false;
get_post_meta( $postdata->ID , '_system_template' , true);
if(  true == get_post_meta( $postdata->ID , '_system_template' , true) ){
  $sys = true;
}
echo SendPress_Tag_Footer_Page::content($sys); ?>
</textarea>
     
		<?php SendPress_Data::nonce_field(); ?>
     </form>

<?php

$this->popup();
}

}

SendPress_Admin::add_cap('Emails_Tempstyle','sendpress_email');