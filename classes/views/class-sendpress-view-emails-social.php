<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Emails_Social extends SendPress_View_Emails {
	
	function save(){
		//$this->security_check();
		$icon_list = SendPress_Data::social_icons();
		$links = array();
		foreach ($icon_list as $key => $value) {
			if( isset( $_POST["url-" . $key] )  && $_POST["url-" . $key] != "" ) {
				$links[$key] = $_POST["url-" . $key];
			}
		}
	
		SendPress_Option::set('socialicons', $links );
		SendPress_Option::set('socialsize', $_POST['icon-view'] );
        //SendPress_Admin::redirect('Settings_Advanced');
	}




	function html() { 
		$icon_list = SendPress_Data::social_icons();
		$socialsize = SendPress_Option::get('socialsize','large');
?>
<form method="post" id="post" role="form">
<div  >
	<div id="button-area">  
		<input type="submit" value="<?php _e('Save','sendpress'); ?>" class="btn btn-large btn-primary"/>
	</div>

</div>
<br><br><br>
<?php $this->panel_start(  ); ?>

<div class="sp-row">
<div class="sp-50 sp-first">
<p class="lead"><?php _e('Social Icons appear in emails in Alphabetical order. If you enter a url in the box below then that icon will show in your emails.','sendpress'); ?></p>
</div>
<div class="sp-50">
<p>
<label >
<input type="radio" name="icon-view" value="large" <?php checked( $socialsize, 'large' ); ?> /> <?php _e('Large','sendpress'); ?> ( 32px x 32px )
</label>
<br>
<label >
<input type="radio" name="icon-view" value="small" <?php checked( $socialsize, 'small' ); ?> /> <?php _e('Small','sendpress'); ?> ( 16px x 16px )
</label>
<br>
<label >
<input type="radio" name="icon-view" value="text" <?php checked( $socialsize, 'text' ); ?> /> <?php _e('Text','sendpress'); ?>
</label>
</p>
</div>
</div>

<div class="sp-row">
<div class="sp-50 sp-first">
<?php 
	$icons = count($icon_list);
	$link = SendPress_Option::get('socialicons');
	$firsthalf = array_slice($icon_list, 0, $icons / 2);
	$secondhalf = array_slice($icon_list, $icons / 2);
	foreach ($firsthalf as $key => $value) {
		$class = "";
		if(isset( $link[$key] )){
			$class =  "bg-success";
		}
		?>
	
   
   		
      
		<div class="well <?php echo $class; ?>">
			<div class="form-group">
			<?php echo "<span class='hidden-xs hidden-sm pull-right text-muted'>". $value . "</span>"; ?>
 		<label for="url-<?php echo $key; ?>" class="control-label ">
		<img src="<?php echo SENDPRESS_URL ."img/16px/". $key .".png" ;  ?>" />
		<img src="<?php echo SENDPRESS_URL ."img/32px/". $key .".png" ;  ?>" />
		<?php
		echo $key ."</label>"; 
		$xlink = "";
		if(isset( $link[$key] )){
			$xlink = $link[$key] ;
		}

		?>
		 
  </div><input type="text" name="url-<?php echo $key; ?>" value="<?php echo $xlink; ?>" class="form-control" placeholder="<?php echo $key; ?> URL: Please include http:// or https://" />
    </div>
		<?php
		
	}

?>
</div>
<div class="sp-50">
<?php 
foreach ($secondhalf as $key => $value) {
	$class = "";
		if(isset( $link[$key] )){
			$class =  "bg-success";
		}
		?>
	
   
   		
      
		<div class="well <?php echo $class; ?>">
			<div class="form-group">
			<?php echo "<span class='hidden-xs hidden-sm pull-right text-muted'>". $value . "</span>"; ?>
 		<label for="url-<?php echo $key; ?>" class="control-label ">
		<img src="<?php echo SENDPRESS_URL ."img/16px/". $key .".png" ;  ?>" />
		<img src="<?php echo SENDPRESS_URL ."img/32px/". $key .".png" ;  ?>" />
		<?php
		echo $key ."</label>"; 
		$xlink = "";
		if(isset( $link[$key] )){
			$xlink = $link[$key] ;
		}

		?>
		 
  </div><input type="text" name="url-<?php echo $key; ?>" value="<?php echo $xlink; ?>" class="form-control" placeholder="<?php echo $key; ?> URL: Please include http:// or https://" />
    </div>
		<?php
	}
?>
</div>
</div>

<?php $this->panel_end(); ?>
		

<?php wp_nonce_field($this->_nonce_value); ?>
</form>
<?php
	}

}


SendPress_Admin::add_cap('Emails_Social','sendpress_email');