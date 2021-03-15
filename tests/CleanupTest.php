<?php

use Studiometa\WPToolkit\Cleanup;

/**
 * CleanupTest test case.
 */
class CleanupTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$this->cleanup = new Cleanup();
	}

	/**
	 * Test remove css and js version.
	 *
	 * @return void
	 */
	public function test_remove_version_css_js() {
		$themes_uri = content_url( 'themes' );
		$theme_src  = $themes_uri . '/example/example.js?ver=2.0.0';
		$other_src  = 'https://example.org/example.js?ver=2.0.0';

		$updated_theme_src = $this->cleanup->remove_version_css_js( $theme_src );
		$updated_other_src = $this->cleanup->remove_version_css_js( $other_src );

		$this->assertFalse( strpos( $updated_other_src, 'ver=' ) );
		$this->assertNotFalse( strpos( $updated_theme_src, 'ver=' ) );
	}
}
