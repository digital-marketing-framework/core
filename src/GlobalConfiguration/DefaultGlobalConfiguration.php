<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

class DefaultGlobalConfiguration implements GlobalConfigurationInterface
{
    /** @var array<string,mixed> */
    protected array $config = [];

    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }
}
