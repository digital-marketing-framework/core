<?php

namespace DigitalMarketingFramework\Core\Registry\Service;

use DigitalMarketingFramework\Core\Queue\QueueInterface;

interface QueueRegistryInterface
{
    public function getPersistentQueue(): QueueInterface;
    public function setPersistentQueue(QueueInterface $queue): void;
    public function getNonPersistentQueue(): QueueInterface;
    public function setNonPersistentQueue(QueueInterface $queue): void;
}
