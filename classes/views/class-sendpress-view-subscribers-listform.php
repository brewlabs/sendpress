<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listform extends SendPress_View_Subscribers {

	function save(){

		if(isset($_POST['listID'])){
			$list_id = $_POST['listID'];
			$postpage = $_POST['post-page'];
			$postpageid = $_POST['post-page-id'];
			$postredirect = $_POST['post-redirect'];
			update_post_meta($list_id,'post-page',$postpage  );
			update_post_meta($list_id,'post-page-id',$postpageid  );
			update_post_meta($list_id,'post-redirect',$postredirect  );
		} else {
			$list_id = $_GET['listID'];
		}
		SendPress_Admin::redirect('Subscribers_Listform',array('listID'=> $list_id));
	}

	
	function html($sp) {
		
	$list ='';
	if(isset($_GET['listID'])){
		$list_id = $_GET['listID'];
		$listinfo = get_post( $_GET['listID'] );
	}

	$backLink = apply_filters('sendpress_back_to_lists_link', 'Subscribers');
	?>	
		<div id="taskbar">
		<div id="button-area" >
			<a href="<?php echo SendPress_Admin::link($backLink); ?>" class="btn btn-large btn-default" ><i class="icon-backward"></i> <?php _e('Back to Lists','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><?php _e('Save','sendpress'); ?></a>
		</div>
		<h2><?php _e('Form Settings for List ','sendpress'); echo ':' . $listinfo->post_title; ?></h2>
		</div>
		<br class="clear">


<form id="post" method="post">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">HTML/Remote Post</h3>
  </div>
  <div class="panel-body">
<div class="boxer form-box">
	


	<div style="float: right; width: 45%;"><br>
		<b>HTML</b><br>
		<textarea style="width:100%; padding: 8px;" rows="21" name="post-page-text">
&lt;form method="post" action="<?php echo trailingslashit(site_url()); ?>">
	&lt;input type="hidden" name="sp_list" value="<?php echo $list_id;?>"/>
	&lt;input type="hidden" name="sendpress" value="post" />
	<div id="form-wrap">
		<p name="email">
			<label for="email">EMail:</label>
			&lt;input type="text" value="" name="sp_email"/>
		</p>
		<p name="firstname">
			<label for="email">First Name:</label>
			&lt;input type="text" value="" name="sp_firstname"/>
		</p>
		<p name="lastname">
			<label for="email">Last Name:</label>
			&lt;input type="text" value="" name="sp_lastname"/>
		</p>
		<p class="submit">
			&lt;input value="Submit" class="sendpress-submit" type="submit" id="submit" name="submit">
		</p>
	</div>
&lt;/form>
		</textarea>
	</div>	
<div style="width: 45%; margin-right: 10%"><br>
	Post URL
	<div class='well'>
		<input type="hidden" name="listID" value="<?php echo $list_id ;?>" />
	<input type="text" readonly value="<?php echo trailingslashit(site_url()); ?>" class="sp-text"/>
</div>
	Response Options
	<div class='well'>
<?php $ctype = get_post_meta($list_id, 'post-page', true);
	if($ctype == false){
		$ctype = 'default';
	}

 ?>
<input type="radio" name="post-page" value="default" <?php if($ctype=='default'){echo "checked='checked'"; } ?> /> Show Default SendPress Page<br><br>
			<input type="radio" name="post-page" value="custom"  <?php if($ctype=='custom'){echo "checked='checked'";} ?>/> Redirect to <select name="post-page-id"> 
 <option value="">
 	<?php $cpageid = get_post_meta($list_id,'post-page-id', true); ?>
<?php echo esc_attr( __( 'Select page' ) ); ?></option> 
 <?php 
  $pages = get_pages(); 
  foreach ( $pages as $page ) {
  	$s ='';
  	if($cpageid == $page->ID){ $s =  "selected"; }
  	$option = '<option value="' . $page->ID .'" ' .$s. '>';
	$option .= $page->post_title;
	$option .= '</option>';
	echo $option;
  }
 ?>
</select><br><br>
<input type="radio" name="post-page" value="json" <?php if($ctype=='json'){echo "checked='checked'"; } ?> /> Return Json Data ie: { success: true/false, list: listid , name: listname, optin: true/false }<br><br>
<?php $link = get_post_meta($list_id,'post-redirect', true); ?>
<input type="radio" name="post-page" value="redirect" <?php if($ctype=='redirect'){echo "checked='checked'"; } ?> /> Redirect to url entered below. <br><br><input type="text" name="post-redirect" class="sp-text" value="<?php echo $link ?>"><br><br>
	</div>
</div>
</div>
</div>
	<br class="clear">
</div>	
<br class="clear">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">iFrame</h3>
  </div>
  <div class="panel-body">
<div class="boxer form-box">
	
	<div style="float: right; width: 45%;"><br>
		<b>HTML</b><br>
		<textarea style="width:100%; padding: 8px;" rows="21" name="post-page-text"><iframe  width="100%" scrolling="no" frameborder="0"  src="<?php echo site_url(); ?>?sendpress=form&list=<?php echo $_GET['listID']; ?>" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 130px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription SendPress" ></iframe></textarea>
	</div>	
<div style="width: 45%; margin-right: 10%"><br>
	<!--
	<iframe width="100%" scrolling="no" frameborder="0" src="http://joshlmbprd.whipplehill.com/wp/?wysija-page=1&controller=subscribers&action=wysija_outter&widgetnumber=4&external_site=1&wysijap=subscriptions" name="wysija-1358371025" class="iframe-wysija" id="wysija-4" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 330px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription Wysija"></iframe>
	-->
	<iframe  width="100%" scrolling="no" frameborder="0" src="<?php echo site_url(); ?>?sendpress=form&amp;list=<?php echo $_GET['listID']; ?>" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 130px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription SendPress" ></iframe>
</div>
	<br class="clear">
</div>
</div>
</div>


	



		<?php wp_nonce_field( SendPress_Data::nonce() ); ?>
	</form>
	<?php
	}

}