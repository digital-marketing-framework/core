<?php

namespace DigitalMarketingFramework\Core\Queue;

use BadMethodCallException;
use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\Job;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\Storage\ItemStorage;

/**
 * @extends ItemStorage<JobInterface>
 */
class NonPersistentQueue extends ItemStorage implements QueueInterface
{
    /** @var array<int,JobInterface> */
    protected array $queue = [];

    public function __construct()
    {
        parent::__construct(Job::class);
    }

    protected function mapDataField(string $name, mixed $value): mixed
    {
        switch ($name) {
            case 'created':
            case 'changed':
                if (!$value instanceof DateTime) {
                    $value = new DateTime('@' . $value);
                }

                return $value;
        }

        return parent::mapDataField($name, $value);
    }

    protected function mapItemField(string $name, mixed $value): mixed
    {
        switch ($name) {
            case 'created':
            case 'changed':
                if ($value instanceof DateTime) {
                    $value = $value->getTimestamp();
                }

                return $value;
        }

        return parent::mapItemField($name, $value);
    }

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

    public function fetchByStatus(array $status = [], int $limit = 0, int $offset = 0): array
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

    public function markAs(JobInterface $job, int $status, ?string $message = null, bool $skipped = false, bool $preserveTimestamp = false): void
    {
        $job->setStatus($status);
        $job->setSkipped($skipped);
        if (!$preserveTimestamp) {
            $job->setChanged(new DateTime());
        }

        if ($message !== null) {
            $job->addStatusMessage($message);
        }
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

    public function markAsFailed(JobInterface $job, string $message = '', bool $preserveTimestamp = false): void
    {
        $this->markAs($job, QueueInterface::STATUS_FAILED, $message, preserveTimestamp: $preserveTimestamp);
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

    public function markListAsFailed(array $jobs, string $message = '', bool $preserveTimestamp = false): void
    {
        foreach ($jobs as $job) {
            $this->markAsFailed($job, $message, $preserveTimestamp);
        }
    }

    protected function getNewId(): int
    {
        $highestId = null;
        foreach (array_keys($this->queue) as $jobId) {
            if ($highestId === null || $highestId < $jobId) {
                $highestId = $jobId;
            }
        }

        return $highestId + 1;
    }

    public function add($job): void
    {
        if ($job->getId() === null) {
            $job->setId($this->getNewId());
        }

        if (!in_array($job, $this->queue)) {
            $this->queue[$job->getId()] = $job;
        }
    }

    public function update($job): void
    {
    }

    public function fetchById(int|string $id): ?JobInterface
    {
        return $this->queue[$id] ?? null;
    }

    public function fetchByIdList(array $ids): array
    {
        $result = [];
        foreach ($ids as $id) {
            if (isset($this->queue[$id])) {
                $result[] = $this->queue[$id];
            }
        }

        return $result;
    }

    public function remove($job): void
    {
        $this->queue = array_filter(
            $this->queue,
            static fn ($a) => $a !== $job
        );
    }

    public function removeOldJobs(int $minAgeInSeconds, array $status = []): void
    {
        $jobs = $this->fetchWhere($status, 0, 0, 0, $minAgeInSeconds);
        foreach ($jobs as $job) {
            $this->remove($job);
        }
    }

    public function getStatistics(array $filters): array
    {
        throw new BadMethodCallException('Non persistent queue does not have any statistics to produce.');
    }

    public function getErrorMessages(array $filters, array $navigation): array
    {
        throw new BadMethodCallException('Non persistent queue does not have any statistics to produce.');
    }

    public function fetchFiltered(array $filters, ?array $navigation = null): array
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public function fetchOneFiltered(array $filters): never
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public function fetchAll(?array $navigation = null): array
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public function countFiltered(array $filters): int
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public function countAll(): int
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public function fetchJobTypes(): array
    {
        throw new BadMethodCallException('Non persistent queue does not support filtered custom requests.');
    }

    public static function getSchema(): ContainerSchema
    {
        return new JobSchema();
    }
}
