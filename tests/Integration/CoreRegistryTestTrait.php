<?php

namespace DigitalMarketingFramework\Core\Tests\Integration;

use DigitalMarketingFramework\Core\Registry\Registry;
use DigitalMarketingFramework\Core\Registry\RegistryCollection;
use DigitalMarketingFramework\Core\Registry\RegistryCollectionInterface;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

trait CoreRegistryTestTrait
{
    use RegistryTestTrait;

    protected RegistryCollectionInterface $registryCollection;

    protected RegistryInterface $registry;

    protected function createRegistry(): void
    {
        $this->registryCollection = new RegistryCollection();
        $this->registry = new Registry();
        $this->registry->setRegistryCollection($this->registryCollection);
    }
}
