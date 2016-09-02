<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Edit extends SendPress_View_Emails {
	
	

	function save_email(){
		//$this->security_check();

		$post_id =	SPNL()->validate->_int('post_ID');
		if($post_id > 0){


			

			$html = SPNL()->validate->_html('content_area_one_edit');
			//SendPress_Error::Log($html);
		 	$post_update = array(
		 		'ID'           => $post_id,
		      	'post_content' => $html
		    );
		   
			update_post_meta( $post_id, '_sendpress_template', SPNL()->validate->_int('template') );
			update_post_meta( $post_id, '_sendpress_subject', sanitize_text_field(SPNL()->validate->_string('post_subject' )) );
			if( SPNL()->validate->_isset('header_content_edit')){
				update_post_meta( $post_id, '_header_content', SPNL()->validate->_html('header_content_edit') );
			} 
			if( SPNL()->validate->_isset('footer_content_edit')){
				update_post_meta( $post_id, '_footer_content', SPNL()->validate->_html('footer_content_edit') );
			}

		 	//	print_r($template);
			remove_filter('content_save_pre', 'wp_filter_post_kses');
			remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');
			wp_update_post( $post_update );
			add_filter('content_save_pre', 'wp_filter_post_kses');
			add_filter('content_filtered_save_pre', 'wp_filter_post_kses');
		
		}
	
        if( SPNL()->validate->_string('submit') == 'save-next'){
            SendPress_Admin::redirect('Emails_Send', array('emailID'=> SPNL()->validate->_int('emailID') ) );
        } else if (SPNL()->validate->_string('submit') == 'send-test'){
            $email = new stdClass;
            $email->emailID  = SPNL()->validate->_int('post_ID');
            $email->subscriberID = 0;
            $email->listID = 0;
            $email->to_email = SPNL()->validate->_email('test-email');
            $d =SendPress_Manager::send_test_email( $email );
            //print_r($d);
           	SendPress_Admin::redirect('Emails_Edit', array('emailID'=>SPNL()->validate->_int('emailID') ));
        } else {
            SendPress_Admin::redirect('Emails_Edit', array('emailID'=>SPNL()->validate->_int('emailID') ));
        }
        

	}

	function admin_init(){
		global $is_IE;
		remove_filter('the_editor',					'qtrans_modifyRichEditor');
		/*
		if (  ! wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
	}

	function html() {
		global $is_IE;
		global $post_ID, $post;
		/*
		if (  wp_is_mobile() &&
			 ! ( $is_IE && preg_match( '/MSIE [5678]/', $_SERVER['HTTP_USER_AGENT'] ) ) ) {

			wp_enqueue_script('editor-expand');
			$_wp_autoresize_on = true;
		}
		*/
		
		$emailID = SPNL()->validate->_int('emailID');
		if( $emailID > 0 ){
			$post = get_post( $emailID );
			$post_ID = $post->ID;
		}

	
        if($post->post_type !== 'sp_newsletters'){
            SendPress_Admin::redirect('Emails');
        }
        $template_id = get_post_meta( $post->ID , '_sendpress_template' , true);

		?>
     <form method="post" id="post" role="form">
        <input type="hidden" name="post_ID" id="post_ID" value="<?php echo $post->ID; ?>" />
        <input type="hidden" name="post_type" id="post_type" value="sp_newsletters" />
        <input type="hidden" name="action" id="action" value="save-email" />
       <div  >
       <div style="float:right;" class="btn-toolbar">
            <div id="sp-cancel-btn" class="btn-group">
               <?php if($post->post_status != 'sp-autoresponder'  ) { ?>
                <a href="?page=<?php echo SPNL()->validate->page(); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
            
            <?php 
            } else { ?>
     		<a href="<?php echo SendPress_Admin::link('Emails_Autoresponder'); ?>" id="cancel-update" class="btn btn-default"><?php echo __('Cancel','sendpress'); ?></a>&nbsp;
           
            <?php } ?>
            </div>
            <div class="btn-group">
            
             <button class="btn btn-default " type="submit" value="save" name="submit"><i class="icon-white icon-ok"></i> <?php echo __('Update','sendpress'); ?></button>
           
            <?php if( SendPress_Admin::access('Emails_Send')  && $post->post_status != 'sp-autoresponder' ) { ?>
            <button class="btn btn-primary " type="submit" value="save-next" name="submit"><i class="icon-envelope icon-white"></i> <?php echo __('Send','sendpress'); ?></button>
            <?php } ?>
            </div>
        </div>
	

</div>
        <h2><?php _e('Edit Email Content','sendpress'); ?></h2>
        <br>
        <?php $this->panel_start('<span class="glyphicon glyphicon-envelope"></span> '.  __('Subject','sendpress') ); ?>
        <input type="text" name="post_subject" size="30" tabindex="1" class="form-control" value="<?php echo esc_attr( htmlspecialchars( get_post_meta($post->ID,'_sendpress_subject',true ) )); ?>" id="email-subject" autocomplete="off" />
        <?php $this->panel_end(  ); ?>
        <div class="sp-row">
<div class="sp-75 sp-first">
<!-- Nav tabs -->
<?php $enable_edits = SendPress_Option::get('enable_email_template_edit');?>
<ul class="nav nav-tabs">
  <li class="active"><a href="#content-area-one-tab" data-toggle="tab"><?php _e('Main Content','sendpress'); ?></a></li>
  <?php if($enable_edits){
  	?>
  	<li><a href="#header-content" data-toggle="tab"><?php _e('Header','sendpress'); ?></a></li>
  	<li><a href="#footer-content" data-toggle="tab"><?php _e('Footer','sendpress'); ?></a></li>
  	<?php
  }

  ?>
 
  <!--
  <li><a href="#messages" data-toggle="tab">Messages</a></li>
  <li><a href="#settings" data-toggle="tab">Settings</a></li>
  -->
</ul>

<div class="tab-content" style="display:block;">
  <div class="tab-pane in active" id="content-area-one-tab">
  <?php wp_editor( $post->post_content, 'content_area_one_edit', array(
	'dfw' => true,
	'drag_drop_upload' => true,
	'tabfocus_elements' => 'insert-media-button-1,save-post',
	'editor_height' => 360,
	'tinymce' => array(
		'resize' => false,
		'wp_autoresize_on' => ( ! empty( $_wp_autoresize_on ) && get_user_setting( 'editor_expand', 'on' ) === 'on' ),
		'add_unload_trigger' => false,
	),
) ); ?><?php //wp_editor($post->post_content,'content_area_one_edit'); ?></div>

	<?php 
	if($enable_edits){
		?>
		<div class="tab-pane" id="header-content">
			<?php wp_editor(  get_post_meta( $post->ID , '_header_content' , true), 'header_content_edit', array(
		'dfw' => true,
		'drag_drop_upload' => true,
		'tabfocus_elements' => 'insert-media-button-1,save-post',
		'editor_height' => 360,
		'tinymce' => array(
			'resize' => false,
			'wp_autoresize_on' => ( ! empty( $_wp_autoresize_on ) && get_user_setting( 'editor_expand', 'on' ) === 'on' ),
			'add_unload_trigger' => false,
		),
		) ); ?>

		</div>
		<div class="tab-pane" id="footer-content">
			<?php wp_editor(  get_post_meta( $post->ID , '_footer_content' , true), 'footer_content_edit', array(
		'dfw' => true,
		'drag_drop_upload' => true,
		'tabfocus_elements' => 'insert-media-button-1,save-post',
		'editor_height' => 360,
		'tinymce' => array(
			'resize' => false,
			'wp_autoresize_on' => ( ! empty( $_wp_autoresize_on ) && get_user_setting( 'editor_expand', 'on' ) === 'on' ),
			'add_unload_trigger' => false,
		),
		) ); ?>

		</div>
		<?php
	}
	?>
   <!--
  <div class="tab-pane fade" id="messages"><?php wp_editor($post->post_content,'content-3'); ?></div>
  <div class="tab-pane fade" id="settings"><?php wp_editor($post->post_content,'content-4'); ?></div>
  -->
</div>

</div>
<div class="sp-25">
<br><br>

	<?php $this->panel_start( __('Template','sendpress') ); ?>
	<select name="template" class="form-control">
	<?php
			$args = array(
			'post_type' => 'sp_template' ,
			'post_status' => array('sp-standard'),
			'posts_per_page' => -1,
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
			echo  '<optgroup label="SendPress Templates">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$temp_id = $the_query->post->ID;
				$s = '';
				if($temp_id == $template_id){
					$s = 'selected';
				}
				echo '<option value="'.$temp_id .'" '.$s.'>' . get_the_title() . '</option>';
			}
			echo  '</optgroup>';
			
			} else {
				echo '<option value="0" ></option>';
			}

		$args = array(
			'post_type' => 'sp_template' ,
			'post_status' => array('sp-custom'),
			'posts_per_page' => -1,
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				echo  '<optgroup label="Custom Templates">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$temp_id = $the_query->post->ID;
				$s = '';
				if($temp_id == $template_id){
					$s = 'selected';
				}
				echo '<option value="'.$temp_id .'" '.$s.'>' . get_the_title() . '</option>';
			}
			echo  '</optgroup>';
			
		}
	?>
	
	</select>
	<?php $this->panel_end(  ); ?>
