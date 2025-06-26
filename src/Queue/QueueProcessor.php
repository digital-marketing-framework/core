<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareInterface;
use DigitalMarketingFramework\Core\GlobalConfiguration\GlobalConfigurationAwareTrait;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerAwareTrait;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Settings\QueueSettings;

class QueueProcessor implements QueueProcessorInterface, GlobalConfigurationAwareInterface, NotificationManagerAwareInterface
{
    use GlobalConfigurationAwareTrait;
    use NotificationManagerAwareTrait;

    public function __construct(
        protected QueueInterface $queue,
        protected WorkerInterface $worker,
        protected QueueSettings $queueSettings,
    ) {
    }

    protected function markJobAsFailed(JobInterface $job, string $message, bool $preserveTimestamp = false): void
    {
        $this->queue->markAsFailed($job, $message, $preserveTimestamp);
        if (!$this->queueSettings->rerunFailedJobEnabled() || $job->getRetryAmount() === 0) {
            $this->notificationManager->notify(
                sprintf('Anyrel distribution job %s failed', $job->getLabel()),
                $message,
                $job->getEnvironment(),
                component: 'queue-processor',
                level: NotificationManagerInterface::LEVEL_ERROR
            );
        }
    }

    public function processJob(JobInterface $job): void
    {
        try {
            $this->queue->markAsRunning($job);
            $processed = $this->worker->processJob($job);
            $this->queue->markAsDone($job, !$processed);
        } catch (QueueException $e) {
            $this->markJobAsFailed($job, $e->getMessage());
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

    public function processBatch(): void
    {
        $batchSize = $this->queueSettings->getBatchSize();
        $this->processJobs($this->queue->fetchQueued($batchSize));
    }

    public function processAll(): void
    {
        $this->processJobs($this->queue->fetchQueued());
    }

    public function updateStuckJobsStatus(): void
    {
        if (!$this->queueSettings->recogniseStuckJobs()) {
            return;
        }

        $maxExecutionTime = $this->queueSettings->getMaximumExecutionTime();
        $pendingStuckJobs = $this->queue->fetchPending(minTimeSinceChangedInSeconds: $maxExecutionTime);
        if ($pendingStuckJobs !== []) {
            $this->queue->markListAsQueued($pendingStuckJobs);
        }

        $runningStuckJobs = $this->queue->fetchRunning(minTimeSinceChangedInSeconds: $maxExecutionTime);
        if ($runningStuckJobs !== []) {
            $message = sprintf('Assumed to be stuck after %d seconds.', $maxExecutionTime);
            foreach ($runningStuckJobs as $job) {
                $this->markJobAsFailed($job, $message, true);
            }
        }
    }

    public function updateFailedJobs(array $jobs = []): void
    {
        if (!$this->queueSettings->rerunFailedJobEnabled()) {
            return;
        }

        if ($jobs === []) {
            $jobs = $this->queue->fetchFailed();
        }

        if ($jobs !== []) {
            $delay = $this->queueSettings->getRerunFailedJobDelay();
            $maxChangedTime = time() - $delay;
            foreach ($jobs as $job) {
                $retryAmount = $job->getRetryAmount();
                if ($retryAmount > 0 && $job->getChanged()->getTimestamp() < $maxChangedTime) {
                    --$retryAmount;
                    $job->setRetryAmount($retryAmount);
                    $this->queue->markAsQueued($job);
                }
            }
        }
    }

    public function updateJobsAndProcessBatch(): void
    {
        $this->updateStuckJobsStatus();
        $this->updateFailedJobs();
        $this->processBatch();
    }

    public function cleanupJobs(): void
    {
        $expirationTime = $this->queueSettings->getExpirationTime() * 24 * 3600;
        $status = $this->queueSettings->cleanupDoneJobsOnly() ? [QueueInterface::STATUS_DONE] : [];
        $this->queue->removeOldJobs($expirationTime, $status);
    }
}
