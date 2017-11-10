<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Tempstyle extends SendPress_View_Emails {
	
	function save(){
        //$this->security_check();
		$saveid = SPNL()->validate->_int('post_ID');
     
         update_post_meta($saveid ,'_header_page_text_color', SPNL()->validate->_hex('pagetxt-color-select') );
          update_post_meta($saveid ,'_header_page_link_color', SPNL()->validate->_hex('pagelink-color-select') );
        
        update_post_meta($saveid ,'_header_text_color', SPNL()->validate->_hex('htxt-color-select') );
        update_post_meta($saveid ,'_header_bg_color', SPNL()->validate->_hex('bg-color-select') );
        if(SPNL()->validate->_isset('padding-heading')){
            update_post_meta($saveid ,'_header_padding', SPNL()->validate->_string('padding-heading') );
        } else {
             update_post_meta($saveid ,'_header_padding', false );
        }
         
          update_post_meta($saveid ,'_body_color', SPNL()->validate->_hex('bg-page-color-select') );
          update_post_meta($saveid ,'_content_color', SPNL()->validate->_hex('content-color-select') );

        update_post_meta($saveid ,'_content_text_color', SPNL()->validate->_hex('content-text-color-select') );
         update_post_meta($saveid ,'_content_link_color', SPNL()->validate->_hex('content-link-color-select') );
          update_post_meta($saveid ,'_footer_link_color', SPNL()->validate->_hex('footer-link-color-select') );

        update_post_meta($saveid ,'_footer_text_color', SPNL()->validate->_hex('footer-txt-color-select') );
        update_post_meta($saveid ,'_footer_bg_color', SPNL()->validate->_hex('footer-bg-color-select') );
       
        if(SPNL()->validate->_isset('padding-footer')){
            update_post_meta($saveid ,'_footer_padding', SPNL()->validate->_string('padding-footer') );
        } else {
             update_post_meta($saveid ,'_footer_padding', false );
        }

        if(SPNL()->validate->_isset('_body_font')){
            update_post_meta($saveid ,'_body_font', SPNL()->validate->_string('_body_font') );
        } else {
             update_post_meta($saveid ,'_body_font', false );
        }

        update_post_meta($saveid ,'_body_font_size', SPNL()->validate->_int('_body_font_size') );
     


    }
    function admin_init(){
       wp_enqueue_style( 'wp-color-picker' );
       wp_enqueue_script( 'wp-color-picker' );
   }

   function html() { 
   
    global $sendpress_html_templates;


    $postdata = get_post( SPNL()->validate->_int('templateID'));


        //print_r( $postdata );
    ?>
    <h2><?php echo $postdata->post_title; ?></h2>
    <br><br>
    <form method="post">
    <div class="alert alert-danger visible-xs"><?php _e('Sorry the Styler does not support screens smaller then 768px.','sendpress'); ?></div>
    <div class="sp-row">
        <input type="hidden" id="post_ID" name="post_ID" value="<?php echo $postdata->ID; ?>" />


        <div class="sp-toolbar">
        <div  class="btn-toolbar">

            <div class="btn-group btn-group-justified" id="code-edit-buttons" >
             
            <a href="<?php echo SendPress_Admin::link('Emails_Temp'); ?>" id="cancel-update" class="btn btn-lg btn-default" data-toggle="tooltip" data-placement="top" title="Cancel"><span class="glyphicon glyphicon-remove"></span></a>
            <div class="btn-group">
                <button class="btn btn-default btn-lg " type="submit" value="save" name="submit"  data-toggle="tooltip" data-placement="top" title="Save"><span class="glyphicon glyphicon-floppy-disk"></span></button></div>
            </div>
               
        </div><br>
            <div class="panel-group" id="accordion">
              <div class="panel panel-color">
                <div class="panel-heading">
                  <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                     <?php _e('Page','sendpress'); ?>
                 </a>
             </h4>
         </div>
         <div id="collapseOne" class="panel-collapse collapse">
          <div class="panel-body">
            <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Headerpage', array('templateID' => SPNL()->validate->_int('templateID') )); ?>" class="btn"><?php _e('Edit Page Header HTML','sendpress'); ?></a>
            <br>
           
            <?php
            $bgcolor = get_post_meta( $postdata->ID ,'_body_color',true );
                if($bgcolor == false ){
                    $bgcolor = '#ebebeb';
                }?>
            <?php _e('Background','sendpress'); ?><br><input type="text" value="<?php echo $bgcolor; ?>" id="bg-page-color-select" name="bg-page-color-select" class="my-color-field" data-default-color="#ebebeb" data-template-style="background-color" data-template-target=".sp-body-bg" />
            <hr>
            <?php 
            $htext = get_post_meta( $postdata->ID ,'_header_page_text_color',true );
                if($htext == false ){
                    $htext = '#333';
                }?>
           
            <?php _e('Text','sendpress'); ?><br><input type="text" value="<?php echo $htext; ?>" id="pagetxt-color-select" name="pagetxt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".page-text-color" />
                 <hr> <?php 
            $linktextpage = get_post_meta( $postdata->ID ,'_header_page_link_color',true );
                if($linktextpage == false ){
                    $linktextpage = '#2469a0';
                }?>
           
            <?php _e('Link','sendpress'); ?><br><input type="text" value="<?php echo $linktextpage; ?>" id="pagelink-color-select" name="pagelink-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".page-text-color a" />
            
            <br><br>
            <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Footerpage', array('templateID' =>SPNL()->validate->_int('templateID') )); ?>" class="btn"><?php _e('Edit Page Footer HTML','sendpress'); ?></a>
            
         </div>
    </div>
</div>
<div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
         <?php _e('Content Header','sendpress'); ?>
      </a>
  </h4>
</div>
<div id="collapseTwo" class="panel-collapse collapse">
  <div class="panel-body">
    <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Header', array('templateID' => SPNL()->validate->_int('templateID') )); ?>" class="btn"><?php _e('Edit Header HTML','sendpress'); ?></a>
    <br>
    <?php 
    $bgtext = get_post_meta( $postdata->ID ,'_header_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#d1d1d1';
        }?>
    <?php _e('Background','sendpress'); ?><br><input type="text" value="<?php echo $bgtext; ?>" id="bg-color-select" name="bg-color-select" class="my-color-field" data-default-color="#d1d1d1" data-template-style="background-color" data-template-target=".sp-style-h-bg" />
    <?php 
    $htext = get_post_meta( $postdata->ID ,'_header_text_color',true );
        if($htext == false ){
            $htext = '#333';
        }?>
    <hr>
    <?php _e('Text','sendpress'); ?><br><input type="text" value="<?php echo $htext; ?>" id="htxt-color-select" name="htxt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".sp-style-h-bg" />
    <hr>
     <?php 
        $padheader = get_post_meta( $postdata->ID ,'_header_padding',true );
        if($padheader == 'pad-header' ){
            $padheader = 'checked';
        }
        ?>
     <input type="checkbox"  <?php echo $padheader; ?> name="padding-heading" value="pad-header" />  <?php _e('Include Padding','sendpress'); ?><br>

    <!--
    <hr>
    Subtitle<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
    <hr>
    -->
</div>
</div>
</div>
<div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
            Content
        </a>
    </h4>
</div>
<div id="collapseThree" class="panel-collapse collapse">
  <div class="panel-body">
  <?php
  $bgtext = get_post_meta( $postdata->ID ,'_content_color',true );
        if($bgtext == false ){
            $bgtext = '#ffffff';
        }?>
    <?php _e('Background','sendpress'); ?><br><input type="text" value="<?php echo $bgtext; ?>" id="content-color-select" name="content-color-select"  class="my-color-field" data-default-color="#ffffff" data-template-style="background-color" data-template-target=".sp-style-c-bg" />
    <hr>
    <!--
    Border<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" data-template-style="border-color" data-template-target="td.sp-style-c-bg"/>
    <hr>
    -->
    <!--
    Titles<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" data-template-style="color" data-template-target=".sp-style-c-bg .sp-header"/>
    <hr>
    -->
    <?php
  $contenttext = get_post_meta( $postdata->ID ,'_content_text_color',true );
        if($contenttext == false ){
            $contenttext = '#333333';
        }?>
    
    <?php _e('Text','sendpress'); ?><br><input type="text" value="<?php echo $contenttext; ?>" id="content-text-color-select" name="content-text-color-select" class="my-color-field" data-default-color="#333333" data-template-style="color" data-template-target=".sp-style-c-bg" />
      <?php
  $contentlink = get_post_meta( $postdata->ID ,'_content_link_color',true );
        if($contentlink == false ){
            $contentlink = '#2469a0';
        }?>
    <hr>
    <?php _e('Link','sendpress'); ?><br><input type="text" value="<?php echo $contentlink; ?>" id="content-link-color-select" name="content-link-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".sp-style-c-bg a" />
  
    <?php do_action('sendpress_view_template_page_settings', $postdata->ID); ?>
    <br><br>
    <!--
    <hr>
    Link<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" data-template-style="color" data-template-target=".sp-style-c-bg a" />
    -->
</div>
</div>
</div>
<div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
           <?php _e('Content Footer','sendpress'); ?>
        </a>
    </h4>
</div>
<div id="collapseFour" class="panel-collapse collapse">
  <div class="panel-body">
    <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Footer', array('templateID' => SPNL()->validate->_int('templateID') )); ?>" class="btn">Edit Footer HTML</a>
    <br>
     <?php 
    $bgtext = get_post_meta( $postdata->ID ,'_footer_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#e2e2e2';
        }?>
    <?php _e('Background','sendpress'); ?><br><input type="text" value="<?php echo $bgtext; ?>" id="footer-bg-color-select" name="footer-bg-color-select" class="my-color-field" data-default-color="#e2e2e2" data-template-style="background-color" data-template-target=".sp-style-f-bg" />
    <?php 
    $htext = get_post_meta( $postdata->ID ,'_footer_text_color',true );
        if($htext == false ){
            $htext = '#333';
        }?>
    <hr>
    <?php _e('Text','sendpress'); ?><br><input type="text" value="<?php echo $htext; ?>" id="footer-txt-color-select" name="footer-txt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".sp-style-f-bg" />
      <?php
  $footerlink = get_post_meta( $postdata->ID ,'_footer_link_color',true );
        if($footerlink == false ){
            $footerlink = '#2469a0';
        }?>
    <hr>
    <?php _e('Link','sendpress'); ?><br><input type="text" value="<?php echo $footerlink; ?>" id="footer-link-color-select" name="footer-link-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".sp-style-f-bg a" />
  
    <hr>
     <?php 
        $padfooter = get_post_meta( $postdata->ID ,'_footer_padding',true );
        if($padfooter == 'pad-footer' ){
            $padfooter = 'checked';
        }
        ?>
     <input type="checkbox"  <?php echo $padfooter; ?> name="padding-footer" value="pad-footer" />  <?php _e('Include Padding','sendpress'); ?><br>
</div>
</div>
</div>
</div>
<br><BR><BR>

<?php wp_nonce_field($this->_nonce_value); ?>
</form>

<?php
echo spnl_get_emails_tags_list();
?>
<hr>
<a class="btn btn-primary btn-large" target="_blank" href="http://docs.sendpress.com/article/58-setting-up-a-newsletter-template">Template Documentation Site</a>

<?php

$home_url = ''; 
 
if (force_ssl_admin()) { 
         $home_url = get_home_url(NULL, '', 'https'); 
} 
else { 
         $home_url = get_home_url(); 
}

?>

</div>
<div class="sp-screen">
    <div class="sp-screen-holder">
        <iframe id="iframe1" class="hidden-xs" width="100%" style="border: solid 1px #999; border-radius: 5px;" src="<?php echo $home_url; ?>?sendpress=render&spemail=<?php echo SPNL()->validate->_int('templateID'); ?>" ></iframe>
    </div>
</div>
<!--
<div class="sendpress-layout-selector">
        <p><input type="radio" name="genesis_layout[_genesis_layout]" class="default-layout" id="default-layout" value="" checked="checked"> <label class="default" for="default-layout">Default Layout set in <a href="http://wp.dev/wp-admin/admin.php?page=genesis">Theme Settings</a></label></p>

        <p><label class="box"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/cs.gif" alt="Content, Primary Sidebar"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="content-sidebar" value="content-sidebar"></label><label class="box"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/sc.gif" alt="Primary Sidebar, Content"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="sidebar-content" value="sidebar-content"></label><label class="box"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/css.gif" alt="Content, Primary Sidebar, Secondary Sidebar"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="content-sidebar-sidebar" value="content-sidebar-sidebar"></label><label class="box selected"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/ssc.gif" alt="Secondary Sidebar, Primary Sidebar, Content"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="sidebar-sidebar-content" value="sidebar-sidebar-content"></label><label class="box"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/scs.gif" alt="Secondary Sidebar, Content, Primary Sidebar"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="sidebar-content-sidebar" value="sidebar-content-sidebar"></label><label class="box"><img src="http://wp.dev/wp-content/themes/genesis/lib/admin/images/layouts/c.gif" alt="Full Width Content"><br> <input type="radio" name="genesis_layout[_genesis_layout]" id="full-width-content" value="full-width-content"></label></p>
    </div>
-->
</div>
<script>



    jQuery(document).ready(function($){


        var styler_options = {
            // you can declare a default color here,
            // or in the data-default-color attribute on the input
            defaultColor: false,
            // a callback to fire whenever the color changes to a valid color
            change: function(event, ui){
               // console.log( event );
               var $target =  $(event.target);
               var target_class = $target.data('template-target');
               var target_style = $target.data('template-style');
                //console.log($(event.target).data('template-target'));
            //console.log(  ui.color.toString()  );

            var iframe = $('#iframe1'),
            content = iframe.contents(),
            body = content.find(  target_class );
                   // styletag = content.find('head').append('<style>body{ background-color: #000; }</style>');
                    //.children('style');
                        body.css( target_style , ui.color.toString() );
                   
                  //  console.log(styletag);


              },
            // a callback to fire when the input is emptied or an invalid color
            clear: function() {},
            // hide the color picker controls on load
            hide: true,
            // show a group of common colors beneath the square
            // or, supply an array of colors to customize further
            palettes:  true
        };



        $('.my-color-field').wpColorPicker( styler_options );

        

        /*
        var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("template-content"), {
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: true,
            mode: "text/html",
            extraKeys: {"Tab": "autocomplete"}
          }); 
        $('#template-editor').submit(function(e){
            myCodeMirror.save();
            var txt = $('#template-content').val();
            var unsub = txt.indexOf('{unsubscribe-link}') != -1;
            if(unsub == false){
                    e.preventDefault();
                $('.alert').removeClass('hide').addClass('in');  
            }
            
        });

        $('#iframe1').hide();
        $("#code-edit").click(function(e){
            reset_buttons();
            $(this).addClass('active');
            e.preventDefault();
            $('.CodeMirror').show();
            $('#iframe1').hide();
        });


        $("#code-preview").click(function(e){
            e.preventDefault();
            reset_buttons();
            $(this).addClass('active');
            $('.CodeMirror').hide();
            $('#iframe1').show();
            myCodeMirror.save();
            $('#iframe1').contents().find('html').html( $('#template-content').val() );
        });

        $("#code-preview-live").click(function(e){
            reset_buttons();
            e.preventDefault();
            $(this).addClass('active');
            $('.CodeMirror').hide();
            $('#iframe1').show();
            myCodeMirror.save();
            $('#iframe1').contents().find('html').html( $('#template-content').val() );
        });

        function reset_buttons(){
            $("#code-edit-buttons button").removeClass('active');
        }
        */
        
        $(window).resize(function(){
             var x = $( window ).height(); //jQuery('#wpbody').height();

        if(x - 350 < 700){
            $('#iframe1').height(700);
       } else {
            $('#iframe1').height(x - 350);
       }
   });

        var x = $( window ).height(); //jQuery('#wpbody').height();
        
       // $('.btn-group .btn').tooltip({container: 'body'});
        if(x - 350 < 700){
            $('#iframe1').height(700);
       } else {
            $('#iframe1').height(x - 350);
       }
       
        ///myCodeMirror.setSize('100%',(x - 350));
    });
</script>
<?php

}

}

SendPress_Admin::add_cap('Emails_Tempstyle','sendpress_email');