</div>
</div>
<div class="well clear">
            <h2><?php _e('Test This Email','sendpress'); ?></h2>
            <p><input type="text" name="test-email" value="" class="sp-text" placeholder="Email to send test to." /></p>
            <button class="btn btn-success" name="submit" type="submit" value="send-test"><i class=" icon-white icon-inbox"></i> <?php _e('Send Test','sendpress'); ?></button>
        </div>


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

  	<p><?php _e('Header HTML','sendpress'); ?>:&nbsp;
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
	<p><?php _e('Header Link','sendpress'); ?>:&nbsp;
	  	<label class="radio">
		  <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="link" checked>
		  <?php _e('Link Header to Post','sendpress'); ?>
		</label>
		<label class="radio">
		  <input type="radio" name="headerlinkOptions" id="optionsRadios2" value="nolink">
		  <?php _e('Don\'t Link Header to Post','sendpress'); ?>
		</label>
	</p>
  	<p><?php _e('Post Content','sendpress'); ?>:&nbsp;
	  	<label class="radio">
		  <input type="radio" name="optionsRadios" id="optionsRadios1" value="excerpt" checked>
		  <?php _e('Excerpt','sendpress'); ?>
		</label>
		<label class="radio">
		  <input type="radio" name="optionsRadios" id="optionsRadios2" value="full">
		  <?php _e('Full Post','sendpress'); ?>
		</label>
	</p>
  	<button class="btn btn-mini btn-success sp-insert-code" id="sp-post-preview-insert" data-code=""><?php _e('Insert','sendpress'); ?></button>
  </div>
 	<div class="tab-pane " id="merge">
 		<h3><?php _e('Subscriber specific content','sendpress'); ?></h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th><?php _e('Description','sendpress'); ?></th>
      <th><?php _e('Code','sendpress'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td><?php _e('First Name','sendpress'); ?></td>
      	<td>*|FNAME|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|FNAME|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    <tr>
    	<td><?php _e('Last Name','sendpress'); ?></td>
      	<td>*|LNAME|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|LNAME|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    <tr>
    	<td><?php _e('Email','sendpress'); ?></td>
      	<td>*|EMAIL|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|EMAIL|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>

  </tbody>
</table>
	<h3><?php _e('Site specific content','sendpress'); ?></h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th><?php _e('Description','sendpress'); ?></th>
      <th><?php _e('Code','sendpress'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td><?php _e('Website URL','sendpress'); ?></td>
      	<td>*|SITE:URL|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:URL|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    <tr>
    	<td><?php _e('Website Title','sendpress'); ?></td>
      	<td>*|SITE:TITLE|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|SITE:TITLE|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    <tr>
    	<td><?php _e('Website Description','sendpress'); ?></td>
      	<td>*|SITE:DECRIPTION|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code"  data-code="*|SITE:DESCRIPTION|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    
  </tbody>
</table>
<h3><?php _e('Date and Time','sendpress'); ?></h3>
  		<table class="table table-condensed table-striped">
  			
  <thead>
    <tr>
      <th><?php _e('Description','sendpress'); ?></th>
      <th><?php _e('Code','sendpress'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
    	<td><?php _e('Current Date','sendpress'); ?><br><small><?php _e('Format based on WordPress settings','sendpress'); ?>.</small></td>
      	<td>*|DATE|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
     <tr>
    	<td><?php _e('Current Time','sendpress'); ?><br><small>5:16 pm</small></td>
      	<td>*|DATE:g:i a|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:g:i a|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
    <tr>
    	<td><?php _e('Custom Date','sendpress'); ?><br><small>March 10, 2001, 5:16 pm</small></td>
      	<td>*|DATE:F j, Y, g:i a|*</td>
      	<td class="text-right"><button class="btn btn-xs btn-success sp-insert-code" data-code="*|DATE:F j, Y, g:i a|*"><?php _e('Insert','sendpress'); ?></button></td>
    </tr>
  
    
  </tbody>
</table>

  </div>
 
  <div class="tab-pane" id="messages">...</div>
  <div class="tab-pane" id="settings">...</div>
</div>
		
	</div>
	<div class="modal-footer">
	 	<a href="#" class="btn btn-primary" data-dismiss="modal"><?php _e('Close','sendpress'); ?></a>
	</div>
</div>
</div>
	<?php SendPress_Data::nonce_field(); ?>
        </form>
	<?php
	}

}