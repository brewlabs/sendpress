<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Settings_Activation') ){

class SendPress_View_Settings_Activation extends SendPress_View_Settings {
	private $error_body = false;

	function save() {
		SendPress_Option::set('send_optin_email', $_POST['optin']);

		SendPress_Option::set('optin_subject', $_POST['subject']);
		SendPress_Option::set('confirm-page', $_POST['confirm-page']);
		SendPress_Option::set('confirm-page-id',$_POST['confirm-page-id']);
		SendPress_Option::set('try-theme', $_POST['try-theme']);



		$optin = SendPress_Data::get_template_id_by_slug('double-optin');
		/*
		$dpost = get_post($optin);
		$dpost->post_content = $_POST['body'];
		$dpost->post_title = $_POST['subject'];
		*/	
		 $my_post = array(
	      'ID'           => $optin,
	      'post_content' => $_POST['body'],
	      'post_title' => $_POST['subject']
	  	);

		wp_update_post($my_post);

		
		SendPress_Admin::redirect('Settings_Activation');
	}
	
	function html($sp) {

		$optin = SendPress_Data::get_template_id_by_slug('double-optin');
		$dpost = get_post($optin);
		?>
		<form method="post" id="post">
<!--
<div style="float:right;" >
	<a href=" " class="btn btn-large btn-default" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
</div>
-->
		<br class="clear">
		<br class="clear">
		<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Public Page Settings</h3>
  </div>
  <div class="panel-body">
<div class="boxer form-box">
	

	<br><b>Use theme styles:&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('try-theme')=='yes'){ echo "checked='checked'"; } ?> name="try-theme"> Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('try-theme')=='no'){ echo "checked='checked'"; } ?> name="try-theme"> No</b>
	<br>This will attempt to use the WordPress theme functions <code>get_header</code> and <code>get_footer</code> to build the SendPress default public pages.
	<br><hr>
	<div class="sp-row">
			<div class="sp-33 sp-first"><b>Manage Page</b><br>
				<div class='well'>
					This is the page subscribers are directed to to manage their subscriptions to your lists.
					<br><br>
					<a class="btn btn-default" href="<?php echo site_url(); ?>?sendpress=manage">View Page</a>
				</div>

			</div>

			<div class="sp-33"><b>Confirmation Page</b><br>
			<div class='well'>
			Select the page a new subcriber will be redirected to after they click the confirmation link in the Double Opt-in Email.<br><br>
			<?php $ctype = SendPress_Option::get('confirm-page'); ?>
			<input type="radio" name="confirm-page" value="default" <?php if($ctype=='default'){echo "checked='checked'"; } ?> /> Use Default SendPress Page<br><br>
			<input type="radio" name="confirm-page" value="custom"  <?php if($ctype=='custom'){echo "checked='checked'";} ?>/> Redirect to <select name="confirm-page-id"> 
 <option value="">
 	<?php $cpageid = SendPress_Option::get('confirm-page-id');?>
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
<a class="btn btn-default" href="<?php echo site_url(); ?>?sendpress=confirm">View Page</a>
</div></div>

			<div class="sp-33"><b>Post Page</b><br>
				<div class='well'>
					This is the page shown by default if you are using a custom form to post subscriber data. This can also be set per list on each lists form page.
					<br><br>
					<a class="btn btn-default" href="<?php echo site_url(); ?>?sendpress=post">View Page</a>
				</div>

			</div>

	</div>

</div>
	
	</div></div>

	<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Double Opt-in Email</h3>
  </div>
  <div class="panel-body">	
<div class="boxer form-box">
	
	<div style="float: right; width: 45%;"><br>
		<b>Subject</b><br>
		<input type="text" name="subject" class="regular-text sp-text" style="width: 100%;" value="<?php echo  stripcslashes($dpost->post_title); ?>"/>
		<br><br><b>Body</b><br>
		<?php if(strpos($dpost->post_content,'*|SP:CONFIRMLINK|*'  )  == false){ echo "<div class='alert alert-error'>Missing <b>*|SP:CONFIRMLINK|*</b> in body content.</div>";} ?>
		<textarea style="width:100%; padding: 8px;" rows="21" name="body"><?php 
		remove_filter( 'the_content', 'wpautop' );
		global $post;
		$post = $dpost;
		$content = apply_filters('the_content', $dpost->post_content);
									$content = str_replace(']]>', ']]&gt;', $content);
									echo stripcslashes($content); ?></textarea>

	</div>	
	<div style="width: 45%; margin-right: 10%">
		<br><b>Send Double Opt-in Email:&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('send_optin_email')=='yes'){ echo "checked='checked'"; } ?> name="optin"> Yes&nbsp;&nbsp;&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('send_optin_email')=='no'){ echo "checked='checked'"; } ?> name="optin"> No</b>
			<br>Keep the spammers, robots and other riff-raff off your list. <br>Read more about why to use double opt-in on out support site.
			<br><br><br>
			


			<br>
			<b>Quick Tags</b>: work in both subject and body.
			<div class='well'>
				<b>*|SP:CONFIRMLINK|*</b> This is required to be in the body of the email.
				<hr>
				*|SITE:TITLE|* - Add Website title to the email.
				<hr>
				*|EMAIL|* - Add the email the user signed up with.
			</div>
		<!--
		<p><input type="text" class="regular-text" style="width: 100%;"/></p>
		-->
		<br class="clear">
		<br class="clear">

	</div>
</div></div></div>
<!--
<div class="boxer form-box">
	<h2>General Form Post Settings</h2>
	<div style="float: right; width: 45%;"><br>
		<b>Page Text</b><br>
		<textarea style="width:100%; padding: 8px;" rows="21" name="post-page-text"></textarea>

	</div>	
<div style="width: 45%; margin-right: 10%"><br>
	<div class='well'>
<?php $ctype = SendPress_Option::get('post-page'); ?>
<input type="radio" name="post-page" value="default" <?php if($ctype=='default'){echo "checked='checked'"; } ?> /> Use Default SendPress Page<br><br>
			<input type="radio" name="post-page" value="custom"  <?php if($ctype=='custom'){echo "checked='checked'";} ?>/> Redirect to <select name="post-page-id"> 
 <option value="">
 	<?php $cpageid = SendPress_Option::get('post-page-id');?>
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
</select>

</div>
</div>
	<br class="clear">
</div>
-->

<?php wp_nonce_field($sp->_nonce_value); ?>
</form>
		<?php
	}

}

} //End Class Check

