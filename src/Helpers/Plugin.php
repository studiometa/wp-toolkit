<?php

/**
 * Helper class to work with plugins.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      2.0.0
 * @version    2.0.0
 */


namespace Studiometa\WPToolkit\Helpers;

use function WeCodeMore\earlyAddFilter as early_add_filter;

/**
 * Plugins class.
 */
class Plugin
{

    /**
     * Disabled plugins.
     *
     * @var string[]
     */
    private static $disabled_plugins = array();

    /**
     * Class instance.
     * @var self|null
     */
    private static $instance = null;

    /**
     * Hook into the activation filters on construction.
     */
    private function __construct()
    {
        early_add_filter(
            'option_active_plugins',
            function ($plugins) {
                return self::do_disabling($plugins);
            }
        );
        early_add_filter(
            'site_option_active_sitewide_plugins',
            function ($plugins) {
                return self::do_network_disabling($plugins);
            }
        );
    }

    /**
     * Hooks in to the option_active_plugins filter and does the disabling
     *
     * @param string[] $plugins WP-provided list of plugin filenames.
     *
     * @return string[] The filtered array of plugin filenames
     */
    private static function do_disabling($plugins): array
    {
        if (count(self::$disabled_plugins)) {
            foreach (self::$disabled_plugins as $disabled_plugin) {
                $key = array_search($disabled_plugin, $plugins, true);
                if (false !== $key) {
                    unset($plugins[ $key ]);
                }
            }
        }

        return $plugins;
    }

    /**
     * Hooks in to the site_option_active_sitewide_plugins filter and does the disabling
     *
     * @param string[] $plugins Plugins.
     *
     * @return string[]
     */
    private static function do_network_disabling($plugins)
    {
        if (count(self::$disabled_plugins)) {
            foreach ((array) self::$disabled_plugins as $plugin) {
                if (isset($plugins[ $plugin ])) {
                    unset($plugins[ $plugin ]);
                }
            }
        }

        return $plugins;
    }

    /**
     * Disable a list of plugins.
     *
     * @param string[] $plugins The list of plugins to disable.
     */
    public static function disable(array $plugins): void
    {
        self::$disabled_plugins = self::$disabled_plugins + $plugins;

        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
    }

    /**
     * Test if plugin is enabled.
     *
     * @param string $filepath Plugin filepath (relative to plugins folder).
     *
     * @codeCoverageIgnore
     *
     * @return boolean Is the plugin enabled?
     */
    public static function is_plugin_enabled(string $filepath):bool
    {
        $cache_key      = __FUNCTION__ . md5($filepath);
        $cached_results = wp_cache_get($cache_key, __CLASS__);

        if (false !== $cached_results) {
            return (bool) $cached_results;
        }

        $is_enabled = is_plugin_active($filepath);

        wp_cache_set($cache_key, (int) $is_enabled, __CLASS__);

        return $is_enabled;
    }
}
