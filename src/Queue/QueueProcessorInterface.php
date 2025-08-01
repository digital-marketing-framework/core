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

    public function processBatch(): void;

    public function processAll(): void;

    public function updateStuckJobsStatus(): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function updateFailedJobs(array $jobs = []): void;

    public function updateJobsAndProcessBatch(): void;

    public function cleanupJobs(): void;
}
