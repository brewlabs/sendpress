<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Help extends SendPress_View{
	function prerender($sp){
		wp_enqueue_script( 'dashboard' );
		sp_add_help_widget( 'help_support', 'Support Information', array(&$this,'help_support'));
		sp_add_help_widget( 'help_knowledge', 'Recent Knowledge Base Articles', array(&$this,'help_knowledge'),'side' );
		sp_add_help_widget( 'help_debug', 'Debug Information', array(&$this,'help_debug'), 'side');
		
		sp_add_help_widget( 'help_blog', 'Recent Blog Posts', array(&$this,'help_blog'),'normal',  array(&$this,'help_blog_control') );
		sp_add_help_widget( 'help_shortcodes', 'Shortcode Cheat Sheet', array(&$this,'help_shortcodes') ,'normal');
		sp_add_help_widget( 'help_editemail', 'Customizing Emails', array(&$this,'help_editemail') ,'normal');


	}

	function help_editemail(){
?>
		<b>SendPress Editor Button</b>
		<p>Look for this button <img src="<?php echo SENDPRESS_URL; ?>/js/icon.png" /> to open the popup for adding posts to an email and personilizing emails.</p>

		

<?php
	}

	function help_shortcodes(){
?>
		<b>Signup Shortcode</b>
		<p>If you would rather add the SendPress signup form to a page, you can use the following short code.  If you want more detailed information on how to use the short code check out our <a href="http://sendpress.com/support/knowledgebase/how-to-use-the-sign-up-shortcode/" target="_blank">knowledge base</a>.</p>
		<pre>[sendpress-signup listids='1']</pre>
		

<?php
	}

	function help_support(){
?>
	<b>Basic Support</b>
	<p>You can get support for the FREE version of SendPress on the <a href="http://wordpress.org/support/plugin/sendpress" target="_blank">WordPress.org forums</a>.<br>Also check our <a href="http://sendpress.com/support" target="_blank">Knowledge Base</a> for help at <a href="http://sendpress.com/support" target="_blank">http://sendpress.com/support</a></p>
	<br>
	<b>Premium Support</b>
	<p>Premium support is available if you have purchased SendPress Pro from SendPress.com. Premium support can be accessed via our support site: <a href="http://sendpress.com/support" target="_blank">http://sendpress.com/support</a> and requires a SendPress.com account.</p>

<?php
	}

	function help_debug(){
		global $wp_version,$wpdb;;
		echo "<b>WordPress Version</b>: ". $wp_version."<br>";
		echo "<b>SendPress Version</b>: ".SENDPRESS_VERSION ."<br>";
		echo '<b>PHP Version</b>: ' . phpversion(). '<br>';
		
		$mem = (int) ini_get('memory_limit') ;	
		$used =  function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0;
		if(!empty( $mem ) && !empty( $used ) ){
			$prec = round ( $used / $mem * 100, 0);
		}
		echo '<b>PHP Memory Limit</b>: '. $mem . __(' MByte') . '<br>';
		echo '<b>PHP Memory Used</b>: '. $used . __(' MByte') . '<br>';
		
		echo '<b>MySQL Version</b>: ' . $wpdb->db_version() . '<br><br>';
		echo "<b>Ports:</b><br>";
	  	$server  = "smtp.sendgrid.net";
	  	$port   = "25";
	  	$port2   = "465";
	  	$port3   = "587";
	  	$timeout = "1";

	  if ($server and $port and $timeout) {
	    $port25 =  @fsockopen("$server", $port, $errno, $errstr, $timeout);
	    $port465 =  @fsockopen("$server", $port2, $errno, $errstr, $timeout);
	    $port587 =  @fsockopen("$server", $port3, $errno, $errstr, $timeout);
	  }	
	  echo "Port 25: ";
	  if(!$port25){
	  	 _e('blocked','sendpress');
	  } else {
	  	_e('open','sendpress');
	  }
	   echo "<br>Port 465: ";
	  if(!$port465){
	  	 _e('blocked','sendpress');
	  } else {
	  	_e('open','sendpress');
	  }
	   echo "<br>Port 587: ";
	  if(!$port587){
	  	 _e('blocked','sendpress');
	  } else {
	  	_e('open','sendpress');
	  }
	  echo "<br><br><b>Active Plugins:</b><br>";
	  $plugins = get_option ( 'active_plugins' );
    foreach ( $plugins as $plugin ) {
    	$p = get_plugin_data(WP_PLUGIN_DIR."/". $plugin );
        echo $p['Name'] ." - ".$p['Version']. "<br>";
    }

	}

	function help_blog(){


		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed('http://sendpress.com/feed');
		if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(5); 

		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items(0, $maxitems); 
		endif;
		?>

		<ul>
		    <?php if ($maxitems == 0) echo '<li>No items.</li>';
		    else
		    // Loop through each feed item and display each item as a hyperlink.
		    foreach ( $rss_items as $item ) : ?>
		    <li>
		        <a href='<?php echo esc_url( $item->get_permalink() ); ?>'
		        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
		        <?php echo esc_html( $item->get_title() ); ?></a>
		    </li>
		    <?php endforeach; ?>
		</ul><?php
	}


	function help_knowledge(){
				
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed('http://sendpress.com/support/feed/?post_type=knowledgebase');
		if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly 
		    // Figure out how many total items there are, but limit it to 5. 
		    $maxitems = $rss->get_item_quantity(5); 

		    // Build an array of all the items, starting with element 0 (first element).
		    $rss_items = $rss->get_items(0, $maxitems); 
		endif;
		?>

		<ul>
		    <?php if ($maxitems == 0) echo '<li>No items.</li>';
		    else
		    // Loop through each feed item and display each item as a hyperlink.
		    foreach ( $rss_items as $item ) : ?>
		    <li>
		        <a href='<?php echo esc_url( $item->get_permalink() ); ?>'
		        title='<?php echo 'Posted '.$item->get_date('j F Y | g:i a'); ?>'>
		        <?php echo esc_html( $item->get_title() ); ?></a>
		    </li>
		    <?php endforeach; ?>
		</ul><?php
	}
	function help_knowledge_control(){
		echo "Add Some settings";
	}


	function html($sp){
		
		global $wp_version;
$screen = get_current_screen();

	$class = 'columns-2';//. get_current_screen()->get_columns();

?>
<div id="dashboard-widgets" class="metabox-holder <?php echo $class; ?>">
	<div id='postbox-container-1' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
	</div>
	<div id='postbox-container-2' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
	</div>
	<div id='postbox-container-3' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
	</div>
	<div id='postbox-container-4' class='postbox-container'>
	<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
	</div>
</div>

<?php
	wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
	wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
				
	}

}
// Add Access Controll!
SendPress_View_Help::cap('sendpress_view');
//SendPress_View_Overview::cap('sendpress_access');


