<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Listform extends SendPress_View_Subscribers {
	
	function html($sp) {
		
	$list ='';
	if(isset($_GET['listID'])){
		$listinfo = get_post( $_GET['listID'] );
	}
	?>
		<div id="taskbar">
		<div id="button-area" >
			<a href="<?php echo SendPress_View_Subscribers::link(); ?>" class="btn btn-large" ><i class="icon-backward"></i> <?php _e('Back to Lists','sendpress'); ?></a> <a href="#" id="save-update" class="btn btn-primary btn-large"><?php _e('Save','sendpress'); ?></a>
		</div>
		<h2><?php _e('Form Settings for List ','sendpress'); echo ':' . $listinfo->post_title; ?></h2>
		</div>
		<br class="clear">
	<form id="list-edit" method="post">


<div class="boxer form-box">
	<h2>iFrame</h2>
	<div style="float: right; width: 45%;"><br>
		<b>HTML</b><br>
		<textarea style="width:100%; padding: 8px;" rows="21" name="post-page-text"><iframe  width="100%" scrolling="no" frameborder="0"  src="<?php echo site_url(); ?>?sendpress=form&list=<?php echo $_GET['listID']; ?>" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 130px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription SendPress" ></iframe></textarea>
	</div>	
<div style="width: 45%; margin-right: 10%"><br>
	<!--
	<iframe width="100%" scrolling="no" frameborder="0" src="http://joshlmbprd.whipplehill.com/wp/?wysija-page=1&controller=subscribers&action=wysija_outter&widgetnumber=4&external_site=1&wysijap=subscriptions" name="wysija-1358371025" class="iframe-wysija" id="wysija-4" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 330px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription Wysija"></iframe>
	-->
	<iframe  width="100%" scrolling="no" frameborder="0"  src="<?php echo site_url(); ?>?sendpress=form&list=<?php echo $_GET['listID']; ?>" vspace="0" tabindex="0" style="position: static; top: 0pt; margin: 0px; border-style: none; height: 130px; left: 0pt; visibility: visible;" marginwidth="0" marginheight="0" hspace="0" allowtransparency="true" title="Subscription SendPress" ></iframe>
</div>
	<br class="clear">
</div>



		<?php wp_nonce_field( SendPress_Data::nonce() ); ?>
	</form>
	<?php
	}

}