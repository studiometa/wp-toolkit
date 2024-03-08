<?php

namespace Studiometa\WPToolkitTest;

use WP_UnitTestCase;
use Studiometa\WPToolkit\Builders\TaxonomyBuilder;

/**
 * TaxonomyBuilder test case.
 */
class TaxonomyBuilderTest extends WP_UnitTestCase
{
    /**
     * Store a TaxonomyBuilder instance.
     */
    public function setUp():void
    {
        parent::setUp();

        $this->taxonomy_builder = new TaxonomyBuilder('category');
    }

    /**
     * Test register function.
     *
     * @return void
     */
    public function test_taxonomy_builder_register()
    {
        $this->taxonomy_builder->register();

        $this->assertTrue(taxonomy_exists('category'));
    }

    /**
     * Test set_labels function.
     *
     * @return void
     */
    public function test_taxonomy_builder_set_labels()
    {
        $this->taxonomy_builder->set_labels('Category', 'Categories');

        $config = $this->taxonomy_builder->get_config();

        $this->assertTrue(isset($config['labels']));
        $this->assertEquals('Category', $config['labels']['singular_name']);
        $this->assertEquals('Categories', $config['labels']['menu_name']);
    }

    /**
     * Test set_post_types function.
     *
     * @return void
     */
    public function test_taxonomy_builder_set_post_types()
    {
        $this->taxonomy_builder->set_post_types('tag');

        $post_types = $this->taxonomy_builder->get_post_types();

        $this->assertEquals('tag', $post_types);
    }
}
