<?php

namespace Studiometa\WPToolkitTest\Sentry;

use PHPUnit\Framework\TestCase;
use Studiometa\WPToolkit\Sentry\Config;

class ConfigTest extends TestCase
{
    public $values =[
        'dsn'                  => 'dsn',
        'js_loader_script'     => 'js_loader_script',
        'environment'          => 'environment',
        'release'              => 'release',
        'traces_sample_rate'   => 0.0,
        'profiles_sample_rate' => 0.0,
    ];

    public function config()
    {
        return new Config(
            dsn: $this->values['dsn'],
            js_loader_script: $this->values['js_loader_script'],
            environment: $this->values['environment'],
            release: $this->values['release'],
            traces_sample_rate:  $this->values['traces_sample_rate'],
            profiles_sample_rate:  $this->values['profiles_sample_rate'],
        );
    }

    public function test_it_has_a_working_to_array_method()
    {
        $expected = $this->values;
        unset($expected['js_loader_script']);
        $this->assertEqualsCanonicalizing($expected, $this->config()->toArray());
    }

    public function test_it_has_a_js_config_method()
    {
        $expected = $this->values;
        unset($expected['js_loader_script']);
        unset($expected['dsn']);
        $js_config = json_decode($this->config()->getJsConfig(), true);
        $this->assertEqualsCanonicalizing($expected, $js_config);
    }
}
