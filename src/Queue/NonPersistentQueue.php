<?php

namespace DigitalMarketingFramework\Core\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

class NonPersistentQueue implements QueueInterface
{
    /** @var array<JobInterface> */
    protected array $queue = [];

    protected int $index = 1;

    /**
     * @param array<int> $status
     *
     * @return array<JobInterface>
     */
    public function fetchWhere(array $status = [], int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0, int $minAgeInSeconds = 0): array
    {
        $result = [];
        $now = new DateTime();
        $count = 0;
        foreach ($this->queue as $job) {
            if ($status !== [] && !in_array($job->getStatus(), $status)) {
                continue;
            }

            if ($minTimeSinceChangedInSeconds > 0 && $now->getTimestamp() - $job->getChanged()->getTimestamp() < $minTimeSinceChangedInSeconds) {
                continue;
            }

            if ($minAgeInSeconds > 0 && $now->getTimestamp() - $job->getCreated()->getTimestamp() < $minAgeInSeconds) {
                continue;
            }

            ++$count;
            if ($count > $offset) {
                $result[] = $job;
            }

            if ($limit > 0 && ($count - $offset) >= $limit) {
                break;
            }
        }

        return $result;
    }

    public function fetch(array $status = [], int $limit = 0, int $offset = 0): array
    {
        return $this->fetchWhere($status, $limit, $offset);
    }

    public function fetchQueued(int $limit = 0, int $offset = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_QUEUED], $limit, $offset);
    }

    public function fetchPending(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_PENDING], $limit, $offset, $minTimeSinceChangedInSeconds);
    }

    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_RUNNING], $limit, $offset, $minTimeSinceChangedInSeconds);
    }

    public function fetchPendingAndRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_PENDING, QueueInterface::STATUS_RUNNING], $limit, $offset, $minTimeSinceChangedInSeconds);
    }

    public function fetchDone(int $limit = 0, int $offset = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_DONE], $limit, $offset);
    }

    public function fetchFailed(int $limit = 0, int $offset = 0): array
    {
        return $this->fetchWhere([QueueInterface::STATUS_FAILED], $limit, $offset);
    }

    public function markAs(JobInterface $job, int $status, string $message = '', bool $skipped = false): void
    {
        $job->setStatus($status);
        $job->setChanged(new DateTime());
        $job->setStatusMessage($message);
        $job->setSkipped($skipped);
    }

    public function markAsQueued(JobInterface $job): void
    {
        $this->markAs($job, QueueInterface::STATUS_QUEUED);
    }

    public function markAsPending(JobInterface $job): void
    {
        $this->markAs($job, QueueInterface::STATUS_PENDING);
    }

    public function markAsRunning(JobInterface $job): void
    {
        $this->markAs($job, QueueInterface::STATUS_RUNNING);
    }

    public function markAsDone(JobInterface $job, bool $skipped = false): void
    {
        $this->markAs($job, QueueInterface::STATUS_DONE, '', $skipped);
    }

    public function markAsFailed(JobInterface $job, string $message = ''): void
    {
        $this->markAs($job, QueueInterface::STATUS_FAILED, $message);
    }

    public function markListAsQueued(array $jobs): void
    {
        foreach ($jobs as $job) {
            $this->markAsQueued($job);
        }
    }

    public function markListAsPending(array $jobs): void
    {
        foreach ($jobs as $job) {
            $this->markAsPending($job);
        }
    }

    public function markListAsRunning(array $jobs): void
    {
        foreach ($jobs as $job) {
            $this->markAsRunning($job);
        }
    }

    public function markListAsDone(array $jobs, bool $skipped = false): void
    {
        foreach ($jobs as $job) {
            $this->markAsDone($job, $skipped);
        }
    }

    public function markListAsFailed(array $jobs, string $message = ''): void
    {
        foreach ($jobs as $job) {
            $this->markAsFailed($job, $message);
        }
    }

    public function addJob(JobInterface $job): JobInterface
    {
        if (!in_array($job, $this->queue)) {
            $job->setRouteId((string)$this->index++);
            $this->queue[] = $job;
        }

        return $job;
    }

    public function removeJob(JobInterface $job): void
    {
        $this->queue = array_filter(
            $this->queue,
            static function ($a) use ($job) {
                return $a !== $job;
            }
        );
    }

    public function removeOldJobs(int $minAgeInSeconds, array $status = []): void
    {
        $jobs = $this->fetchWhere($status, 0, 0, 0, $minAgeInSeconds);
        foreach ($jobs as $job) {
            $this->removeJob($job);
        }
    }
}
