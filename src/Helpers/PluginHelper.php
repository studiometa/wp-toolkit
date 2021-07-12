<?php
/**
 * Plugin helper functions.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2021 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WPToolkit\Helpers;

/**
 * Plugin helper class.
 */
class PluginHelper {
	/**
	 * Test if plugin is enabled.
	 *
	 * @param string $filepath Plugin filepath (relative to plugins folder).
	 *
	 * @return boolean Is plugin enabled?
	 */
	public static function is_plugin_enabled( string $filepath ):bool {
		$cache_key      = __FUNCTION__ . md5( $filepath );
		$cached_results = wp_cache_get( $cache_key, __CLASS__ );

		if ( false !== $cached_results ) {
			return (bool) $cached_results;
		}

		$is_enabled = is_plugin_active( $filepath );

		wp_cache_set( $cache_key, (int) $is_enabled, __CLASS__ );

		return $is_enabled;
	}
}
