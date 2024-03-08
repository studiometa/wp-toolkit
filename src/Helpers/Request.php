<?php

namespace Studiometa\WPToolkit\Helpers;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request {
	/**
	 * Private variable to hold the helpers instance.
	 *
	 * @var ?Request
	 */
	private static $instance = null;

	/**
	 * Private variables to hold the current request instance.
	 *
	 * @var SymfonyRequest
	 */
	private SymfonyRequest $request;

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->request = SymfonyRequest::createFromGlobals();
	}

	/**
	 * Get singleton instance.
	 *
	 * @return Request
	 */
	private static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Work with the current HTTP request.
	 *
	 * @return SymfonyRequest
	 */
	public static function request(): SymfonyRequest {
		return self::get_instance()->request;
	}
}
