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
