<?php

namespace DigitalMarketingFramework\Core\Tests\Service;

use DigitalMarketingFramework\Core\GlobalConfiguration\GloballyConfigurableInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GloballyConfigurableTrait;

class GloballyConfigurableObject implements GloballyConfigurableInterface
{
    use GloballyConfigurableTrait;

    public function __construct(
        protected string $packageName,
    ) {
    }

    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return array<string,mixed>
     */
    public function getGloballyConfiguredData(): array
    {
        return $this->globalSettings;
    }

    public function testGlobalConfig(string $key, string $component = '', mixed $default = null): mixed
    {
        return $this->getGlobalConfig($key, $component, $default);
    }
}
