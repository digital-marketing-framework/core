<?php

namespace DigitalMarketingFramework\Core\Cleanup;

use DigitalMarketingFramework\Core\Exception\DigitalMarketingFrameworkException;
use DigitalMarketingFramework\Core\Log\LoggerAwareInterface;
use DigitalMarketingFramework\Core\Log\LoggerAwareTrait;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;

class CleanupManager implements CleanupManagerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        protected RegistryInterface $registry,
    ) {
    }

    public function cleanup(): void
    {
        $tasks = $this->registry->getAllCleanupTasks();
        foreach ($tasks as $task) {
            try {
                $task->execute();
            } catch (DigitalMarketingFrameworkException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
