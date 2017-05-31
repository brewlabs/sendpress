<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Help extends SendPress_View{
	function prerender(){
		
		wp_enqueue_script( 'dashboard' );
		sp_add_help_widget( 'help_support', 'Contact Us', array(&$this,'help_support'));
		//sp_add_help_widget( 'help_knowledge', 'Recent Knowledge Base Articles', array(&$this,'help_knowledge'),'side' );
		sp_add_help_widget( 'help_debug', 'Debug Information', array(&$this,'help_debug'), 'side');
		
		//sp_add_help_widget( 'help_blog', 'Recent Blog Posts', array(&$this,'help_blog'),'normal',  array(&$this,'help_blog_control') );
		sp_add_help_widget( 'help_shortcodes', 'Shortcode Cheat Sheet', array(&$this,'help_shortcodes') ,'normal');
		sp_add_help_widget( 'help_editemail', 'Customizing Emails', array(&$this,'help_editemail') ,'normal');


	}

	function help_editemail(){
?>
		<b><?php _e('SendPress Editor Button','sendpress'); ?></b>
		<p><?php _e('Look for this button','sendpress'); ?> <img src="<?php echo SENDPRESS_URL; ?>/js/icon.png" /> <?php _e('to open the popup for adding posts to an email and personilizing emails.','sendpress'); ?></p>

		

<?php
	}

	function help_shortcodes(){ ?>
	<p class="lead"><?php _e('Click a title to view info about a shortcode.','sendpress'); ?></p>
	<?php
		SendPress_Shortcode_Loader::docs();
	}

	function help_support(){
	
		
?>
	<b><?php _e('Basic Support','sendpress'); ?></b>
	<p><?php _e('You can get support for the FREE version of SendPress on the','sendpress'); ?> <a href="http://wordpress.org/support/plugin/sendpress" target="_blank"><?php _e('WordPress.org forums','sendpress'); ?></a>.<br><?php _e('Also check our','sendpress'); ?> <a href="http://sendpress.com/support" target="_blank"><?php _e('Knowledge Base','sendpress'); ?></a> <?php _e('for help at','sendpress'); ?> <a href="http://docs.sendpress.com/" target="_blank">http://docs.sendpress.com/</a></p>
	<br>
	<b><?php _e('Premium Support','sendpress'); ?></b>
	<p><?php _e('Premium support is available if you have purchased SendPress Pro from SendPress.com. Premium support can be accessed via our support site:','sendpress'); ?> <a href="http://sendpress.com/your-account" target="_blank">http://sendpress.com/your-account</a> <?php _e('and requires a SendPress.com account','sendpress'); ?>.</p>
	
<?php
	}
	 function display() {
		$browser = NEW SendPress_Browser();
		if ( get_bloginfo( 'version' ) < '3.4' ) {
			$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$theme      = $theme_data['Name'] . ' ' . $theme_data['Version'];
		} else {
			$theme_data = wp_get_theme();
			$theme      = $theme_data->Name . ' ' . $theme_data->Version;
		}

		// Try to identify the hosting provider
		$host = false;
		if ( defined( 'WPE_APIKEY' ) ) {
			$host = 'WP Engine';
		} elseif ( defined( 'PAGELYBIN' ) ) {
			$host = 'Pagely';
		}

		$request['cmd'] = '_notify-validate';

		$params = array(
			'sslverify' => false,
			'timeout'   => 60,
			'body'      => $request,
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$WP_REMOTE_POST = 'wp_remote_post() works' . "\n";
		} else {
			$WP_REMOTE_POST = 'wp_remote_post() does not work' . "\n";
		}

		return $this->display_output( $browser, $theme, $host, $WP_REMOTE_POST );
	}
	//Render Info Display
	 function display_output( $browser, $theme, $host, $WP_REMOTE_POST ) {
		global $wpdb;
		ob_start();
		require_once( SENDPRESS_PATH . 'inc/output.php' );
		return ob_get_clean();
	}

	function help_debug(){
		global $wp_version,$wpdb;
		$browser = NEW SendPress_Browser();
		echo "<b>WordPress Version</b>: ". $wp_version."<br>";
		echo "<b>SendPress Version</b>: ".SENDPRESS_VERSION ."<br>";
		if(defined('SENDPRESS_PRO_VERSION')){
			echo "<b>SendPress Pro Version</b>: ".SENDPRESS_VERSION ."<br>";
		}
		echo '<b>PHP Version</b>: ' . phpversion(). '<br>';
		
		$mem = (int) ini_get('memory_limit') ;	
		$used =  function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0;
		if(!empty( $mem ) && !empty( $used ) ){
			$prec = round ( $used / $mem * 100, 0);
		}
		echo '<b>PHP Memory Limit</b>: '. $mem . __(' MByte') . '<br>';
		echo '<b>PHP Memory Used</b>: '. $used . __(' MByte') . '<br>';
		
		echo '<b>MySQL Version</b>: ' . $wpdb->db_version() . '<br><br>';

		echo '<b>Send Setup</b>: ' . SendPress_Option::get( 'sendmethod' ) . '<br><br>';
	
		SendPress_DB_Tables::check_setup();

		/*
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
	  }  */?><br><br>

	  	<b><?php _e('Support Info','sendpress'); ?>:</b>
	  	<textarea readonly="readonly" class="sendpress-sysinfo"  name="sendpress-sysinfo" title="<?php _e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'sendpress' ); ?>"><?php echo esc_html( $this->display() ); ?></textarea>



	  <?php
	 

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


	function html(){
		 SendPress_Tracking::event('Help Tab');
		global $wp_version;
$screen = get_current_screen();

	$class = 'columns-2';//. get_current_screen()->get_columns();

?>
<div id="dashboard-widgets" class="metabox-holder clearfix <?php echo $class; ?>">
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
SendPress_Admin::add_cap('Help','sendpress_view');
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
		if ( $widget_id == SPNL()->validate->_int('edit') ) {
			//Uses esc_url
			list($url) = explode( '#', add_query_arg( 'edit', false ), 2 );
			$widget_name .= ' <span class="postbox-title-action"><a href="' . esc_url( $url ) . '">' . __( 'Cancel' ) . '</a></span>';
			$callback = '_sp_help_control_callback';
		} else {
			//Uses esc_url
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

