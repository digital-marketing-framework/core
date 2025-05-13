<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Alert\AlertHandlerInterface;
use DigitalMarketingFramework\Core\Alert\AlertManagerInterface;

trait AlertRegistryTrait
{
    use PluginRegistryTrait;

    abstract public function createObject(string $class, array $arguments = []): object;

    public function getAlertManager(): AlertManagerInterface
    {
        return $this->getRegistryCollection()->getAlertManager();
    }

    public function setAlertManager(AlertManagerInterface $alertManager): void
    {
        $this->getRegistryCollection()->setAlertManager($alertManager);
    }

    public function registerAlertHandler(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(AlertHandlerInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteAlertHandler(string $keyword): void
    {
        $this->deletePlugin($keyword, AlertHandlerInterface::class);
    }

    public function getAlertHandler(string $keyword): ?AlertHandlerInterface
    {
        return $this->getPlugin($keyword, AlertHandlerInterface::class);
    }

    public function getAllAlertHandlers(): array
    {
        return $this->getAllPlugins(AlertHandlerInterface::class);
    }
}
