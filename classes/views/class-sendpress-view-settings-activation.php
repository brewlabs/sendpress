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
		$dpost = get_post($optin);
		$dpost->post_content = $_POST['body'];
		$dpost->post_title = $_POST['subject'];
			
		
		wp_update_post($dpost);

		
		SendPress_Admin::redirect('Settings_Activation');
	}
	
	function html($sp) {

		$optin = SendPress_Data::get_template_id_by_slug('double-optin');
		$dpost = get_post($optin);
		?>
		<form method="post" id="post">

		<div style="float:right;" >
			<a href=" " class="btn btn-large" ><i class="icon-remove"></i> <?php _e('Cancel','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><i class="icon-white icon-ok"></i> <?php _e('Save','sendpress'); ?></a>
		</div>
		<br class="clear">
		<br class="clear">
<div class="boxer form-box">
	<h2><?php _e('Public Page Settings', 'sendpress'); ?></h2>
	<br><b><?php _e('Use theme styles:', 'sendpress'); ?>&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('try-theme')=='yes'){ echo "checked='checked'"; } ?> name="try-theme"> <?php _e('Yes', 'sendpress'); ?>&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('try-theme')=='no'){ echo "checked='checked'"; } ?> name="try-theme"> <?php _e('No', 'sendpress');?></label></b>
	<br><?php _e('This will attempt to use the WordPress theme functions <code>get_header</code> and <code>get_footer</code> to build the SendPress default public pages.', 'sendpress'); ?>
	<br><br><b><?php _e('View the default pages below to see how they look.', 'sendpress'); ?></b>
	<ul class="nomargin">
		<li><a href="<?php echo site_url(); ?>?sendpress=confirm"><?php _e('Confirmation Page', 'sendpress'); ?></a></li>
		<li><a href="<?php echo site_url(); ?>?sendpress=manage"><?php _e('Manage Page', 'sendpress'); ?></a></li>
		<li><a href="<?php echo site_url(); ?>?sendpress=post"><?php _e('Post Page', 'sendpress'); ?></a></li>
	</ul>


</div>
	
		
<div class="boxer form-box">
	<h2><?php _e('Double Opt-in Email', 'sendpress'); ?></h2>
	<div style="float: right; width: 45%;"><br>
		<b><?php _e('Subject', 'sendpress'); ?></b><br>
		<input type="text" name="subject" class="regular-text sp-text" style="width: 100%;" value="<?php echo  stripcslashes($dpost->post_title); ?>"/>
		<br><br><b><?php _e('Body', 'sendpress'); ?></b><br>
		<?php if(strpos($dpost->post_content,'*|SP:CONFIRMLINK|*'  )  == false){ echo "<div class='alert alert-error'>" . __('Missing <b>*|SP:CONFIRMLINK|*</b> in body content.', 'sendpress') . "</div>";} ?>
		<textarea style="width:100%; padding: 8px;" rows="21" name="body"><?php 
		remove_filter( 'the_content', 'wpautop' );
		global $post;
		$post = $dpost;
		$content = apply_filters('the_content', $dpost->post_content);
									$content = str_replace(']]>', ']]&gt;', $content);
									echo stripcslashes($content); ?></textarea>

	</div>	
	<div style="width: 45%; margin-right: 10%">
		<br><b><?php _e('Send Double Opt-in Email:', 'sendpress'); ?>&nbsp;&nbsp;&nbsp;<input type="radio" value="yes" <?php if(SendPress_Option::get('send_optin_email')=='yes'){ echo "checked='checked'"; } ?> name="optin"> <?php _e('Yes', 'sendpress'); ?>&nbsp;&nbsp;&nbsp;<input type="radio" value="no" <?php if(SendPress_Option::get('send_optin_email')=='no'){ echo "checked='checked'"; } ?> name="optin"> <?php _e('No', 'sendpress'); ?></b>
			<br><?php _e('Keep the spammers, robots and other riff-raff off your list. <br>Read more about why to use double opt-in on out support site.', 'sendpress'); ?>
			<br><br><br>
			<b><?php _e('Confirmation Page', 'sendpress'); ?></b><br>
			<div class='well'>
			<?php _e('Select the page a new subcriber will be redirected to after they click the confirmation link in the Double Opt-in Email.', 'sendpress'); ?><br><br>
			<?php $ctype = SendPress_Option::get('confirm-page'); ?>
			<input type="radio" name="confirm-page" value="default" <?php if($ctype=='default'){echo "checked='checked'"; } ?> /> <?php _e('Use Default SendPress Page', 'sendpress'); ?><br><br>
			<input type="radio" name="confirm-page" value="custom"  <?php if($ctype=='custom'){echo "checked='checked'";} ?>/> <?php _e('Redirect to', 'sendpress'); ?> <select name="confirm-page-id"> 
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
</select>
</div>


			<br>
			<?php _e('<b>Quick Tags</b>: work in both subject and body.' ,'sendpress'); ?>
			<div class='well'>
				<b>*|SP:CONFIRMLINK|*</b> - <?php _e('This is required to be in the body of the email.' ,'sendpress'); ?>
				<hr>
				*|SITE:TITLE|* - <?php _e('Add Website title to the email.' ,'sendpress'); ?>
				<hr>
				*|EMAIL|* - <?php _e('Add the email the user signed up with.' ,'sendpress'); ?>
			</div>
		<!--
		<p><input type="text" class="regular-text" style="width: 100%;"/></p>
		-->
		<br class="clear">
		<br class="clear">

	</div>
</div>
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

