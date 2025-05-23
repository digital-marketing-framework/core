<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Backend\Controller\AjaxController\AjaxControllerInterface;
use DigitalMarketingFramework\Core\Backend\Controller\SectionController\SectionControllerInterface;

trait BackendControllerRegistryTrait
{
    use PluginRegistryTrait;

    public function registerBackendSectionController(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(SectionControllerInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteBackendSectionController(string $keyword): void
    {
        $this->deletePlugin($keyword, SectionControllerInterface::class);
    }

    public function getBackendSectionController(string $keyword): ?SectionControllerInterface
    {
        return $this->getPlugin($keyword, SectionControllerInterface::class);
    }

    public function getAllBackendSectionControllers(): array
    {
        return $this->getAllPlugins(SectionControllerInterface::class);
    }

    public function registerBackendAjaxController(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(AjaxControllerInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteBackendAjaxController(string $keyword): void
    {
        $this->deletePlugin($keyword, AjaxControllerInterface::class);
    }

    public function getBackendAjaxController(string $keyword): ?AjaxControllerInterface
    {
        return $this->getPlugin($keyword, AjaxControllerInterface::class);
    }

    public function getAllBackendAjaxControllers(): array
    {
        return $this->getAllPlugins(AjaxControllerInterface::class);
    }
}
