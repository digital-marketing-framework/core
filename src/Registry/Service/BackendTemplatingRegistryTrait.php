<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Backend\AssetUriBuilderInterface;
use DigitalMarketingFramework\Core\Backend\BackendManager;
use DigitalMarketingFramework\Core\Backend\BackendManagerInterface;
use DigitalMarketingFramework\Core\Backend\UriBuilderInterface;
use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;

trait BackendTemplatingRegistryTrait
{
    protected UriBuilderInterface $backendUriBuilder;

    protected AssetUriBuilderInterface $backendAssetUriBuilder;

    protected BackendManagerInterface $backendManager;

    public function getBackendUriBuilder(): UriBuilderInterface
    {
        if (!isset($this->backendUriBuilder)) {
            throw new DigitalMarketingFrameworkException('No backend URI builder found.');
        }

        return $this->backendUriBuilder;
    }

    public function setBackendUriBuilder(UriBuilderInterface $backendUriBuilder): void
    {
        $this->backendUriBuilder = $backendUriBuilder;
    }

    public function getBackendAssetUriBuilder(): AssetUriBuilderInterface
    {
        if (!isset($this->backendAssetUriBuilder)) {
            throw new DigitalMarketingFrameworkException('No backend asset URI builder found.');
        }

        return $this->backendAssetUriBuilder;
    }

    public function setBackendAssetUriBuilder(AssetUriBuilderInterface $backendAssetUriBuilder): void
    {
        $this->backendAssetUriBuilder = $backendAssetUriBuilder;
    }

    public function getBackendManager(): BackendManagerInterface
    {
        if (!isset($this->backendController)) {
            $this->backendController = $this->createObject(BackendManager::class, [$this]);
        }

        return $this->backendController;
    }

    public function setBackendManager(BackendManagerInterface $backendManager): void
    {
        $this->backendController = $backendManager;
    }
}
