<?php

use Studiometa\WPToolkit\Helpers\Plugin;

/**
 * PluginTest test case.
 */
class PluginTest extends WP_UnitTestCase {

	public function set_up():void {
		parent::set_up();
		activate_plugin( 'akismet/akismet.php' );
	}

	/**
	 * Test `is_plugin_enabled` function.
	 *
	 * @return void
	 */
	public function test_is_plugin_enabled() {
		$this->assertTrue(
			is_bool( Plugin::is_plugin_enabled( 'my-plugin/my-plugin.php' ) )
		);
	}

	/**
	 * Test the `disable` method.
	 *
	 * @return void
	 */
	public function test_plugins_have_been_disabled() {
		$this->assertTrue( is_plugin_active( 'akismet/akismet.php' ) );
		Plugin::disable( array( 'akismet/akismet.php' ) );
		$this->assertFalse( is_plugin_active( 'akismet/akismet.php' ) );
	}
}
