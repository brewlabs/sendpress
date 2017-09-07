<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Testing extends SendPress_View_Emails {
	
	function save(){
        //$this->security_check();
		$saveid = SPNL()->validate->_int( 'templateID' );
        if( $saveid > 0 ){
            update_post_meta( $saveid, '_header_content', SPNL()->validate->_html('header-content') );
       
            SendPress_Admin::redirect('Emails_Header',array('templateID' => $saveid));
            }
        }
   
   function html() { 
    ?>
    <link rel="stylesheet" href="<?php echo SENDPRESS_URL;?>grape/css/grapes.min.css">
    <link rel="stylesheet" href="<?php echo SENDPRESS_URL;?>grape/css/grapesjs-preset-newsletter.css">

<div id="gjs"></div>

		<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>

<script src="<?php echo SENDPRESS_URL;?>grape/grapes.min.js"></script>

<script src="<?php echo SENDPRESS_URL;?>grape/grapesjs-preset-newsletter.min.js"></script>
<script type="text/javascript">
  var editor = grapesjs.init({
      container : '#gjs',
      plugins: ['gjs-preset-newsletter'],
      pluginsOpts: {
        'gjs-preset-newsletter': {
          modalTitleImport: 'Import template',
          // ... other options
        }
      }
  });
</script>


<?php

}

}

SendPress_Admin::add_cap('Emails_Tempstyle','sendpress_email');