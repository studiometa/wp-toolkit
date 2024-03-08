<?php

use Studiometa\WPToolkit\Helpers\Request as RequestClass;
use function Studiometa\WPToolkit\request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * RequestTest test case.
 */
class RequestTest extends WP_UnitTestCase {

	/**
	 * Test the request() function
	 *
	 * @return void
	 */
	public function test_type_of_request_function_helper() {
		$request1 = RequestClass::request();
		$request2 = request();
		$this->assertTrue($request1 instanceof SymfonyRequest);
		$this->assertTrue($request2 instanceof SymfonyRequest);
		$this->assertTrue($request1 === $request2);
	}
}
