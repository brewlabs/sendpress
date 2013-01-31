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

		/**
		 * Class constructor
		 */
		function __construct() {
			/*
			// The tracking checks daily, but only sends new data every 7 days.
			if ( !wp_next_scheduled( 'sendpress_tracking' ) ) {
				wp_schedule_event( time(), 'daily', 'sendpress_tracking' );
			}

			add_action( 'sendpress_tracking', array( $this, 'tracking' ) );
			*/
		}

		/**
		 * Main tracking function.
		 */
		function tracking() {
			// Start of Metrics
			global $wpdb;

			
			$hash = SendPress_Option::get( 'hash' );

			if ( $hash == false ) {
				$hash = md5( site_url() );
				SendPress_Option::set('hash', $hash);
			}

			$data = '';// get_transient( 'sendpress_tracking_cache' );
			if ( !$data ) {

				$pts = array();
				foreach ( get_post_types( array( 'public' => true ) ) as $pt ) {
					$count    = wp_count_posts( $pt );
					$pts[$pt] = $count->publish;
				}

				$comments_count = wp_count_comments();

				// wp_get_theme was introduced in 3.4, for compatibility with older versions, let's do a workaround for now.
				if ( function_exists( 'wp_get_theme' ) ) {
					$theme_data = wp_get_theme();
					$theme      = array(
						'name'       => $theme_data->display( 'Name', false, false ),
						'theme_uri'  => $theme_data->display( 'ThemeURI', false, false ),
						'version'    => $theme_data->display( 'Version', false, false ),
						'author'     => $theme_data->display( 'Author', false, false ),
						'author_uri' => $theme_data->display( 'AuthorURI', false, false ),
					);
					if ( isset( $theme_data->template ) && !empty( $theme_data->template ) && $theme_data->parent() ) {
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
				} else {
					$theme_data = (object) get_theme_data( get_stylesheet_directory() . '/style.css' );
					$theme      = array(
						'version'  => $theme_data->Version,
						'name'     => $theme_data->Name,
						'author'   => $theme_data->Author,
						'template' => $theme_data->Template,
					);
				}

				$plugins = array();
				foreach ( get_option( 'active_plugins' ) as $plugin_path ) {
					$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );

					$slug           = str_replace( '/' . basename( $plugin_path ), '', $plugin_path );
					$plugins[$slug] = array(
						'version'    => $plugin_info['Version'],
						'name'       => $plugin_info['Name'],
						'plugin_uri' => $plugin_info['PluginURI'],
						'author'     => $plugin_info['AuthorName'],
						'author_uri' => $plugin_info['AuthorURI'],
					);
				}

				$data = array(
					'site'     => array(
						'hash'      => $hash,
						'url'       => site_url(),
						'name'      => get_bloginfo( 'name' ),
						'version'   => get_bloginfo( 'version' ),
						'multisite' => is_multisite(),
						'users'     => count( get_users() ),
						'lang'      => get_locale(),
					),
					'pts'      => $pts,
					'comments' => array(
						'total'    => $comments_count->total_comments,
						'approved' => $comments_count->approved,
						'spam'     => $comments_count->spam,
						'pings'    => $wpdb->get_var( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_type = 'pingback'" ),
					),
					'options'  => apply_filters( 'sendpress_tracking_filters', array() ),
					'theme'    => $theme,
					'plugins'  => $plugins,
				);

				$args = array(
					'body' => json_encode($data)
				);
				$wtf = wp_remote_post( 'http://joshlmbprd.whipplehill.com/wp/api/tracking/update', $args );

				//print_r($wtf);

				// Store for a week, then push data again.
				set_transient( 'sendpress_tracking_cache', true, 60 * 5 ); // 7 * 60 * 60 * 24 );
			}
		}
	}
}

/**
 * Adds tracking parameters for WP SEO settings. Outside of the main class as the class could also be in use in other plugins.
 *
 * @param array $options
 * @return array
 */
function sendpress_tracking_additions( $options ) {
	
	return $options;
}

add_filter( 'sendpress_tracking_filters', 'sendpress_tracking_additions' );