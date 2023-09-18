<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

interface WorkerInterface
{
    /**
     * @throws QueueException
     */
    public function processJob(JobInterface $job): bool;
}
