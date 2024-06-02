<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\ResourceServiceInterface;

trait ResourceServiceRegistryTrait
{
    /** @var array<string,ResourceServiceInterface> */
    protected array $resourceServices = [];

    public function getResourceService(string $identifier): ?ResourceServiceInterface
    {
        $idParts = explode(':', $identifier);
        if (count($idParts) >= 2) {
            $prefix = $idParts[0];
            if (isset($this->resourceServices[$prefix]) && $this->resourceServices[$prefix]->resourceIdentifierMatch($identifier)) {
                return $this->resourceServices[$prefix];
            }
        }

        foreach ($this->resourceServices as $assetService) {
            if ($assetService->resourceIdentifierMatch($identifier)) {
                return $assetService;
            }
        }

        return null;
    }

    public function registerResourceService(ResourceServiceInterface $resourceService): void
    {
        $this->resourceServices[$resourceService->getIdentifierPrefix()] = $resourceService;
    }
}
