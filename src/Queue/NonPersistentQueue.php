<?php

namespace DigitalMarketingFramework\Core\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

class NonPersistentQueue implements QueueInterface
{
    protected array $queue = [];
    protected int $index = 1;

    public function fetchWhere(array $status = [], int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0, int $minAgeInSeconds = 0)
    {
        $result = [];
        $now = new DateTime();
        $count = 0;
        /** @var JobInterface $job */
        foreach ($this->queue as $job) {
            if (!empty($status) && !in_array($job->getStatus(), $status)) {
                continue;
            }
            if ($minTimeSinceChangedInSeconds > 0 && $now->getTimestamp() - $job->getChanged()->getTimestamp() < $minTimeSinceChangedInSeconds) {
                continue;
            }
            if ($minAgeInSeconds > 0 && $now->getTimestamp() - $job->getCreated()->getTimestamp() < $minAgeInSeconds) {
                continue;
            }
            $count++;
            if ($count > $offset) {
                $result[] = $job;
            }
            if ($limit > 0 && ($count - $offset) >= $limit) {
                break;
            }
        }
        return $result;
    }

    public function fetch(array $status = [], int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere($status, $limit, $offset);
    }

    public function fetchPending(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_PENDING], $limit, $offset);
    }

    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_RUNNING], $limit, $offset, $minTimeSinceChangedInSeconds);
    }

    public function fetchDone(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_DONE], $limit, $offset);
    }

    public function fetchFailed(int $limit = 0, int $offset = 0)
    {
        return $this->fetchWhere([QueueInterface::STATUS_FAILED], $limit, $offset);
    }

    public function markAs(JobInterface $job, int $status, string $message = '', bool $skipped = false)
    {
        $job->setStatus($status);
        $job->setChanged(new DateTime());
        $job->setStatusMessage($message);
        $job->setSkipped($skipped);
    }

    public function markAsPending(JobInterface $job)
    {
        $this->markAs($job, QueueInterface::STATUS_PENDING);
    }

    public function markAsRunning(JobInterface $job)
    {
        $this->markAs($job, QueueInterface::STATUS_RUNNING);
    }

    public function markAsDone(JobInterface $job, bool $skipped = false)
    {
        $this->markAs($job, QueueInterface::STATUS_DONE, '', $skipped);
    }

    public function markAsFailed(JobInterface $job, string $message = '')
    {
        $this->markAs($job, QueueInterface::STATUS_FAILED, $message);
    }

    public function markListAsRunning(array $jobs)
    {
        foreach ($jobs as $job) {
            $this->markAsRunning($job);
        }
    }

    public function markListAsDone(array $jobs, bool $skipped = false)
    {
        foreach ($jobs as $job) {
            $this->markAsDone($job, $skipped);
        }
    }

    public function markListAsFailed(array $jobs, string $message = '')
    {
        foreach ($jobs as $job) {
            $this->markAsFailed($job, $message);
        }
    }

    public function addJob(JobInterface $job)
    {
        if (array_search($job, $this->queue) === false) {
            $job->setId($this->index++);
            $this->queue[] = $job;
        }
    }

    public function removeJob(JobInterface $job)
    {
        $this->queue = array_filter(
            $this->queue,
            function ($a) use ($job) { return $a !== $job; }
        );
    }

    public function removeOldJobs(int $minAgeInSeconds, array $status = [])
    {
        $jobs = $this->fetchWhere($status, 0, 0, 0, $minAgeInSeconds);
        foreach ($jobs as $job) {
            $this->removeJob($job);
        }
    }
}
