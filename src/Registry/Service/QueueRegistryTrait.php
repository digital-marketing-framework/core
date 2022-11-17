<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Queue\QueueInterface;

trait QueueRegistryTrait
{
    protected QueueInterface $persistentQueue;
    protected QueueInterface $nonPersistentQueue;

    public function getPersistentQueue(): QueueInterface
    {
        return $this->persistentQueue;
    }

    public function setPersistentQueue(QueueInterface $queue): void
    {
        $this->persistentQueue = $queue;
    }

    public function getNonPersistentQueue(): QueueInterface
    {
        return $this->nonPersistentQueue;
    }

    public function setNonPersistentQueue(QueueInterface $queue): void
    {
        $this->nonPersistentQueue = $queue;
    }
}
