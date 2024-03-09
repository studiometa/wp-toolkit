<?php

namespace Studiometa\WPToolkit\Sentry;

class Config {
    public function __construct(
        public string $dsn,
        public string $js_loader_script,
        public string $environment,
        public string $release,
        public float $traces_sample_rate,
        public float $profiles_sample_rate,
    )
    {}

    public function toArray(): array {
        return [
            'dsn'                 => $this->dsn,
            'environment'         => $this->environment,
            'release'             => $this->release,
            'traces_sample_rate'  => $this->traces_sample_rate,
            'profiles_sample_rate' => $this->profiles_sample_rate,
        ];
    }

    public function getJsConfig():string {
        $config = $this->toArray();
        unset($config['dsn']);
        return json_encode($config);
    }
}
