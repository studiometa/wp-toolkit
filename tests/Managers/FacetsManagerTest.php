<?php

namespace Studiometa\WPToolkitTest;

use WP_Query;
use WP_UnitTestCase;
use Studiometa\WPToolkit\Managers\FacetsManager;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\TwigFunction;

use function Studiometa\WPToolkit\request;

/**
 * FacetsManagerTest test case.
 */
class FacetsManagerTest extends WP_UnitTestCase
{
    public function test_it_should_add_query_vars_to_wp_query()
    {
        request()->query->set('facets', ['cat' => 1]);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[cat]=1');
        global $wp_query;

        $this->assertTrue($wp_query instanceof WP_Query);
        $this->assertTrue($wp_query->query_vars['cat'] === 1);
    }

    public function test_it_should_not_add_query_vars_to_wp_query()
    {
        request()->query->remove('facets');
        (new FacetsManager())->run();
        $this->go_to('/?facets[cat]=1');
        global $wp_query;

        $this->assertTrue($wp_query instanceof WP_Query);
        $this->assertFalse($wp_query->query_vars['cat'] === 1);
    }

    public function test_it_should_add_a_twig_helper_function()
    {
        request()->query->set('facets', ['cat' => 2]);
        $loader = new ArrayLoader();
        $twig = new Environment($loader);
        $manager = new FacetsManager();
        $twig = $manager->add_twig_helpers($twig);
        $function = $twig->getFunction('facets_get');
        $this->assertTrue($function instanceof TwigFunction);
        $this->assertTrue($function->getCallable()('cat') === 2);
    }

    public function test_it_should_handle_taxonomy_queries_with_slugs()
    {
        // Register a test taxonomy
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy' => 'slug1,slug2']);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy]=slug1,slug2');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertIsArray($tax_query);
        $this->assertCount(1, $tax_query);
        $this->assertEquals('test_taxonomy', $tax_query[0]['taxonomy']);
        $this->assertEquals('IN', $tax_query[0]['operator']);
        $this->assertEquals('slug', $tax_query[0]['field']);
        $this->assertEquals(['slug1', 'slug2'], $tax_query[0]['terms']);
    }

    public function test_it_should_handle_taxonomy_queries_with_ids()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy' => '1,2,3']);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy]=1,2,3');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertEquals('term_id', $tax_query[0]['field']);
        $this->assertEquals([1, 2, 3], $tax_query[0]['terms']);
    }

    public function test_it_should_handle_taxonomy_not_in_queries()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy__not_in' => 'excluded-slug']);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy__not_in]=excluded-slug');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertEquals('test_taxonomy', $tax_query[0]['taxonomy']);
        $this->assertEquals('NOT IN', $tax_query[0]['operator']);
        $this->assertEquals(['excluded-slug'], $tax_query[0]['terms']);
    }

    public function test_it_should_handle_taxonomy_and_queries()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy__and' => 'slug1,slug2']);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy__and]=slug1,slug2');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertEquals('AND', $tax_query[0]['operator']);
    }

    public function test_it_should_handle_taxonomy_exists_queries()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy__exists' => '1']);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy__exists]=1');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertEquals('test_taxonomy', $tax_query[0]['taxonomy']);
        $this->assertEquals('EXISTS', $tax_query[0]['operator']);
        $this->assertArrayNotHasKey('terms', $tax_query[0]);
        $this->assertArrayNotHasKey('field', $tax_query[0]);
    }

    public function test_it_should_combine_taxonomy_and_regular_queries()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', [
            'test_taxonomy' => 'test-slug',
            'meta_key' => 'custom_field',
            'posts_per_page' => 5
        ]);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy]=test-slug&facets[meta_key]=custom_field&facets[posts_per_page]=5');
        global $wp_query;

        // Check taxonomy query
        $tax_query = $wp_query->get('tax_query');
        $this->assertIsArray($tax_query);
        $this->assertEquals('test_taxonomy', $tax_query[0]['taxonomy']);

        // Check regular query vars
        $this->assertEquals('custom_field', $wp_query->query_vars['meta_key']);
        $this->assertEquals(5, $wp_query->query_vars['posts_per_page']);
    }

    public function test_it_should_handle_existing_tax_query()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy' => 'new-slug']);
        $manager = new FacetsManager();
        $manager->run();

        // Set up existing tax_query
        $existing_tax_query = [
            [
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => ['existing-category'],
                'operator' => 'IN'
            ]
        ];

        $this->go_to('/?facets[test_taxonomy]=new-slug');
        global $wp_query;
        $wp_query->set('tax_query', $existing_tax_query);

        // Trigger the facets query modification
        $manager->add_facets_to_query($wp_query);

        $tax_query = $wp_query->get('tax_query');
        $this->assertCount(2, $tax_query);
        $this->assertEquals('test_taxonomy', $tax_query[0]['taxonomy']);
        $this->assertEquals('category', $tax_query[1][0]['taxonomy']);
    }

    public function test_it_should_handle_array_values_in_facets()
    {
        register_taxonomy('test_taxonomy', 'post', ['public' => true]);

        request()->query->set('facets', ['test_taxonomy' => ['slug1', 'slug2']]);
        $manager = new FacetsManager();
        $manager->run();

        $this->go_to('/?facets[test_taxonomy][]=slug1&facets[test_taxonomy][]=slug2');
        global $wp_query;

        $tax_query = $wp_query->get('tax_query');
        $this->assertEquals(['slug1', 'slug2'], $tax_query[0]['terms']);
        $this->assertEquals('slug', $tax_query[0]['field']);
    }
}
