<?php

use Studiometa\WPToolkit\TransientCleaner;

/**
 * PostTypeBuilder test case.
 */
class TransientCleanerTest extends WP_UnitTestCase {
	const POST_TRANSIENT_KEY   = TransientCleaner::PREFIX . 'transient_cleaner_post';
	const TERM_TRANSIENT_KEY   = TransientCleaner::PREFIX . 'transient_cleaner_term';
	const OPTION_TRANSIENT_KEY = TransientCleaner::PREFIX . 'transient_cleaner_option';

	/**
	 * Initialize transient cleaner based on config.
	 */
	public function setUp():void {
		parent::setUp();

		$this->config = array(
			'post'   => array(
				'post' => array(
					self::POST_TRANSIENT_KEY,
				),
			),
			'term'   => array(
				'post_tag' => array(
					self::TERM_TRANSIENT_KEY,
				),
			),
			'option' => array(
				'baz_option' => array(
					self::OPTION_TRANSIENT_KEY,
				),
			),
		);

		$this->transient_cleaner = TransientCleaner::get_instance( $this->config );

		set_transient( self::POST_TRANSIENT_KEY, 'foo' );
		set_transient( self::TERM_TRANSIENT_KEY, 'bar' );
		set_transient( self::OPTION_TRANSIENT_KEY, 'baz' );

		// Mock stored transient because WordPress `update_option` don't work in tests.
		$this->transient_cleaner->set_stored_transients(
			array(
				self::POST_TRANSIENT_KEY,
				self::TERM_TRANSIENT_KEY,
				self::OPTION_TRANSIENT_KEY,
			)
		);
	}

	/**
	 * Test set/get config.
	 *
	 * @return void
	 */
	public function test_set_get_config() {
		$config_1 = $this->transient_cleaner->get_config();
		$this->transient_cleaner->set_config(
			array_merge(
				$config_1,
				array( 'foo' => array() )
			)
		);
		$config_2 = $this->transient_cleaner->get_config();

		$this->assertIsArray( $config_2 );
		$this->assertNotEmpty( $config_2 );
		$this->assertEquals( count( $config_1 ), count( $config_2 ) - 1 );
	}

	/**
	 * Test set/get stored_transients.
	 *
	 * @return void
	 */
	public function test_set_get_stored_transients() {
		$stored_transients_1 = $this->transient_cleaner->get_stored_transients();
		$this->transient_cleaner->set_stored_transients(
			array( 'foo' )
		);
		$stored_transients_2 = $this->transient_cleaner->get_stored_transients();

		$this->assertEquals( array( 'foo' ), $stored_transients_2 );
	}

	/**
	 * Test merge_stored_transients_option_values.
	 *
	 * @return void
	 */
	public function test_merge_stored_transients_option_values() {
		$test_no_old_value                = $this->transient_cleaner->merge_stored_transients_option_values( 'foo', false );
		$test_old_value                   = $this->transient_cleaner->merge_stored_transients_option_values( 'foo', 'foo_old' );
		$test_merge_values                = $this->transient_cleaner->merge_stored_transients_option_values( array( 'foo' ), array( 'foo_old' ) );
		$test_merge_values_already_exists = $this->transient_cleaner->merge_stored_transients_option_values( array( 'foo' ), array( 'foo_old', 'foo' ) );

		$this->assertEquals( 'foo', $test_no_old_value );
		$this->assertEquals( 'foo_old', $test_old_value );
		$this->assertEquals( array( 'foo_old', 'foo' ), $test_merge_values );
		$this->assertEquals( array( 'foo_old', 'foo' ), $test_merge_values_already_exists );
	}

	/**
	 * Test store transient.
	 *
	 * @return void
	 */
	public function test_store_transient_key() {
		$test_fail    = $this->transient_cleaner->store_transient_key( 'foo' );
		$test_success = $this->transient_cleaner->store_transient_key( TransientCleaner::PREFIX . 'foo' );

		$this->assertFalse( $test_fail );
		$this->assertTrue( $test_success );
	}

	/**
	 * Test post transient cleaner.
	 *
	 * @return void
	 */
	public function test_post_transient_cleaner() {
		$post         = $this->factory->post->create_and_get();
		$test_success = $this->transient_cleaner->post_transient_cleaner( 1, $post );

		$config_1 = $this->transient_cleaner->get_config();
		unset( $config_1['post'] );
		$this->transient_cleaner->set_config( $config_1 );

		$test_fail = $this->transient_cleaner->post_transient_cleaner( 1, $post );

		$this->assertTrue( $test_success );
		$this->assertFalse( $test_fail );
	}

	/**
	 * Test term transient cleaner.
	 *
	 * @return void
	 */
	public function test_term_transient_cleaner() {
		$taxonomy     = 'post_tag';
		$test_success = $this->transient_cleaner->term_transient_cleaner( 1, 1, $taxonomy );

		$config_1 = $this->transient_cleaner->get_config();
		unset( $config_1['term'] );
		$this->transient_cleaner->set_config( $config_1 );

		$test_fail = $this->transient_cleaner->term_transient_cleaner( 1, 1, $taxonomy );

		$this->assertTrue( $test_success );
		$this->assertFalse( $test_fail );
	}

	/**
	 * Test option transient cleaner.
	 *
	 * @return void
	 */
	public function test_option_transient_cleaner() {
		$option       = 'baz_option';
		$test_success = $this->transient_cleaner->option_transient_cleaner( $option );

		$config_1 = $this->transient_cleaner->get_config();
		unset( $config_1['option'] );
		$this->transient_cleaner->set_config( $config_1 );

		$test_fail = $this->transient_cleaner->option_transient_cleaner( $option );

		$this->assertTrue( $test_success );
		$this->assertFalse( $test_fail );
	}
}
