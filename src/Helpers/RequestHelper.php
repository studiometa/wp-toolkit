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

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Request helper class.
 */
class RequestHelper {
	/**
	 * Request instance.
	 *
	 * @var Request|null
	 */
	private static $request = null;

	/**
	 * Get the Request instance.
	 *
	 * @return Request
	 */
	public static function get(): Request {
		if ( is_null( self::$request ) ) {
			self::$request = Request::createFromGlobals();
		}

		return self::$request;
	}
}
