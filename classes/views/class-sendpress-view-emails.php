<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Emails') ){


class SendPress_View_Emails extends SendPress_View{

	function admin_init(){
		add_action('load-sendpress_page_sp-emails',array($this,'screen_options'));
	}

	function screen_options(){

		$screen = get_current_screen();

		$args = array(
			'label' => __('Emails per page', 'sendpress'),
			'default' => 10,
			'option' => 'sendpress_emails_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

 	function sub_menu(){

		?>
		<div class="navbar navbar-default" >
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
       <span class="sr-only"><?php _e('Toggle navigation','sendpress'); ?></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>

    </button>
   <a class="navbar-brand" href="#"><?php _e('Emails','sendpress'); ?></a>
	</div>
		 <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
        <?php $the_view = SPNL()->validate->_string('view'); ?>
					<li <?php if( $the_view == ''|| $the_view === 'style' || $the_view === 'create' || $the_view === 'send' || $the_view === 'send-confirm'  || $the_view === 'send-queue'  ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails'); ?>"><?php _e('Newsletters','sendpress'); ?></a>
				  	</li>
				  	<?php  if(  false == true) {  //if(SendPress_Option::get('prerelease_templates') == 'yes') { ?>
				 	
				  	<li <?php if($the_view === 'all'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Auto'); ?>"><?php _e('Autoresponders','sendpress'); ?></a>
				  	</li>
				  	  	<!--	-->
				  	<?php } ?>

				  	<li <?php if($the_view === 'temp' || $the_view === 'tempstyle' ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Temp'); ?>"><?php _e('Templates','sendpress'); ?></a>
				  	</li>
				  	<li <?php if($the_view === 'templates' || $the_view === 'tempedit' ){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Templates'); ?>"><?php _e('Custom Templates','sendpress'); ?></a>
				  	</li>
				  	<?php //} ?>
				  	<li <?php if($the_view === 'social'){ ?>class="active"<?php } ?> >
				    	<a href="<?php echo SendPress_Admin::link('Emails_Social'); ?>"><?php _e('Social Icons','sendpress'); ?></a>
				  	</li>
				  	<li <?php if(in_array($the_view, array('postnotifications')) ) { ?>class="active"<?php } ?> >
              <a href="<?php echo SendPress_Admin::link('Emails_Postnotifications'); ?>"><?php _e('Post Notifications','sendpress'); ?></a>
            </li>
				  	
            
            <?php if(SendPress_Option::get('beta')){ ?>
              <li <?php if( in_array($the_view, array('scheduledsending','createschedule')) ) { ?>class="active"<?php } ?> >
                <a href="<?php echo SendPress_Admin::link('Emails_Scheduledsending'); ?>"><?php _e('Scheduled Sending','sendpress'); ?></a>
              </li>

              <li <?php if( in_array($the_view, array('autoresponder','autoedit','createauto','createschedule')) ) { ?>class="active"<?php } ?> >
                <a href="<?php echo SendPress_Admin::link('Emails_Autoresponder'); ?>"><?php _e('Automation','sendpress'); ?></a>
              </li>
           
              
            <?php } ?>

            <li <?php if(strpos(SPNL()->_current_view,'systememail') !== false){ ?>class="active"<?php } ?> ><a <?php if(strpos(SPNL()->_current_view,'systememail') !== false){ ?>class="wp-ui-primary"<?php } ?>  href="<?php echo SendPress_Admin::link('Emails_Systememail'); ?>"><i class=" icon-bullhorn"></i> <?php _e('System Email','sendpress'); ?></a></li>
				</ul>
			</div>
		</div>

		<?php



	}

	function popup(){
		?>

<div class="modal fade bs-modal-lg" id="sendpress-helper" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <ul class="nav nav-tabs" id="myTab">
      <li class="active tabs-first"><a href="#posts"><?php _e('Single Post','sendpress'); ?></a></li>
        <li ><a href="#merge"><?php _e('Personalize','sendpress'); ?></a></li>
     
        <!--
      <li><a href="#messages">Messages</a></li>
      <li><a href="#settings">Settings</a></li>
      -->
    </ul>
  </div>
  <div class="modal-body">

 
<div class="tab-content">
   <div class="tab-pane active" id="posts">

    <div id="search-header"><?php _e('Search Posts','sendpress'); ?>: <input type="text" name="q" id="sp-single-query"></div>
    <div  id="sp-post-preview" class="well">
      <?php _e('No Post Selected','sendpress'); ?>
    </div>

    <p>Header HTML:&nbsp;
      <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios1" value="h1" >
      H1
    </label>
    <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios2" value="h2">
      H2
    </label>
    <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios2" value="h3" checked>
      H3
    </label>
    <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios2" value="h4">
      H4
    </label>
    <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios2" value="h5">
      H5
    </label>
    <label class="radio">
      <input type="radio" name="headerOptions" id="optionsRadios2" value="h6">
      H6
    </label>
  </p>
  <p>Header Link:&nbsp;
      <label class="radio">
      <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="link" checked>
      Link Header to Post
    </label>
    <label class="radio">
      <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="nolink">
      Don't Link Header to Post
    </label>
  </p>
    <p>Post Content:&nbsp;
      <label class="radio">
      <input type="radio" name="optionsRadios" id="optionsRadios1" value="excerpt" checked>
      Excerpt
    </label>
    <label class="radio">
      <input type="radio" name="optionsRadios" id="optionsRadios2" value="full">
      Full Post
    </label>
  </p>
    <button class="btn btn-mini btn-success sp-insert-code" id="sp-post-preview-insert" data-code="">Insert</button>
  </div>
  <div class="tab-pane " id="merge">
    <h3>Subscriber specific content</h3>
      <table class="table table-condensed table-striped">
        
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>First Name</td>
        <td>*|FNAME|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|FNAME|*">Insert</button></td>
    </tr>
    <tr>
      <td>Last Name</td>
        <td>*|LNAME|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|LNAME|*">Insert</button></td>
    </tr>
    <tr>
      <td>Email</td>
        <td>*|EMAIL|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|EMAIL|*">Insert</button></td>
    </tr>

  </tbody>
</table>
  <h3>Site specific content</h3>
      <table class="table table-condensed table-striped">
        
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Website URL</td>
        <td>*|SITE:URL|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:URL|*">Insert</button></td>
    </tr>
    <tr>
      <td>Website Title</td>
        <td>*|SITE:TITLE|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:TITLE|*">Insert</button></td>
    </tr>
    <tr>
      <td>Website Description</td>
        <td>*|SITE:DECRIPTION|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|SITE:DESCRIPTION|*">Insert</button></td>
    </tr>
    
  </tbody>
</table>
<h3>Date and Time</h3>
      <table class="table table-condensed table-striped">
        
  <thead>
    <tr>
      <th>Description</th>
      <th>Code</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Current Date<br><small>Format based on WordPress settings.</small></td>
        <td>*|DATE|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE|*">Insert</button></td>
    </tr>
     <tr>
      <td>Current Time<br><small>5:16 pm</small></td>
        <td>*|DATE:g:i a|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:g:i a|*">Insert</button></td>
    </tr>
    <tr>
      <td>Custom Date<br><small>March 10, 2001, 5:16 pm</small></td>
        <td>*|DATE:F j, Y, g:i a|*</td>
        <td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:F j, Y, g:i a|*">Insert</button></td>
    </tr>
  
    
  </tbody>
</table>

  </div>
 
  <div class="tab-pane" id="messages">...</div>
  <div class="tab-pane" id="settings">...</div>
</div>
    
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
  </div>
</div>
</div>
</div>
		<?php
	}


	function prerender(){



	}

	function html(){
		 SendPress_Tracking::event('Emails Tab');
	//Create an instance of our package class...
	$testListTable = new SendPress_Emails_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>

	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="email-filter" method="get">
		<div id="taskbar" class="lists-dashboard rounded group">

		<div id="button-area">
			<a class="btn btn-primary btn-large" href="?page=<?php echo SPNL()->validate->page(); ?>&view=create"><?php _e('Create Email','sendpress'); ?></a>
		</div>

	</div>
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
	    <input type="hidden" name="page" value="<?php echo SPNL()->validate->page(); ?>" />
	    <!-- Now we can render the completed list table -->
	    <?php $testListTable->display(); ?>
	    <?php wp_nonce_field($this->_nonce_value); ?>
	</form>
	<?php
	}

}



SendPress_Admin::add_cap('Emails','sendpress_email');

}
