<?php

use Studiometa\WPToolkit\Builders\PostTypeBuilder;

/**
 * PostTypeBuilder test case.
 */
class PostTypeBuilderTest extends WP_UnitTestCase {
	/**
	 * Store a PostTypeBuilder instance.
	 */
	public function setUp():void {
		parent::setUp();

		$this->post_type_builder = new PostTypeBuilder( 'product' );
	}

	/**
	 * Test register function.
	 *
	 * @return void
	 */
	public function test_post_type_builder_register() {
		$this->post_type_builder->register();

		$this->assertTrue( post_type_exists( 'product' ) );
	}

	/**
	 * Test set_labels function.
	 *
	 * @return void
	 */
	public function test_post_type_builder_set_labels() {
		$this->post_type_builder->set_labels( 'Product', 'Products' );

		$config = $this->post_type_builder->get_config();

		$this->assertTrue( isset( $config['labels'] ) );
		$this->assertEquals( 'Product', $config['labels']['singular_name'] );
		$this->assertEquals( 'Products', $config['labels']['menu_name'] );
	}
}
