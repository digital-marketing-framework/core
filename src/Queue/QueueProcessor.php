<?php

namespace DigitalMarketingFramework\Core\Queue;

class QueueProcessor implements QueueProcessorInterface
{
    public function __construct(
        protected QueueInterface $queue,
        protected WorkerInterface $worker,
    ) {
    }

    public function processJobs(array $jobs): void
    {
        if (!empty($jobs)) {
            $this->queue->markListAsRunning($jobs);
            foreach ($jobs as $job) {
                try {
                    $processed = $this->worker->processJob($job);
                    $this->queue->markAsDone($job, !$processed);
                } catch (QueueException $e) {
                    $this->queue->markAsFailed($job, $e->getMessage());
                }
            }
        }
    }

    public function processBatch(int $batchSize = 1): void
    {
        $this->processJobs($this->queue->fetchPending($batchSize));
    }

    public function processAll(): void
    {
        $this->processJobs($this->queue->fetchPending());
    }
}
