<?php

namespace DigitalMarketingFramework\Core\Registry\Plugin;

use DigitalMarketingFramework\Core\Cleanup\CleanupManagerInterface;
use DigitalMarketingFramework\Core\Cleanup\CleanupTaskInterface;

interface CleanupRegistryInterface
{
    public function setCleanupManager(CleanupManagerInterface $cleanupManager): void;

    public function getCleanupManager(): CleanupManagerInterface;

    /**
     * @param array<mixed> $additionalArguments
     */
    public function registerCleanupTask(string $class, array $additionalArguments = [], string $keyword = ''): void;

    public function deleteCleanupTask(string $keyword): void;

    public function getCleanupTask(string $keyword): ?CleanupTaskInterface;

    /**
     * @return array<CleanupTaskInterface>
     */
    public function getAllCleanupTasks(): array;
}
