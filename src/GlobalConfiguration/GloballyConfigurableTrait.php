<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

trait GloballyConfigurableTrait
{
    /** @var array<string,mixed> */
    protected array $globalSettings = [];

    abstract public function getPackageName(): string;

    /**
     * @param array<string,mixed> $globalSettings
     */
    public function injectGlobalSettings(array $globalSettings): void
    {
        $this->globalSettings = $globalSettings;
    }

    protected function getGlobalConfig(string $key, string $component = '', mixed $default = null): mixed
    {
        $config = $this->globalSettings;
        if ($component !== '') {
            $config = $config[$component] ?? [];
        }

        return $config[$key] ?? $default;
    }
}
