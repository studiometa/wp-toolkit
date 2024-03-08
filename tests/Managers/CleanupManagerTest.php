<?php

namespace Studiometa\WPToolkitTest;

use WP_UnitTestCase;
use Studiometa\WPToolkit\Managers\CleanupManager;

/**
 * CleanupManagerTest test case.
 */
class CleanupManagerTest extends WP_UnitTestCase
{

    /**
     * CleanupManager.
     *
     * @var CleanupManager
     */
    public $cleanup_manager;

    public function setUp():void
    {
        parent::setUp();

        $this->cleanup_manager = new CleanupManager();
    }

    /**
     * Test remove css and js version.
     *
     * @return void
     */
    public function test_remove_version_css_js()
    {
        $themes_uri = content_url('themes');
        $theme_src  = $themes_uri . '/example/example.js?ver=2.0.0';
        $other_src  = 'https://example.org/example.js?ver=2.0.0';

        $updated_theme_src = $this->cleanup_manager->remove_version_css_js($theme_src);
        $updated_other_src = $this->cleanup_manager->remove_version_css_js($other_src);

        $this->assertFalse(strpos($updated_other_src, 'ver='));
        $this->assertNotFalse(strpos($updated_theme_src, 'ver='));
    }

    /**
     * Test disable XML RPC.
     *
     * @return void
     */
    public function test_xml_rpc_disabled()
    {
        $this->cleanup_manager->run();
        $this->assertFalse(apply_filters('xmlrpc_enabled', true));
    }
}
