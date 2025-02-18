<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration\Settings;

interface GlobalSettingsInterface
{
    public function getPackageName(): string;

    public function getComponentName(): string;

    /**
     * @param array<string,mixed> $settings
     */
    public function injectSettings(array $settings): void;

    public function get(string $key, mixed $default = null): mixed;
}
