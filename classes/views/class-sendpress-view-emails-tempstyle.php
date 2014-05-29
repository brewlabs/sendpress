<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Tempstyle extends SendPress_View_Emails {
	
	function save(){
		$saveid = $_POST['post_ID'];
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

        $my_post = _wp_translate_postdata(true);
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
        $my_post['post_status'] = 'publish';
        // Update the post into the database
        wp_update_post( $my_post );
        update_post_meta( $my_post['ID'], '_sendpress_subject', $_POST['post_subject'] );
        update_post_meta( $my_post['ID'], '_sendpress_template', $_POST['template'] );
        update_post_meta( $my_post['ID'], '_sendpress_status', 'private');

        SendPress_Email::set_default_style($my_post['ID']);
        //clear the cached file.
        delete_transient( 'sendpress_email_html_'. $my_post['ID'] );

        update_post_meta($saveid ,'body_bg', $bodybg);
        update_post_meta($saveid ,'body_text', $bodytext );
        update_post_meta($saveid ,'body_link', $bodylink );
        update_post_meta($saveid ,'content_bg', $contentbg );
        update_post_meta($saveid ,'content_text', $contenttext );
        update_post_meta($saveid ,'sp_content_link_color', $contentlink );
        update_post_meta($saveid ,'content_border', $contentborder );
        update_post_meta($saveid ,'upload_image', $upload_image );

        update_post_meta($saveid ,'header_bg', $headerbg );
        update_post_meta($saveid ,'header_text_color', $headertextcolor );
        update_post_meta($saveid ,'header_text', $headertext );

        update_post_meta($saveid ,'header_link', $headerlink );
        update_post_meta($saveid ,'image_header_url', $imageheaderurl );
        update_post_meta($saveid ,'sub_header_text', $subheadertext );

        update_post_meta($saveid ,'active_header', $activeHeader );
        
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
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>$_GET['emailID'] ));
        } else {
            SendPress_Admin::redirect('Emails_Style', array('emailID'=>$_GET['emailID'] ));
        }


       
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
<div class="alert alert-danger visible-xs">Sorry the Styler does not support screens smaller then 768px.</div>
<div class="sp-row">


<div class="sp-toolbar">
<div class="panel-group" id="accordion">
  <div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
         Page
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
        Background<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Preheader<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
      </div>
    </div>
  </div>
  <div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Header
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
        Background<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Title<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Subtitle<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
       </div>
    </div>
  </div>
  <div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
            Body
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
        Background<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Border<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Title<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Text<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Link<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
       </div>
    </div>
  </div>
  <div class="panel panel-color">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
            Footer
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
        Background<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Text<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
        <hr>
        Link<br><input type="text" value="#bada55" id="bg-color-select" class="my-color-field" data-default-color="#effeff" />
      </div>
    </div>
  </div>
</div>
<br><BR><BR>

<!--


<?php $this->panel_start( '<span class=" glyphicon glyphicon-tint"></span> '. __('Background Styles','sendpress') ); ?>

Body<br>

<br>
Content<br>
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
</div>
<script>

        

    jQuery(document).ready(function($){
        var myOptions2 = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){
       // console.log( event );


    //console.log(  ui.color.toString()  );

     var iframe = $('#iframe1'),
            content = iframe.contents(),
            body = content.find('.bodyContent');
           // styletag = content.find('head').append('<style>body{ background-color: #000; }</style>');
            //.children('style');

            body.css( 'background-color' , ui.color.toString()  );

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

        
            var myOptions = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){
       // console.log( event );


    //console.log(  ui.color.toString()  );

     var iframe = $('#iframe1'),
            content = iframe.contents(),
            body = content.find('body');
           // styletag = content.find('head').append('<style>body{ background-color: #000; }</style>');
            //.children('style');

            body.css( 'background-color' , ui.color.toString()  );

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

           var myOptions3 = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){
       // console.log( event );


    //console.log(  ui.color.toString()  );

     var iframe = $('#iframe1'),
            content = iframe.contents(),
            body = content.find('.footerContent');
           // styletag = content.find('head').append('<style>body{ background-color: #000; }</style>');
            //.children('style');

            body.css( 'background-color' , ui.color.toString()  );

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
        $('.my-color-field3').wpColorPicker(myOptions3);
        $('.my-color-field2').wpColorPicker(myOptions2);
        $('.my-color-field').wpColorPicker(myOptions);
        $('#bg-color-select').on('change',function(e){
          
/*
var iframe = $('iframe1'),
content = iframe.contents(),
body = content.find('body'),
styletag = content.find('head').append('<style></style>').children('style');

styletag.text( 'background-color:' + $(this).val() +';' );

*/
        });
        

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
        
       // $('.btn-group .btn').tooltip({container: 'body'});
        $('#iframe1').height((x - 350));
        });

        var x = $( window ).height(); //jQuery('#wpbody').height();
        
       // $('.btn-group .btn').tooltip({container: 'body'});
        $('#iframe1').height((x - 350));
        ///myCodeMirror.setSize('100%',(x - 350));
    });
    </script>
    <?php

	}

}

SendPress_Admin::add_cap('Emails_Tempstyle','sendpress_email');