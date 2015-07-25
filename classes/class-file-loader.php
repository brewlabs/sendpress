<?php

// Prevent loading this file directly
if ( ! defined( 'SENDPRESS_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

class File_Loader {

	private $type;

	function __construct( $identifier ) {
		$this->type = $identifier;

		$this->load_modules();
	}

	/**
	 * Loads the modules.
	 */
	function load_modules() {

		$modules = $this->get_available_modules();


		if ( $modules ) {
			usort( $modules, 'sendpress_sort' );
			foreach ( $modules as $module ) {
				require $this->get_module_path( $module[0], $module[1] );
			}
		}
		do_action( 'SendPress_Classes_Loaded' );
	}

	/**
	 * List available Jetpack modules. Simply lists .php files in /modules/.
	 * Make sure to tuck away module "library" files in a sub-directory.
	 */
	function get_available_modules() {

		$modules = false;

		$files = $this->glob_php( plugin_dir_path( __FILE__ ) );

		foreach ( $files as $file ) {

			if ( $headers = $this->get_module( $file, plugin_dir_path( __FILE__ ) ) ) {
				$modules[ $this->get_module_slug( $file ) ] = array(
					$this->get_module_slug( $file ),
					plugin_dir_path( __FILE__ )
				);
			}
		}

		return $modules;
	}

	/**
	 * Load module data from module file. Headers differ from WordPress
	 * plugin headers to avoid them being identified as standalone
	 * plugins on the WordPress plugins page.
	 */
	function get_module( $module, $dir ) {
		$headers = array(
			'name' => $this->type,
			'sort' => 'Sort Order'
		);

		$file = $this->get_module_path( $this->get_module_slug( $module ), $dir );
		$mod  = get_file_data( $file, $headers );

		if ( empty( $mod['sort'] ) ) {
			$mod['sort'] = 10;
		}
		if ( ! empty( $mod['name'] ) ) {
			return $mod;
		}

		return false;
	}

	/**
	 * Extract a module's full path from its slug.
	 */
	function get_module_slug( $file ) {
		return str_replace( '.php', '', basename( $file ) );
	}

	/**
	 * Generate a module's path from its slug.
	 */
	function get_module_path( $slug, $dir ) {
		return $dir . "/$slug.php";
	}

	/**
	 * Returns an array of all PHP files in the specified absolute path.
	 * Equivalent to glob( "$absolute_path/*.php" ).
	 *
	 * @param string $absolute_path The absolute path of the directory to search.
	 *
	 * @return array Array of absolute paths to the PHP files.
	 */
	function glob_php( $absolute_path ) {
		$absolute_path = untrailingslashit( $absolute_path );
		$files         = array();
		if ( ! $dir = @opendir( $absolute_path ) ) {
			return $files;
		}

		while ( false !== $file = readdir( $dir ) ) {
			if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, - 4 ) ) {
				continue;
			}

			$file = "$absolute_path/$file";

			if ( ! is_file( $file ) ) {
				continue;
			}

			$files[] = $file;
		}

		closedir( $dir );

		return $files;
	}

}