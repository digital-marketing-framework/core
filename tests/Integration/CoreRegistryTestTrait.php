<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Registry\Registry;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

trait CoreRegistryTestTrait
{
    use RegistryTestTrait;

    protected RegistryInterface $registry;

    protected function createRegistry(): void
    {
        $this->registry = new Registry();
    }
}
