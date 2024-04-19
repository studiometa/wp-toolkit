<?php
/**
 * Helpers
 *
 * @package Studiometa
 */

namespace Studiometa\WPToolkit;

use Studiometa\WPToolkit\Helpers\Env;
use Studiometa\WPToolkit\Helpers\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Get an environment variable value.
 *
 * @param  string $key The variable name.
 * @return string
 */
function env(string $key): string
{
    return Env::get($key);
}

/**
 * Test if the current environment is prod.
 */
function env_is_prod(): bool
{
    return Env::is_prod();
}

/**
 * Test if the current environment is preprod.
 */
function env_is_preprod(): bool
{
    return Env::is_preprod();
}

/**
 * Test if the current environment is local.
 */
function env_is_local(): bool
{
    return Env::is_local();
}

/**
 * Test if the current environment is staging.
 */
function env_is_staging(): bool
{
    return Env::is_staging();
}

/**
 * Test if the current environment is development.
 */
function env_is_development(): bool
{
    return Env::is_development();
}

/**
 * Test if the current request comes from WP CLI.
 */
function env_is_wp_cli(): bool
{
    return Env::is_wp_cli();
}

/**
 * Get a Request instance from the symfony/http-foundation package.
 *
 * @see https://symfony.com/doc/current/components/http_foundation.html#request
 *
 * @return SymfonyRequest
 */
function request(): SymfonyRequest
{
    return Request::request();
}
