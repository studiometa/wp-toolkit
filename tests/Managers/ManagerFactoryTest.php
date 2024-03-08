<?php

namespace Studiometa\WPToolkitTest;

use WP_UnitTestCase;
use Studiometa\WPToolkit\Managers\ManagerInterface;
use Studiometa\WPToolkit\Managers\ManagerFactory;

/**
 * Dummy manager class.
 */
class Manager implements ManagerInterface
{
    public function run()
    {
        ManagerFactoryTest::$has_run = true;
    }
}

/**
 * ManagerFactoryTest test case.
 */
class ManagerFactoryTest extends WP_UnitTestCase
{
    /**
     * Has dummy manager has be run?
     *
     * @var boolean
     */
    public static $has_run = false;

    /**
     * Test init method.
     * Should trigger the manager instances `run` method.
     *
     * @return void
     */
    public function test_init()
    {
        $this->assertFalse(self::$has_run);

        ManagerFactory::init(
            array( new Manager() )
        );

        $this->assertTrue(self::$has_run);
    }
}
