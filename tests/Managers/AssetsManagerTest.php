<?php

namespace Studiometa\WPToolkitTest;

use WP_UnitTestCase;
use Studiometa\WPToolkit\Managers\AssetsManager;

/**
 * CleanupManagerTest test case.
 */
class AssetsManagerTest extends WP_UnitTestCase
{
    public function set_up():void
    {
        parent::set_up();

        $this->assets_manager = new AssetsManager();

        // @todo put stubs file in current theme folder for tests
        $this->theme_path = realpath(get_template_directory());
        $this->stubs_path = realpath(__DIR__ . '/__stubs__/theme/');

		// phpcs:ignore
		exec( sprintf( 'rsync -avh %s %s', $this->stubs_path . '/', $this->theme_path . '/' ) );
        $this->assets_manager->run();
    }

    /**
     * Test remove css and js version.
     *
     * @return void
     */
    public function test_configs_are_read()
    {
        $this->assertEqualSets(
            $this->assets_manager->config,
            array(
                'all'  => array(
                    'entries' => array(
                        'css/app.scss',
                        'js/app.js',
                    ),
                    'css'     => array(
                        'editor' => 'dist/editor.css',
                    ),
                ),
                'post' => array(
                    'css' => array(
                        'post' => 'dist/post.css',
                    ),
                ),
            )
        );

        $this->assertTrue(
            $this->assets_manager->webpack_manifest instanceof \Studiometa\WebpackConfig\Manifest
        );

        $this->assertTrue(
            $this->assets_manager->webpack_manifest->entry('css/app') instanceof \Studiometa\WebpackConfig\Entry
        );

        $this->assertTrue(
            $this->assets_manager->webpack_manifest->entry('js/app') instanceof \Studiometa\WebpackConfig\Entry
        );
    }

    public function test_assets_are_registered()
    {
        do_action('wp_enqueue_scripts');
        $this->assertTrue(isset(wp_styles()->registered['theme-styles-1234-css']));
        $this->assertTrue(isset(wp_styles()->registered['theme-editor']));
        $this->assertTrue(isset(wp_scripts()->registered['theme-app-1234-js']));
    }

    public function test_entries_are_enqueued()
    {
        apply_filters('template_include', 'frontpage');
        do_action('wp_enqueue_scripts');

        $this->assertTrue(in_array('theme-styles-1234-css', wp_styles()->queue, true));
        $this->assertTrue(in_array('theme-editor', wp_styles()->queue, true));
        $this->assertFalse(in_array('theme-post', wp_styles()->queue, true));

        $this->assertTrue(in_array('theme-app-1234-js', wp_scripts()->queue, true));
    }

    public function test_entries_are_enqueued_by_template()
    {
        apply_filters('template_include', 'single-post.php');
        do_action('wp_enqueue_scripts');

        $this->assertTrue(in_array('theme-styles-1234-css', wp_styles()->queue, true));
        $this->assertTrue(in_array('theme-editor', wp_styles()->queue, true));
        $this->assertTrue(in_array('theme-post', wp_styles()->queue, true));
    }
}
