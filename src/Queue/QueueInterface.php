<?php

namespace DigitalMarketingFramework\Core\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;

interface QueueInterface
{
    public const STATUS_PENDING = 1;
    public const STATUS_RUNNING = 2;
    public const STATUS_DONE = 3;
    public const STATUS_FAILED = 4;

    public function fetch(array $status = [], int $limit = 0, int $offset = 0);
    public function fetchPending(int $limit = 0, int $offset = 0);
    public function fetchRunning(int $limit = 0, int $offset = 0, int $minTimeSinceChangedInSeconds = 0);
    public function fetchDone(int $limit = 0, int $offset = 0);
    public function fetchFailed(int $limit = 0, int $offset = 0);

    public function markAsPending(JobInterface $job);
    public function markAsRunning(JobInterface $job);
    public function markAsDone(JobInterface $job, bool $skipped = false);
    public function markAsFailed(JobInterface $job, string $message = '');

    public function markListAsRunning(array $jobs);
    public function markListAsDone(array $jobs, bool $skipped = false);
    public function markListAsFailed(array $jobs, string $message = '');

    public function addJob(JobInterface $job);
    public function removeJob(JobInterface $job);

    public function removeOldJobs(int $minAgeInSeconds, array $status = []);
}
