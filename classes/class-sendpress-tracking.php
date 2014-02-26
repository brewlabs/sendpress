<?php
if ( !defined( 'SENDPRESS_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}



/**
 * Class that creates the tracking functionality for WP SEO, as the core class might be used in more plugins, it's checked for existence first.
 */
if ( !class_exists( 'SendPress_Tracking' ) ) {
	class SendPress_Tracking {

		static function init(){
			add_action( 'admin_enqueue_scripts', array('SendPress_Tracking','be_password_pointer_enqueue' ));
		}

		static function data() {
			/*
		    // PressTrends Account API Key
		    $api_key = 'eu1x95k67zut64gsjb5qozo7whqemtqiltzu';
		    $auth    = 'j0nc5cpqb2nlv8xgn0ouo7hxgac5evn0o';
		    // Start of Metrics
		    global $wpdb;
		    $data = get_transient( 'presstrends_cache_data' );
		    if ( !$data || $data == '' ) {
		        $api_base = 'http://api.presstrends.io/index.php/api/pluginsites/update/auth/';
		        $url      = $api_base . $auth . '/api/' . $api_key . '/';
		        $count_posts    = wp_count_posts();
		        $count_pages    = wp_count_posts( 'page' );
		        $comments_count = wp_count_comments();
		        if ( function_exists( 'wp_get_theme' ) ) {
		            $theme_data = wp_get_theme();
		            $theme_name = urlencode( $theme_data->Name );
		        } else {
		            $theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );
		            $theme_name = $theme_data['Name'];
		        }
		        $plugin_name = '&';
		        foreach ( get_plugins() as $plugin_info ) {
		            $plugin_name .= $plugin_info['Name'] . '&';
		        }
		        // CHANGE __FILE__ PATH IF LOCATED OUTSIDE MAIN PLUGIN FILE
		        $plugin_data         = get_plugin_data( __FILE__ );
		        $posts_with_comments = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type='post' AND comment_count > 0" );
		        $data                = array(
		            'url'             => base64_encode(site_url()),
		            'posts'           => $count_posts->publish,
		            'pages'           => $count_pages->publish,
		            'comments'        => $comments_count->total_comments,
		            'approved'        => $comments_count->approved,
		            'spam'            => $comments_count->spam,
		            'pingbacks'       => $wpdb->get_var( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'" ),
		            'post_conversion' => ( $count_posts->publish > 0 && $posts_with_comments > 0 ) ? number_format( ( $posts_with_comments / $count_posts->publish ) * 100, 0, '.', '' ) : 0,
		            'theme_version'   => $plugin_data['Version'],
		            'theme_name'      => $theme_name,
		            'site_name'       => str_replace( ' ', '', get_bloginfo( 'name' ) ),
		            'plugins'         => count( get_option( 'active_plugins' ) ),
		            'plugin'          => urlencode( $plugin_name ),
		            'wpversion'       => get_bloginfo( 'version' ),
		        );
		        foreach ( $data as $k => $v ) {
		            $url .= $k . '/' . $v . '/';
		        }
		        wp_remote_get( $url );
		        set_transient( 'presstrends_cache_data', $data, 60 * 60 * 24 );
		    }
		    */
		}

		// Setup Events
		static function event($event_name) {
			return;
			/*
			// PressTrends Account API Key & Theme/Plugin Unique Auth Code
			$api_key 		= 'eu1x95k67zut64gsjb5qozo7whqemtqiltzu';
			$auth 			= 'j0nc5cpqb2nlv8xgn0ouo7hxgac5evn0o';
			$api_base 		= 'http://api.presstrends.io/index.php/api/events/track/auth/';
			$api_string     = $api_base . $auth . '/api/' . $api_key . '/';
			$site_url 		= base64_encode(site_url());
		    $event_string	= $api_string . 'name/' . urlencode($event_name) . '/url/' . $site_url . '/';
			wp_remote_get( $event_string );
			*/
		}

		function presstrends_theme_options() {
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			?>
			<form action="options.php" method="post">
			<?php settings_fields('presstrends_theme_opt'); ?>
			<?php do_settings_sections('presstrends_top'); ?>
			<p class="submit">
			<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Update'); ?>" />
			</p>
			</form>
			<?php
		}



// Add PressTrends Pointer
static function be_password_pointer_enqueue( $hook_suffix ) {
	$enqueue = false;

	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

	if ( ! in_array( 'activate_autocron', $dismissed ) && SendPress_Option::get('autocron') == 'no' ) {
		$enqueue = true;
		add_action( 'admin_print_footer_scripts', array('SendPress_Tracking','be_password_pointer_print_admin_bar') );
	}

	if ( $enqueue ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
	}
}




function be_password_pointer_print_admin_bar() {

	$pointer_content  = '<h3>' . 'SendPress Pro Free Feature' . '</h3>';
	$pointer_content .= '<p>' . '<b>Auto Cron</b>: Every hour we visit your site, just like a "cron" job. No setup involved. Easy and hassle free.' . '</p><p>See the <a href="'.SendPress_Admin::link('Settings_Account').'">Sending Account</a> tab for more details.</p>';

?>

	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function($) {
		$('#wpadminbar').pointer({
			content: '<?php echo $pointer_content; ?>',
			position: {
			edge: 'top',
			align: 'center'
		},
			pointerWidth: 300,
			buttons:function (event, t) {
					button = jQuery('<div class="wp-pointer-buttons"><a id="sp-pointer-close" style="margin-left:5px" class="button-secondary">Dismiss</a><a id="sp-pointer-primary" class="button-primary">Enable Pro Auto Cron</a></div>');
					button.bind('click.pointer', function () {
						t.element.pointer('close');
					});
					return button;
				},
				close:function () {
					$.post( ajaxurl, {
						pointer: 'activate_autocron',
						action: 'dismiss-wp-pointer'
					});

				},

			

		}).pointer('open');

		jQuery('#sp-pointer-primary').click(function (event) {
			event.preventDefault();
			$.post( ajaxurl, {
						enable: true,
						action: 'sendpress-autocron'
					});				
		});
		
	});

	


	//]]>
	</script>

<?php
}










	}



}