function _sp_help_control_callback( $dashboard, $meta_box ) {
	echo '<form action="" method="post" class="dashboard-widget-control-form">';
	wp_dashboard_trigger_widget_control( $meta_box['id'] );
	echo '<input type="hidden" name="widget_id" value="' . esc_attr($meta_box['id']) . '" />';
	submit_button( __('Submit') );
	echo '</form>';
}

function sp_add_help_widget( $widget_id, $widget_name, $callback, $location =null, $control_callback = null ) {
	$screen = get_current_screen();
	global $wp_dashboard_control_callbacks;

	if ( $control_callback && current_user_can( 'edit_dashboard' ) && is_callable( $control_callback ) ) {
		$wp_dashboard_control_callbacks[$widget_id] = $control_callback;
		if ( isset( $_GET['edit'] ) && $widget_id == $_GET['edit'] ) {
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			$callback = '_sp_help_control_callback';
		} else {
			list($url) = explode( '#', add_query_arg( 'edit', $widget_id ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( "$url#$widget_id" ) . '" class="edit-box open-box">' . __( 'Configure' ) . '</a></span>';
		}
	}

	if ( is_blog_admin () )
		$side_widgets = array('dashboard_quick_press', 'dashboard_recent_drafts', 'dashboard_primary', 'dashboard_secondary');
	else if (is_network_admin() )
		$side_widgets = array('dashboard_primary', 'dashboard_secondary');
	else
		$side_widgets = array();
	if( $location == null)
		$location = 'normal';

	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';

	$priority = 'core';
	if ( 'dashboard_browser_nag' === $widget_id )
		$priority = 'high';

	add_meta_box( $widget_id, $widget_name, $callback, $screen, $location, $priority );
}

