<?php
/**
 * Manage your WordPress assets with a simple YAML configuration file.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WPToolkit\Managers;

use Studiometa\WPToolkit\Managers\ManagerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Helper class to manage a theme's assets.
 */
class AssetsManager implements ManagerInterface {
	/**
	 * The parsed configuration.
	 *
	 * @var array
	 */
	public $config;

	/**
	 * @inheritdoc
	 */
	public function run() {
		$config_path = get_template_directory() . '/assets.yml';

		if ( ! file_exists( $config_path ) ) {
			$config_path = get_template_directory() . '/assets.yaml';

			if ( ! file_exists( $config_path ) ) {
				$msg = "No assets config file found. Try adding an `assets.yml` file in 'get_template_directory()'.";
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( esc_html( $msg ), E_USER_NOTICE );
				return;
			}
		}

		$this->config = Yaml::parseFile( $config_path );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_all' ) );
		add_filter( 'template_include', array( $this, 'enqueue_all' ) );
	}

	/**
	 * Register all defined JS and CSS assets with automatic
	 * versioning based on their content's MD5 hash.
	 *
	 * @return void
	 */
	public function register_all() {
		foreach ( $this->config as $name => $config ) {
			if ( isset( $config['css'] ) ) {
				foreach ( $config['css'] as $handle => $path ) {
					$this->register( 'style', $handle, $path );

					// Enqueue directly if the name of the config is 'all'.
					if ( 'all' === $name ) {
						wp_enqueue_style( $handle );
					}
				}
			}

			if ( isset( $config['js'] ) ) {
				foreach ( $config['js'] as $handle => $path ) {
					$this->register( 'script', $handle, $path );

					// Enqueue directly if the name of the config is 'all'.
					if ( 'all' === $name ) {
						wp_enqueue_script( $handle );
					}
				}
			}
		}
	}

	/**
	 * Enqueue CSS and JS files based on the WordPress template.
	 *
	 * @param  string $template The template path.
	 * @return string           The template path.
	 */
	public function enqueue_all( $template ) {
		$potential_names = $this->get_potential_names( $template );

		foreach ( $potential_names as $potential_name ) {
			foreach ( $this->config as $name => $config ) {
				if ( (string) $name !== $potential_name ) {
					continue;
				}

				if ( isset( $config['css'] ) ) {
					foreach ( $config['css'] as $handle => $path ) {
						$this->enqueue( 'style', $handle );
					}
				}

				if ( isset( $config['js'] ) ) {
					foreach ( $config['js'] as $handle => $path ) {
						$this->enqueue( 'script', $handle );
					}
				}
			}
		}

		return $template;
	}

	/**
	 * Get all the potential assets group name.
	 * For a template file `single-post-hello.php`, the following group names
	 * will be returned:
	 *
	 * - single
	 * - single-post
	 * - single-post-hello
	 *
	 * @param string $template The full template path.
	 * @return array A list of potential assets name.
	 */
	protected function get_potential_names( string $template ):array {
		$pathinfo = pathinfo( $template );
		$parts    = explode( '-', $pathinfo['filename'] );

		return array_reduce(
			$parts,
			function ( $acc, $part ) {
				if ( empty( $acc ) ) {
					return array( $part );
				}

				$previous_part = $acc[ count( $acc ) - 1 ];
				$acc[]         = $previous_part . '-' . $part;

				return $acc;
			},
			array()
		);
	}


	/**
	 * Register a single asset.
	 *
	 * @param  string       $type   The type of the asset: 'style' or 'script'.
	 * @param  string       $handle The asset's handle.
	 * @param  string|array $path   The asset's path in the theme.
	 * @return void
	 */
	protected function register( string $type, string $handle, $path ):void {
		if ( is_array( $path ) ) {
			$_path     = $path;
			$path      = $_path['path'];
			$media     = $_path['media'] ?? 'all';
			$in_footer = $_path['footer'] ?? true;
		} else {
			$media     = 'all';
			$in_footer = true;
		}

		$public_path = get_template_directory_uri() . '/' . $path;

		if ( ! file_exists( get_template_directory() . '/' . $path ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( esc_html( "The asset file '$path' does not exist." ), E_USER_NOTICE );
			return;
		}

		$hash = md5_file( get_template_directory() . '/' . $path );

		if ( 'style' === $type ) {
			wp_register_style(
				$handle,
				$public_path,
				array(),
				$hash,
				$media
			);
		} else {
			wp_register_script(
				$handle,
				$public_path,
				array(),
				$hash,
				$in_footer
			);
		}
	}

	/**
	 * Enqueue an asset given its handle.
	 *
	 * @param  string $type   The type of the asset: 'style' or 'script'.
	 * @param  string $handle The asset's handle.
	 * @return void
	 */
	protected function enqueue( $type, $handle ) {
		add_action(
			'wp_enqueue_scripts',
			function () use ( $type, $handle ) {
				if ( 'style' === $type ) {
					wp_enqueue_style( $handle );
				} else {
					wp_enqueue_script( $handle );
				}
			}
		);
	}
}
