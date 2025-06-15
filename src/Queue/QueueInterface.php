<?php

namespace DigitalMarketingFramework\Core\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\Error;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Storage\ItemStorageInterface;

/**
 * @extends ItemStorageInterface<JobInterface>
 */
interface QueueInterface extends ItemStorageInterface
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
    public function fetchByStatus(array $status = [], int $limit = 0, int $offset = 0): array;

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

    public function markAsFailed(JobInterface $job, string $message = '', bool $preserveTimestamp = false): void;

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
    public function markListAsFailed(array $jobs, string $message = '', bool $preserveTimestamp = false): void;

    /**
     * @param array<int> $status
     */
    public function removeOldJobs(int $minAgeInSeconds, array $status = []): void;

    /**
     * @param array{minCreated:?DateTime,maxCreated:?DateTime,minChanged:?DateTime,maxChanged:?DateTime} $filters
     *
     * @return array{hashes:int,all:int,queued:int,pending:int,running:int,done:int,doneNotSkipped:int,doneSkipped:int,failed:int,groupedByType:array<string,array{all:int,queued:int,pending:int,running:int,done:int,doneNotSkipped:int,doneSkipped:int,failed:int}>}
     */
    public function getStatistics(array $filters): array;

    /**
     * @param array{minCreated:?DateTime,maxCreated:?DateTime,minChanged:?DateTime,maxChanged:?DateTime} $filters
     * @param array{page:int,itemsPerPage:int,sorting:array<string,string>} $navigation
     *
     * @return array<Error>
     */
    public function getErrorMessages(array $filters, array $navigation): array;

    /**
     * @return array<string>
     */
    public function fetchJobTypes(): array;
}
