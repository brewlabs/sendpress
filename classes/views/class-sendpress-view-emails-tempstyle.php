<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Tempstyle extends SendPress_View_Emails {
	
	function save(){
		$saveid = $_POST['post_ID'];
        /*
        $bodybg = $_POST['body_bg'];
        $bodytext = $_POST['body_text'];
        $bodylink = $_POST['body_link'];
        $contentbg = $_POST['content_bg'];
        $contenttext = $_POST['content_text'];
        $contentlink = $_POST['sp_content_link_color'];
        $contentborder = $_POST['content_border'];
        $upload_image = $_POST['upload_image'];
        
        $headerbg = $_POST['header_bg'];
        $headertextcolor = $_POST['header_text_color'];
        $headertext = $_POST['header_text'];

        $headerlink = $_POST['header_link'];
        $imageheaderurl = $_POST['image_header_url'];
        $subheadertext = $_POST['sub_header_text'];

        $activeHeader = $_POST['active_header'];

        $_POST['post_type'] = 'sp_newsletters';
        // Update post 37
    */
        //$my_post = _wp_translate_postdata(true);
        /*            
        $my_post['ID'] = $_POST['post_ID'];
        $my_post['post_content'] = $_POST['content'];
        $my_post['post_title'] = $_POST['post_title'];
        */
       /*
        $str = $my_post['post_content'];
        $DOM = new DOMDocument;
        $DOM->loadHTML($str);
           //get all H1
        $aTags = $DOM->getElementsByTagName('a');

        foreach ($aTags as $aElement) {
            $style = $aElement->getAttribute('style');

                if($style == ""){
                    $aElement->setAttribute('style', 'color: '. $contentlink);
                }
        }

        $body_html = $DOM->saveHtml();
        $my_post['post_content']  = $body_html;
    */
        //$my_post['post_status'] = 'publish';
        // Update the post into the database
        //wp_update_post( $my_post );
       // update_post_meta( $my_post['ID'], '_sendpress_subject', $_POST['post_subject'] );
        //update_post_meta( $my_post['ID'], '_sendpress_template', $_POST['template'] );
        //update_post_meta( $my_post['ID'], '_sendpress_status', 'private');

        //SendPress_Email::set_default_style($my_post['ID']);
        //clear the cached file.
        //delete_transient( 'sendpress_email_html_'. $my_post['ID'] );
         update_post_meta($saveid ,'_header_page_text_color', $_POST['pagetxt-color-select'] );
          update_post_meta($saveid ,'_header_page_link_color', $_POST['pagelink-color-select'] );
        
        update_post_meta($saveid ,'_header_text_color', $_POST['htxt-color-select'] );
        update_post_meta($saveid ,'_header_bg_color', $_POST['bg-color-select'] );
        if(isset( $_POST['padding-heading'])){
            update_post_meta($saveid ,'_header_padding', $_POST['padding-heading'] );
        } else {
             update_post_meta($saveid ,'_header_padding', false );
        }
         
          update_post_meta($saveid ,'_body_color', $_POST['bg-page-color-select'] );
          update_post_meta($saveid ,'_content_color', $_POST['content-color-select'] );

        update_post_meta($saveid ,'_content_text_color', $_POST['content-text-color-select'] );
         update_post_meta($saveid ,'_content_link_color', $_POST['content-link-color-select'] );
          update_post_meta($saveid ,'_footer_link_color', $_POST['footer-link-color-select'] );

        update_post_meta($saveid ,'_footer_text_color', $_POST['footer-txt-color-select'] );
        update_post_meta($saveid ,'_footer_bg_color', $_POST['footer-bg-color-select'] );
       
        if(isset( $_POST['padding-footer'])){
            update_post_meta($saveid ,'_footer_padding', $_POST['padding-footer'] );
        } else {
             update_post_meta($saveid ,'_footer_padding', false );
        }
        /*padding-heading



        update_post_meta($saveid ,'body_bg', $bodybg);
        update_post_meta($saveid ,'body_text', $bodytext );
        update_post_meta($saveid ,'body_link', $bodylink );
        update_post_meta($saveid ,'content_bg', $contentbg );
        update_post_meta($saveid ,'content_text', $contenttext );
        update_post_meta($saveid ,'sp_content_link_color', $contentlink );
        update_post_meta($saveid ,'content_border', $contentborder );
        update_post_meta($saveid ,'upload_image', $upload_image );

        update_post_meta($saveid ,'footer_bg', $headerbg );
        update_post_meta($saveid ,'header_text_color', $headertextcolor );
        update_post_meta($saveid ,'header_text', $headertext );

        update_post_meta($saveid ,'header_link', $headerlink );
        update_post_meta($saveid ,'image_header_url', $imageheaderurl );
        update_post_meta($saveid ,'sub_header_text', $subheadertext );

        update_post_meta($saveid ,'active_header', $activeHeader );
        */
        /*
        if(isset($_POST['submit']) && $_POST['submit'] == 'save-next'){
            SendPress_Admin::redirect('Emails_Send', array('emailID'=>$_GET['emailID'] ));
        } else if (isset($_POST['submit']) && $_POST['submit'] == 'send-test'){
            $email = new stdClass;
            $email->emailID  = $my_post['ID'];
            $email->subscriberID = 0;
            $email->listID = 0;
            $email->to_email = $_POST['test-email'];
            $d =SendPress_Manager::send_test_email( $email );
            //print_r($d);
            SendPress_Admin::redirect('Emails_Tempstyle', array('templateID'=>$_GET['templateID'] ));
        } else {
            SendPress_Admin::redirect('Emails_Tempstyle', array('templateID'=>$_GET['templateID'] ));
        }
        */


    }
    function admin_init(){
       wp_enqueue_style( 'wp-color-picker' );
       wp_enqueue_script( 'wp-color-picker' );
   }

   function html($sp) { 
   
    global $sendpress_html_templates;

        //print_r($sendpress_html_templates[$_GET['templateID']]);

    $postdata = get_post( $_GET['templateID'] );


        //print_r( $postdata );
    ?>
    <h2><?php echo $postdata->post_title; ?></h2>
    <br><br>
    <form method="post">
    <div class="alert alert-danger visible-xs">Sorry the Styler does not support screens smaller then 768px.</div>
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
                     Page
                 </a>
             </h4>
         </div>
         <div id="collapseOne" class="panel-collapse collapse">
          <div class="panel-body">
            <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Headerpage', array('templateID' => $_GET['templateID'] )); ?>" class="btn">Edit Page Header HTML</a>
            <br>
           
            <?php
            $bgcolor = get_post_meta( $postdata->ID ,'_body_color',true );
                if($bgcolor == false ){
                    $bgcolor = '#ebebeb';
                }?>
            Background<br><input type="text" value="<?php echo $bgcolor; ?>" id="bg-page-color-select" name="bg-page-color-select" class="my-color-field" data-default-color="#ebebeb" data-template-style="background-color" data-template-target=".sp-body-bg" />
            <hr>
            <?php 
            $htext = get_post_meta( $postdata->ID ,'_header_page_text_color',true );
                if($htext == false ){
                    $htext = '#333';
                }?>
           
            Text<br><input type="text" value="<?php echo $htext; ?>" id="pagetxt-color-select" name="pagetxt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".page-text-color" />
                 <hr> <?php 
            $linktextpage = get_post_meta( $postdata->ID ,'_header_page_link_color',true );
                if($linktextpage == false ){
                    $linktextpage = '#2469a0';
                }?>
           
            Link<br><input type="text" value="<?php echo $linktextpage; ?>" id="pagelink-color-select" name="pagelink-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".page-text-color a" />
          

            <br><br>
            <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Footerpage', array('templateID' => $_GET['templateID'] )); ?>" class="btn">Edit Page Footer HTML</a>
            
         </div>
    </div>
</div>
<div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Content Header
      </a>
  </h4>
</div>
<div id="collapseTwo" class="panel-collapse collapse">
  <div class="panel-body">
    <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Header', array('templateID' => $_GET['templateID'] )); ?>" class="btn">Edit Header HTML</a>
    <br>
    <?php 
    $bgtext = get_post_meta( $postdata->ID ,'_header_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#d1d1d1';
        }?>
    Background<br><input type="text" value="<?php echo $bgtext; ?>" id="bg-color-select" name="bg-color-select" class="my-color-field" data-default-color="#d1d1d1" data-template-style="background-color" data-template-target=".sp-style-h-bg" />
    <?php 
    $htext = get_post_meta( $postdata->ID ,'_header_text_color',true );
        if($htext == false ){
            $htext = '#333';
        }?>
    <hr>
    Text<br><input type="text" value="<?php echo $htext; ?>" id="htxt-color-select" name="htxt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".sp-style-h-bg" />
    <hr>
     <?php 
        $padheader = get_post_meta( $postdata->ID ,'_header_padding',true );
        if($padheader == 'pad-header' ){
            $padheader = 'checked';
        }
        ?>
     <input type="checkbox"  <?php echo $padheader; ?> name="padding-heading" value="pad-header" />  Include Padding<br>

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
    Background<br><input type="text" value="<?php echo $bgtext; ?>" id="content-color-select" name="content-color-select"  class="my-color-field" data-default-color="#ffffff" data-template-style="background-color" data-template-target=".sp-style-c-bg" />
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
    
    Text<br><input type="text" value="<?php echo $contenttext; ?>" id="content-text-color-select" name="content-text-color-select" class="my-color-field" data-default-color="#333333" data-template-style="color" data-template-target=".sp-style-c-bg" />
      <?php
  $contentlink = get_post_meta( $postdata->ID ,'_content_link_color',true );
        if($contentlink == false ){
            $contentlink = '#2469a0';
        }?>
    <hr>
    Link<br><input type="text" value="<?php echo $contentlink; ?>" id="content-link-color-select" name="content-link-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".sp-style-c-bg a" />
  
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
           Content Footer
        </a>
    </h4>
</div>
<div id="collapseFour" class="panel-collapse collapse">
  <div class="panel-body">
    <a class="btn btn-default btn-block" href="<?php echo SendPress_Admin::link('Emails_Footer', array('templateID' => $_GET['templateID'] )); ?>" class="btn">Edit Footer HTML</a>
    <br>
     <?php 
    $bgtext = get_post_meta( $postdata->ID ,'_footer_bg_color',true );
        if($bgtext == false ){
            $bgtext = '#e2e2e2';
        }?>
    Background<br><input type="text" value="<?php echo $bgtext; ?>" id="footer-bg-color-select" name="footer-bg-color-select" class="my-color-field" data-default-color="#e2e2e2" data-template-style="background-color" data-template-target=".sp-style-f-bg" />
    <?php 
    $htext = get_post_meta( $postdata->ID ,'_footer_text_color',true );
        if($htext == false ){
            $htext = '#333';
        }?>
    <hr>
    Text<br><input type="text" value="<?php echo $htext; ?>" id="footer-txt-color-select" name="footer-txt-color-select" class="my-color-field" data-default-color="#333" data-template-style="color" data-template-target=".sp-style-f-bg" />
      <?php
  $footerlink = get_post_meta( $postdata->ID ,'_footer_link_color',true );
        if($footerlink == false ){
            $footerlink = '#2469a0';
        }?>
    <hr>
    Link<br><input type="text" value="<?php echo $footerlink; ?>" id="footer-link-color-select" name="footer-link-color-select" class="my-color-field" data-default-color="#2469a0" data-template-style="color" data-template-target=".sp-style-f-bg a" />
  
    <hr>
     <?php 
        $padfooter = get_post_meta( $postdata->ID ,'_footer_padding',true );
        if($padfooter == 'pad-footer' ){
            $padfooter = 'checked';
        }
        ?>
     <input type="checkbox"  <?php echo $padfooter; ?> name="padding-footer" value="pad-footer" />  Include Padding<br>
</div>
</div>
</div>
</div>
<br><BR><BR>
<?php wp_nonce_field($this->_nonce_value); ?>
</form>
<!--


<?php $this->panel_start( '<span class=" glyphicon glyphicon-tint"></span> '. __('Background Styles','sendpress') ); ?>

Body<br>

<br>
footer<br>
<input type="text" value="#bada55" id="bg-color-select" class="my-color-field2" data-default-color="#effeff" />

<br>
Footer<br>
<input type="text" value="#bada55" id="bg-color-select" class="my-color-field3" data-default-color="#effeff" />

<?php // $sp->create_color_picker( array('id'=>'body_bg','value'=>$body_bg['value'],'std'=>$body_bg['std'], 'link'=>'#html-view' ,'css'=>'background-color' ) ); ?>
<br><br>
Body Text Color<br>
<?php // $sp->create_color_picker( array('id'=>'body_text','value'=>$body_text['value'],'std'=>$body_text['std'], 'link'=>'.html-view-outer-text' ,'css'=>'color' ) ); ?>
<br><br>
Body Link Color<br>
<?php // $sp->create_color_picker( array('id'=>'body_link','value'=>$body_link['value'],'std'=>$body_link['std'], 'link'=>'.html-view-outer-text a' ,'css'=>'color' ) ); ?>

<?php $this->panel_end(); ?>
-->
<?php
echo spnl_get_emails_tags_list();
?>

</div>
<div class="sp-screen">
    <div class="sp-screen-holder">
        <iframe id="iframe1" class="hidden-xs" width="100%" style="border: solid 1px #999; border-radius: 5px;" src="<?php echo home_url(); ?>?sendpress=render&spemail=<?php echo $_GET['templateID']; ?>" ></iframe>
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