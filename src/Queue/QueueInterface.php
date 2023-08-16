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

    public function fetch(array $status = [], int $limit = 0, int $offset = 0): array;
    public function fetchQueued(int $limit = 0, int $offset = 0): array;
    public function fetchPending(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;
    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;
    public function fetchPendingAndRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0): array;
    public function fetchDone(int $limit = 0, int $offset = 0): array;
    public function fetchFailed(int $limit = 0, int $offset = 0): array;

    public function markAsQueued(JobInterface $job): void;
    public function markAsPending(JobInterface $job): void;
    public function markAsRunning(JobInterface $job): void;
    public function markAsDone(JobInterface $job, bool $skipped = false): void;
    public function markAsFailed(JobInterface $job, string $message = ''): void;

    public function markListAsQueued(array $jobs): void;
    public function markListAsPending(array $jobs): void;
    public function markListAsRunning(array $jobs): void;
    public function markListAsDone(array $jobs, bool $skipped = false): void;
    public function markListAsFailed(array $jobs, string $message = ''): void;

    public function addJob(JobInterface $job): JobInterface;
    public function removeJob(JobInterface $job): void;

    public function removeOldJobs(int $minAgeInSeconds, array $status = []): void;
}
