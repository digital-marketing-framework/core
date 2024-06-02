<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

interface ResourceServiceRegistryInterface
{
    public function getResourceService(string $identifier): ?ResourceServiceInterface;

    public function registerResourceService(ResourceServiceInterface $resourceService): void;
}
