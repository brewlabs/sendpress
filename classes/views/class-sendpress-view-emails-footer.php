<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Footer extends SendPress_View_Emails {
	
	function save(){
		$saveid = $_POST['templateID'];
      
        update_post_meta( $saveid, '_footer_content', $_POST['footer-content'] );
       
        }
   
   function html($sp) { 
    SendPress_Template_Manager::update_template_content();
    global $sendpress_html_templates;

        //print_r($sendpress_html_templates[$_GET['templateID']]);

    $postdata = get_post( $_GET['templateID'] );


        //print_r( $postdata );
    ?>
    <form method="post" name="post" >
    <input type="hidden" value="<?php echo $_GET['templateID'];  ?>" name="templateID" />
     
   	<div class="pull-right">
     <a href="<?php echo SendPress_Admin::link('Emails_Tempstyle', array('templateID' => $_GET['templateID']  ) ); ?>">Back to Template</a>&nbsp;&nbsp;&nbsp;<button class="btn btn-primary " type="submit" value="save" name="submit"><i class="icon-white icon-ok"></i> <?php echo __('Save','sendpress'); ?></button>
   	</div>
   <h2><?php echo $postdata->post_title; ?> Template Footer</h2><br>
     <div class="tab-pane fade in active" id="home"><?php the_editor( get_post_meta( $postdata->ID , '_footer_content' , true) , 'footer-content'); ?></div>

		<?php SendPress_Data::nonce_field(); ?>
     </form>

<br><br>
<?php
echo spnl_get_emails_tags_list();
?>
<?php

}

}

SendPress_Admin::add_cap('Emails_Tempstyle','sendpress_email');