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
}
