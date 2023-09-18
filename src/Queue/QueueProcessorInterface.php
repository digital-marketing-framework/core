<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

interface QueueProcessorInterface
{
    public function processJob(JobInterface $job): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function processJobs(array $jobs): void;

    public function processBatch(int $batchSize = 1): void;

    public function processAll(): void;
}
