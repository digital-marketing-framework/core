<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\Asset\AssetService;
use DigitalMarketingFramework\Core\Resource\Asset\AssetServiceInterface;

trait AssetServiceRegistryTrait
{
    protected AssetServiceInterface $assetService;

    /**
     * @template ObjectClass of object
     *
     * @param class-string<ObjectClass> $class
     * @param array<mixed> $arguments
     *
     * @return ObjectClass
     */
    abstract public function createObject(string $class, array $arguments = []): object;

    public function setAssetService(AssetServiceInterface $assetService): void
    {
        $this->assetService = $assetService;
    }

    public function getAssetService(): AssetServiceInterface
    {
        if (!isset($this->assetService)) {
            $this->assetService = $this->createObject(AssetService::class, [$this]);
        }

        return $this->assetService;
    }
}
