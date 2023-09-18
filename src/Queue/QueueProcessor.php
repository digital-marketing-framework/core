<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

class QueueProcessor implements QueueProcessorInterface
{
    public function __construct(
        protected QueueInterface $queue,
        protected WorkerInterface $worker,
    ) {
    }

    public function processJob(JobInterface $job): void
    {
        try {
            $this->queue->markAsRunning($job);
            $processed = $this->worker->processJob($job);
            $this->queue->markAsDone($job, !$processed);
        } catch (QueueException $e) {
            $this->queue->markAsFailed($job, $e->getMessage());
        }
    }

    public function processJobs(array $jobs): void
    {
        if ($jobs !== []) {
            $this->queue->markListAsPending($jobs);
            foreach ($jobs as $job) {
                $this->processJob($job);
            }
        }
    }

    public function processBatch(int $batchSize = 1): void
    {
        $this->processJobs($this->queue->fetchQueued($batchSize));
    }

    public function processAll(): void
    {
        $this->processJobs($this->queue->fetchQueued());
    }
}
