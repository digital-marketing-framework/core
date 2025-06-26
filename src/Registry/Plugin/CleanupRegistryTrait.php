<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Cleanup\CleanupManager;
use DigitalMarketingFramework\Core\Cleanup\CleanupManagerInterface;
use DigitalMarketingFramework\Core\Cleanup\CleanupTaskInterface;

trait CleanupRegistryTrait
{
    use PluginRegistryTrait;

    protected CleanupManagerInterface $cleanupManager;

    public function setCleanupManager(CleanupManagerInterface $cleanupManager): void
    {
        $this->cleanupManager = $cleanupManager;
    }

    public function getCleanupManager(): CleanupManagerInterface
    {
        if (!isset($this->cleanupManager)) {
            $this->cleanupManager = $this->createObject(CleanupManager::class, [$this]);
        }

        return $this->cleanupManager;
    }

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerCleanupTask(string $class, array $additionalArguments = [], string $keyword = ''): void
    {
        $this->registerPlugin(CleanupTaskInterface::class, $class, $additionalArguments, $keyword);
    }

    public function deleteCleanupTask(string $keyword): void
    {
        $this->deletePlugin($keyword, CleanupTaskInterface::class);
    }

    public function getCleanupTask(string $keyword): ?CleanupTaskInterface
    {
        return $this->getPlugin($keyword, CleanupTaskInterface::class);
    }

    /**
     * @return array<CleanupTaskInterface>
     */
    public function getAllCleanupTasks(): array
    {
        return $this->getAllPlugins(CleanupTaskInterface::class);
    }
}
