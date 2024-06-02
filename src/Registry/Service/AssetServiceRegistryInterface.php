<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Resource\Asset\AssetServiceInterface;

interface AssetServiceRegistryInterface
{
    public function setAssetService(AssetServiceInterface $service): void;

    public function getAssetService(): AssetServiceInterface;
}
