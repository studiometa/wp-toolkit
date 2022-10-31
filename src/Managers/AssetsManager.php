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
use Studiometa\WebpackConfig\Manifest;

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
	 * The parsed Webpack manifest.
	 *
	 * @var Manifest
	 */
	public $webpack_manifest;

	/**
	 * Configuration filepath.
	 *
	 * @var string
	 */
	private $configuration_filepath;

	/**
	 * Webpack manifest filepath.
	 *
	 * @var string
	 */
	private $webpack_manifest_filepath;

	/**
	 * Constructor.
	 *
	 * @param string|null $configuration_filepath Configuration filepath.
	 * @param string|null $webpack_manifest_filepath Webpack manifest filepath.
	 */
	public function __construct( ?string $configuration_filepath = null, ?string $webpack_manifest_filepath = null ) {
		$this->configuration_filepath    = get_template_directory() . '/config/assets.yml';
		$this->webpack_manifest_filepath = get_template_directory() . '/dist/assets-manifest.json';

		if ( isset( $configuration_filepath ) ) {
			$this->configuration_filepath = $configuration_filepath;
		}

		if ( isset( $webpack_manifest_filepath ) ) {
			$this->webpack_manifest_filepath = $webpack_manifest_filepath;
		}
	}

	// phpcs:ignore Generic.Commenting.DocComment.MissingShort
	/**
	 * @inheritdoc
	 */
	public function run() {
		if ( ! file_exists( $this->configuration_filepath ) ) {
			$msg = 'No assets configuration file found.';
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( esc_html( $msg ), E_USER_NOTICE );
			return;
		}

		// @phpstan-ignore-next-line
		$this->config = Yaml::parseFile( $this->configuration_filepath );

		if ( $this->webpack_manifest_filepath ) {
			if ( ! file_exists( $this->webpack_manifest_filepath ) ) {
				$msg = sprintf( 'No webpack manifest file found in `%s`.', $this->webpack_manifest_filepath );
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( esc_html( $msg ), E_USER_NOTICE );
				return;
			}

			$this->webpack_manifest = new Manifest(
				$this->webpack_manifest_filepath,
				$this->get_webpack_public_path_relative_to_theme()
			);
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'register_all' ) );
		add_filter( 'template_include', array( $this, 'enqueue_all' ) );
	}

	/**
	 * Get Webpack dist folder relative to the theme.
	 * @returns string
	 */
	private function get_webpack_public_path_relative_to_theme():string {
		return trim( str_replace( get_template_directory(), '', dirname( $this->webpack_manifest_filepath ) ), '/') . '/';
	}

	/**
	 * Register all defined JS and CSS assets with automatic
	 * versioning based on their content's MD5 hash.
	 *
	 * @return void
	 */
	public function register_all() {
		foreach ( $this->config as $name => $config ) {
			if ( isset( $config['entries'] ) && is_array( $config['entries'] ) ) {
				foreach ( $config['entries'] as $entry ) {
					$pathinfo = pathinfo( $entry );
					$entry    = implode(
						DIRECTORY_SEPARATOR,
						array( $pathinfo['dirname'] ?? '', $pathinfo['filename'] )
					);

					$webpack_entry = $this->webpack_manifest->entry( $entry );

					if ( ! $webpack_entry ) {
						continue;
					}

					$webpack_entry->styles->each(
						function ( $style, $handle ) {
							$this->register( 'style', $handle, $style->getAttribute( 'href' ) );
						}
					);

					$webpack_entry->scripts->each(
						function ( $script, $handle ) {
							$this->register( 'script', $handle, $script->getAttribute( 'src' ) );
						}
					);
				}
			}

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
		$potential_names  = ['all'] + $this->get_potential_names( $template );

		foreach ( $potential_names as $potential_name ) {
			foreach ( $this->config as $name => $config ) {
				if ( (string) $name !== $potential_name ) {
					continue;
				}

				if ( isset( $config['entries'] ) && is_array( $config['entries'] ) ) {
					foreach ( $config['entries'] as $entry ) {
						$pathinfo = pathinfo( $entry );
						$entry    = implode(
							DIRECTORY_SEPARATOR,
							array( $pathinfo['dirname'] ?? '', $pathinfo['filename'] )
						);

						$webpack_entry = $this->webpack_manifest->entry( $entry );

						if ( ! $webpack_entry ) {
							continue;
						}

						$webpack_entry->styles->keys()->each(
							function ( $handle ) {
								$this->enqueue( 'style', $handle );
							}
						);

						$webpack_entry->scripts->keys()->each(
							function ( $handle ) {
								$this->enqueue( 'script', $handle );
							}
						);
					}
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
		$handle = $this->format_handle( $handle );

		if ( is_array( $path ) ) {
			$_path     = $path;
			$path      = $_path['path'];
			$media     = $_path['media'] ?? 'all';
			$in_footer = $_path['footer'] ?? true;
		} else {
			$media     = 'all';
			$in_footer = true;
		}

		$webpack_path = str_replace( $this->get_webpack_public_path_relative_to_theme(), '', $path );

		// Read path from Webpack manifest if it exists.
		if ( $this->webpack_manifest instanceof Manifest && $this->webpack_manifest->asset( $webpack_path ) ) {
			$path = $this->webpack_manifest->asset( $webpack_path );
		}

		$path       = trim( $path, '/' );
		$full_path  = get_template_directory() . '/' . $path;
		$public_uri = get_template_directory_uri() . '/' . $path;

		$hash = null;
		if (file_exists( $full_path ) ) {
			$hash = md5_file( $full_path );
		}

		if ( 'style' === $type ) {
			wp_register_style(
				$handle,
				$public_uri,
				array(),
				$hash,
				$media
			);
		} else {
			wp_register_script(
				$handle,
				$public_uri,
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
		$handle = $this->format_handle( $handle );

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

	/**
	 * Prefix all handles with `theme-`.
	 * @param  string $handle
	 * @return string
	 */
	protected function format_handle( string $handle ):string {
		return 'theme-' . str_replace( '.', '-', $handle );
	}
}
