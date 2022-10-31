<?php

use Studiometa\WPToolkit\Helpers\PluginHelper;

/**
 * PluginHelperTest test case.
 */
class PluginHelperTest extends WP_UnitTestCase {
	/**
	 * Test `is_plugin_enabled` function.
	 *
	 * @return void
	 */
	public function test_is_plugin_enabled() {
		$this->assertTrue(
			is_bool( PluginHelper::is_plugin_enabled( 'my-plugin/my-plugin.php' ) )
		);
	}
}
