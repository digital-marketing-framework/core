<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

interface QueueInterface
{
    public const STATUS_QUEUED = 0;

    public const STATUS_PENDING = 1;

    public const STATUS_RUNNING = 2;

    public const STATUS_DONE = 3;

    public const STATUS_FAILED = 4;

    /**
     * @param array<int> $status
     *
     * @return array<JobInterface>
     */
    public function fetch(array $status = [], int $limit = 0, int $offset = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchQueued(int $limit = 0, int $offset = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchPending(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchPendingAndRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchDone(int $limit = 0, int $offset = 0): array;

    /**
     * @return array<JobInterface>
     */
    public function fetchFailed(int $limit = 0, int $offset = 0): array;

    public function markAsQueued(JobInterface $job): void;

    public function markAsPending(JobInterface $job): void;

    public function markAsRunning(JobInterface $job): void;

    public function markAsDone(JobInterface $job, bool $skipped = false): void;

    public function markAsFailed(JobInterface $job, string $message = ''): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function markListAsQueued(array $jobs): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function markListAsPending(array $jobs): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function markListAsRunning(array $jobs): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function markListAsDone(array $jobs, bool $skipped = false): void;

    /**
     * @param array<JobInterface> $jobs
     */
    public function markListAsFailed(array $jobs, string $message = ''): void;

    public function addJob(JobInterface $job): JobInterface;

    public function removeJob(JobInterface $job): void;

    /**
     * @param array<int> $status
     */
    public function removeOldJobs(int $minAgeInSeconds, array $status = []): void;
}
