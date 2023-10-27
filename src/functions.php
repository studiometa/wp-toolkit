<?php
/**
 * Helper functions.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2023 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.1.0
 */

namespace Studiometa\WPToolkit;

use Symfony\Component\HttpFoundation\Request;
use Studiometa\WPToolkit\Helpers\RequestHelper;
use Studiometa\WPToolkit\Helpers\EnvHelper;

/**
 * Get the Request instance.
 *
 * @return Request
 */
function request(): Request {
	return RequestHelper::get();
}

/**
 * Get an environment variable value.
 *
 * @param  string $key The variable name.
 * @return string
 */
function env( string $key ): string {
	return EnvHelper::get( $key );
}
