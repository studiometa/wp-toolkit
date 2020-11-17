<?php
/**
 * Utilities to configure custom post types.
 *
 * @package    studiometa/wp
 * @author     Lucas Simeon <lucas.s@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WP\Builder;

/**
 * Cleanup a WordPress project for security and performance.
 */
abstract class Builder {
	/**
	 * The post type key.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * The post type configuration.
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * The post type configuration.
	 *
	 * @var array
	 */
	public $default_config = array();

	/**
	 * The method used to register the builder.
	 *
	 * @var string
	 */
	public $register_method = '';

	/**
	 * __construct
	 *
	 * @param string $type   The post type key.
	 * @param array  $config A config for the builder.
	 */
	public function __construct( string $type, array $config = array() ) {
		$this->type = $type;

		if ( is_array( $this->default_config ) ) {
			$this->update_config( $this->default_config );
		}

		$this->update_config( $config );
	}

	/**
	 * Build the post type configuration.
	 *
	 * @return array The post type configuration.
	 */
	public function get_config():array {
		return $this->config;
	}

	/**
	 * Set a config key -> value pair.
	 *
	 * @param string $key   The key to set.
	 * @param mixed  $value The value to set.
	 * @return $this
	 */
	public function set_config( string $key, $value ) {
		return $this->update_config( array( $key => $value ) );
	}

	/**
	 * Update multiple config values.
	 *
	 * @param array $config The config to merge.
	 * @return $this
	 */
	public function update_config( $config ) {
		$this->config = array_merge( $this->config, $config );
		return $this;
	}

	/**
	 * Get the builder type.
	 *
	 * @return string The builder type.
	 */
	private function get_type():string {
		return $this->type;
	}

	/**
	 * Get the register method name.
	 *
	 * @return string The register method name.
	 */
	private function get_register_method():string {
		return $this->register_method;
	}

	/**
	 * Register the post type.
	 *
	 * @return void
	 */
	public function register():void {
		$register_method = $this->get_register_method();
		if ( $register_method && is_callable( $register_method ) ) {
			$register_method( $this->get_type(), $this->get_config() );
		}
	}
}
