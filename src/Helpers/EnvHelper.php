<?php
/**
 * Request helper functions.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2021 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.1.0
 */

namespace Studiometa\WPToolkit\Helpers;

/**
 * Request helper class.
 */
class EnvHelper {
	/**
	 * Get an environment variable value.
	 *
	 * @param  string $key The variable name.
	 * @return string
	 */
	public static function get( string $key ): string {
		// phpcs:ignore
		/** @var array<string, string> Good type. */
		$env = $_ENV;
		// In some environment, values are not available in the `$_ENV` variables,
		// so we use `getenv` as a fallback to try and get the value.
		return $env[ $key ] ?? (string) getenv( $key );
	}
}
