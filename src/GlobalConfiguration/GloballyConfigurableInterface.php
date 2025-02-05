<?php

namespace DigitalMarketingFramework\Core\GlobalConfiguration;

interface GloballyConfigurableInterface
{
    public function getPackageName(): string;

    /**
     * @param array<string,mixed> $globalSettings
     */
    public function injectGlobalSettings(array $globalSettings): void;
}
