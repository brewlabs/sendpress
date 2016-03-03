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
			$transient_key = 'sendpress_tracking_cache';
			$data          = get_transient( $transient_key );

			// bail if transient is set and valid
			if ( $data !== false ) {
				return;
			}

			// Make sure to only send tracking data once a week
			set_transient( $transient_key, 1, 7 * 86400 );

			// Start of Metrics
			global $blog_id, $wpdb;

			$hash = get_option( 'SendPress_Tracking_Hash', false );

			if ( ! $hash || empty( $hash ) ) {
				// create and store hash
				$hash = md5( site_url() );
				update_option( 'SendPress_Tracking_Hash', $hash );
			}

			$pts        = array();
			$post_types = get_post_types( array( 'public' => true ) );
			if ( is_array( $post_types ) && $post_types !== array() ) {
				foreach ( $post_types as $post_type ) {
					$count             = wp_count_posts( $post_type );
					$pts[ $post_type ] = $count->publish;
				}
			}
			unset( $post_types );

			$comments_count = wp_count_comments();

			$theme_data     = wp_get_theme();
			$theme          = array(
				'name'       => $theme_data->display( 'Name', false, false ),
				'theme_uri'  => $theme_data->display( 'ThemeURI', false, false ),
				'version'    => $theme_data->display( 'Version', false, false ),
				'author'     => $theme_data->display( 'Author', false, false ),
				'author_uri' => $theme_data->display( 'AuthorURI', false, false ),
			);
			$theme_template = $theme_data->get_template();
			if ( $theme_template !== '' && $theme_data->parent() ) {
				$theme['template'] = array(
					'version'    => $theme_data->parent()->display( 'Version', false, false ),
					'name'       => $theme_data->parent()->display( 'Name', false, false ),
					'theme_uri'  => $theme_data->parent()->display( 'ThemeURI', false, false ),
					'author'     => $theme_data->parent()->display( 'Author', false, false ),
					'author_uri' => $theme_data->parent()->display( 'AuthorURI', false, false ),
				);
			} else {
				$theme['template'] = '';
			}
			unset( $theme_template );


			$plugins       = array();
			$active_plugin = get_option( 'active_plugins' );
			foreach ( $active_plugin as $plugin_path ) {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				}

				$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

				$slug             = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
				$plugins[ $slug ] = array(
					'version'    => $plugin_info['Version'],
					'name'       => $plugin_info['Name'],
					'plugin_uri' => $plugin_info['PluginURI'],
					'author'     => $plugin_info['AuthorName'],
					'author_uri' => $plugin_info['AuthorURI'],
				);
			}
			unset( $active_plugins, $plugin_path );

			$lists = SendPress_Data::get_lists();

			$data = array(
				'site'      => array(
					'hash'      => $hash,
					'wp_version'   => get_bloginfo( 'version' ),
					'sp'   => SENDPRESS_VERSION,
					'pro'   => defined('SENDPRESS_PRO_VERSION') ? SENDPRESS_PRO_VERSION : 0 ,
					'lists' 	=> count($lists->posts),
					'subscribers' => SendPress_Data::get_total_subscribers(),
					'multisite' => is_multisite(),
					'lang'      => get_locale(),
				),
				'pts'       => $pts,
				'options'   => apply_filters( 'sp_tracking_filters', array() ),
				'theme'     => $theme,
				'plugins'   => $plugins,
			);

			$args = array(
				'body'      => $data,
				'blocking'  => false,
				'sslverify' => false,
			);

			wp_remote_post( 'http://api.sendpress.com/api/v1/track/add', $args );
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




static function be_password_pointer_print_admin_bar() {

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





function spnl_tracking_additions( $options ) {
	if ( function_exists( 'curl_version' ) ) {
		$curl = curl_version();
	} else {
		$curl = null;
	}


	//$opt = WPSEO_Options::get_all();

	$options['sp'] = array(
		/*
		'xml_sitemaps'                => ( $opt['enablexmlsitemap'] === true ) ? 1 : 0,
		'force_rewrite'               => ( $opt['forcerewritetitle'] === true ) ? 1 : 0,
		'opengraph'                   => ( $opt['opengraph'] === true ) ? 1 : 0,
		'twitter'                     => ( $opt['twitter'] === true ) ? 1 : 0,
		'strip_category_base'         => ( $opt['stripcategorybase'] === true ) ? 1 : 0,
		'on_front'                    => get_option( 'show_on_front' ),
		'wmt_alexa'                   => ( ! empty( $opt['alexaverify'] ) ) ? 1 : 0,
		'wmt_bing'                    => ( ! empty( $opt['msverify'] ) ) ? 1 : 0,
		'wmt_google'                  => ( ! empty( $opt['googleverify'] ) ) ? 1 : 0,
		'wmt_pinterest'               => ( ! empty( $opt['pinterestverify'] ) ) ? 1 : 0,
		'wmt_yandex'                  => ( ! empty( $opt['yandexverify'] ) ) ? 1 : 0,
		'permalinks_clean'            => ( $opt['cleanpermalinks'] == 1 ) ? 1 : 0,
		*/
		'site_db_charset'             => DB_CHARSET,

		'webserver_apache'            => spnl_is_apache() ? 1 : 0,
		'webserver_apache_version'    => function_exists( 'apache_get_version' ) ? apache_get_version() : 0,
		'webserver_nginx'             => spnl_is_nginx() ? 1 : 0,

		'webserver_server_software'   => $_SERVER['SERVER_SOFTWARE'],
		'webserver_gateway_interface' => $_SERVER['GATEWAY_INTERFACE'],
		'webserver_server_protocol'   => $_SERVER['SERVER_PROTOCOL'],

		'php_version'                 => phpversion(),

		'php_max_execution_time'      => ini_get( 'max_execution_time' ),
		'php_memory_limit'            => ini_get( 'memory_limit' ),
		'php_open_basedir'            => ini_get( 'open_basedir' ),

		'php_bcmath_enabled'          => extension_loaded( 'bcmath' ) ? 1 : 0,
		'php_ctype_enabled'           => extension_loaded( 'ctype' ) ? 1 : 0,
		'php_curl_enabled'            => extension_loaded( 'curl' ) ? 1 : 0,
		'php_curl_version_a'          => phpversion( 'curl' ),
		'php_curl'                    => ( ! is_null( $curl ) ) ? $curl['version'] : 0,
		'php_dom_enabled'             => extension_loaded( 'dom' ) ? 1 : 0,
		'php_dom_version'             => phpversion( 'dom' ),
		'php_filter_enabled'          => extension_loaded( 'filter' ) ? 1 : 0,
		'php_mbstring_enabled'        => extension_loaded( 'mbstring' ) ? 1 : 0,
		'php_mbstring_version'        => phpversion( 'mbstring' ),
		'php_pcre_enabled'            => extension_loaded( 'pcre' ) ? 1 : 0,
		'php_pcre_version'            => phpversion( 'pcre' ),
		'php_pcre_with_utf8_a'        => @preg_match( '/^.{1}$/u', 'ñ', $UTF8_ar ),
		'php_pcre_with_utf8_b'        => defined( 'PREG_BAD_UTF8_ERROR' ),
		'php_spl_enabled'             => extension_loaded( 'spl' ) ? 1 : 0,
	);

	return $options;
}


add_filter( 'sp_tracking_filters', 'spnl_tracking_additions' );






