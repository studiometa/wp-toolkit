<?php

use Studiometa\WPToolkit\Helpers\Env as EnvClass;
use function Studiometa\WPToolkit\env;

/**
 * EnvTest test case.
 */
class EnvTest extends WP_UnitTestCase {

	/**
	 * Test the request() function
	 *
	 * @return void
	 */
	public function test_type_of_request_function_helper() {
		$this->assertTrue(is_string(env('missing')));
		$this->assertTrue(is_string(EnvClass::get('missing')));
	}
}
