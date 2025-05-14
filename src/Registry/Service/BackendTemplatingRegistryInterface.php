<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Backend\AssetUriBuilderInterface;
use DigitalMarketingFramework\Core\Backend\BackendManagerInterface;
use DigitalMarketingFramework\Core\Backend\UriBuilderInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

interface BackendTemplatingRegistryInterface
{
    public function getBackendUriBuilder(): UriBuilderInterface;

    public function setBackendUriBuilder(UriBuilderInterface $backendUriBuilder): void;

    public function getBackendAssetUriBuilder(): AssetUriBuilderInterface;

    public function setBackendAssetUriBuilder(AssetUriBuilderInterface $backendAssetUriBuilder): void;

    public function getBackendManager(): BackendManagerInterface;

    public function setBackendManager(BackendManagerInterface $backendManager): void;
}
