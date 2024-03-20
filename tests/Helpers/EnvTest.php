<?php

namespace Studiometa\WPToolkitTest;

use PHPUnit\Framework\TestCase;
use Studiometa\WPToolkit\Helpers\Env;
use function Studiometa\WPToolkit\env;

/**
 * EnvTest test case.
 */
class EnvTest extends TestCase
{

    /**
     * Test the request() function
     *
     * @return void
     */
    public function test_type_of_request_function_helper()
    {
        $this->assertTrue(is_string(env('missing')));
        $this->assertTrue(is_string(Env::get('missing')));
    }

    /**
     * Test the `env_is_...` functions and methods.
     *
     * @return void
     */
    public function test_env_is_functions()
    {
        $mapping = [
            'is_local'       => 'local',
            'is_prod'        => 'production',
            'is_prod'        => 'prod',
            'is_preprod'     => 'preprod',
            'is_development' => 'development',
            'is_staging'     => 'staging',
        ];

        foreach ($mapping as $name => $value) {
            $fn = sprintf('\Studiometa\WPToolkit\env_%s', $name);
            $_ENV['APP_ENV'] = strtolower($value);
            $this->assertTrue($fn());
            $this->assertTrue(Env::$name());
            $_ENV['APP_ENV'] = strtoupper($value);
            $this->assertTrue($fn());
            $this->assertTrue(Env::$name());
        }
    }
}
