<?php

namespace Studiometa\WPToolkit\Helpers;

class Env
{
    /**
     * Get an environment variable value.
     *
     * @param  string $key The variable name.
     * @return string
     */
    public static function get(string $key): string
    {
		// phpcs:ignore
		/** @var array<string, string> Good type. */
        $env = $_ENV;
        // In some environment, values are not available in the `$_ENV` variables,
        // so we use `getenv` as a fallback to try and get the value.
        return $env[ $key ] ?? (string) getenv($key);
    }

    /**
     * Get the current APP_ENV configuration.
     *
     * @return string
     */
    private static function get_app_env(): string
    {
        return strtolower(self::get('APP_ENV'));
    }

    /**
     * Test if the current environment is production.
     * Both `production` and `prod` values are tested on the `APP_ENV` or
     * `WP_ENV` environment variable or the `WP_ENV` constant.
     *
     * @return bool
     */
    public static function is_prod(): bool
    {
        $env = self::get_app_env();
        return $env === 'production' || $env === 'prod';
    }

    /**
     * Test if the current environment is preprod.
     *
     * @return bool
     */
    public static function is_preprod(): bool
    {
        return self::get_app_env() === 'preprod';
    }

    /**
     * Test if the current environment is local.
     *
     * @return bool
     */
    public static function is_local(): bool
    {
        return self::get_app_env() === 'local';
    }

    /**
     * Test if the current environment is staging.
     *
     * @return bool
     */
    public static function is_staging(): bool
    {
        return self::get_app_env() === 'staging';
    }

    /**
     * Test if the current environment is development.
     *
     * @return bool
     */
    public static function is_development(): bool
    {
        return self::get_app_env() === 'development';
    }

    public static function is_wp_cli(): bool
    {
        return defined('WP_CLI') ? WP_CLI : false;
    }
}
