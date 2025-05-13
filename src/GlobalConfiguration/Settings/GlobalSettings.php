<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

abstract class GlobalSettings implements GlobalSettingsInterface
{
    /**
     * @var array<string,mixed>
     */
    protected array $settings = [];

    public function __construct(
        protected string $packageName,
        protected string $component = '',
    ) {
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    public function getComponentName(): string
    {
        return $this->component;
    }

    public function injectSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }
}
