<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Queue;

use DigitalMarketingFramework\Core\Model\Queue\Job;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Queue\NonPersistentQueue;
use DigitalMarketingFramework\Core\Queue\QueueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NonPersistentQueueTest extends TestCase
{
    protected QueueInterface $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new NonPersistentQueue();
    }

    /**
     * @return array<array{0:int}>
     */
    public static function statusProvider(): array
    {
        return [
            [QueueInterface::STATUS_QUEUED],
            [QueueInterface::STATUS_PENDING],
            [QueueInterface::STATUS_RUNNING],
            [QueueInterface::STATUS_DONE],
            [QueueInterface::STATUS_FAILED],
        ];
    }

    /**
     * @param array<mixed> $data
     */
    protected function createJob(array $data, int $status = QueueInterface::STATUS_QUEUED): JobInterface
    {
        $job = new Job();
        $job->setData($data);
        $job->setStatus($status);

        return $job;
    }

    #[Test]
    public function addOneJob(): void
    {
        $job = $this->createJob(['value1']);
        $this->subject->add($job);

        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(1, $jobs);
        $this->assertEquals($job, $jobs[0]);
    }

    #[Test]
    public function addTwoJobs(): void
    {
        $this->subject->add($this->createJob(['value1']));
        $this->subject->add($this->createJob(['value2']));

        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(2, $jobs);
    }

    #[Test]
    public function removeJob(): void
    {
        $this->subject->add($this->createJob(['value1']));
        $this->subject->add($this->createJob(['value2']));

        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(2, $jobs);

        $this->subject->remove($jobs[0]);
        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(1, $jobs);

        $this->subject->remove($jobs[0]);
        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(0, $jobs);
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>,4:array<int>}>
     */
    public static function fetchProvider(): array
    {
        return [
            // limit, offset, expectedCount, statusFilter, jobStatusArray
            [0, 0, 0, [], []],

            [0, 0, 2, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [0, 0, 2, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE]],

            [0, 0, 1, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED]],
            [0, 0, 2, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [0, 0, 1, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_QUEUED]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_RUNNING]],
            [0, 0, 0, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_FAILED]],

            [0, 0, 4, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_RUNNING, QueueInterface::STATUS_FAILED]],
            [0, 0, 1, [QueueInterface::STATUS_DONE], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_RUNNING, QueueInterface::STATUS_FAILED]],

            [0, 0, 2, [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE]],
            [0, 0, 1, [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_RUNNING], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE]],

            [0, 0, 4, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [2, 0, 2, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [1, 1, 1, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [0, 2, 2, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [3, 2, 2, [], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],

            [0, 0, 3, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [2, 0, 2, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [1, 1, 1, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [0, 2, 1, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
            [3, 2, 1, [QueueInterface::STATUS_QUEUED], [QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_DONE, QueueInterface::STATUS_QUEUED, QueueInterface::STATUS_QUEUED]],
        ];
    }

    /**
     * @param array<int> $statusFilter
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchProvider')]
    public function fetch(int $limit, int $offset, int $expectedCount, array $statusFilter, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchByStatus($statusFilter, $limit, $offset);
        $this->assertCount($expectedCount, $jobs);
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    protected static function fetchStatusProvider(int $s1, int $s2, int $s3, int $s4, int $s5): array
    {
        return [
            'empty job queue' => [0, 0, 0, []],

            'one job to be fetched'  => [0, 0, 1, [$s1]],
            'two jobs to be fetched' => [0, 0, 2, [$s1, $s1]],

            'two jobs, one to be fetched'     => [0, 0, 1, [$s1, $s2]],
            'one job, not to be fetched (s2)' => [0, 0, 0, [$s2]],
            'one job, not to be fetched (s3)' => [0, 0, 0, [$s3]],
            'one job, not to be fetched (s4)' => [0, 0, 0, [$s4]],
            'one job, not to be fetched (s5)' => [0, 0, 0, [$s5]],

            'all jobs, limit 0, offset 0' => [0, 0, 4, [$s1, $s1, $s1, $s1]],
            'all jobs, limit 2, offset 0' => [2, 0, 2, [$s1, $s1, $s1, $s1]],
            'all jobs, limit 1, offset 1' => [1, 1, 1, [$s1, $s1, $s1, $s1]],
            'all jobs, limit 0, offset 2' => [0, 2, 2, [$s1, $s1, $s1, $s1]],
            'all jobs, limit 3, offset 2' => [3, 2, 2, [$s1, $s1, $s1, $s1]],

            'some jobs, limit 0, offset 0' => [0, 0, 3, [$s1, $s2, $s1, $s1]],
            'some jobs, limit 2, offset 0' => [2, 0, 2, [$s1, $s2, $s1, $s1]],
            'some jobs, limit 1, offset 1' => [1, 1, 1, [$s1, $s2, $s1, $s1]],
            'some jobs, limit 0, offset 2' => [0, 2, 1, [$s1, $s2, $s1, $s1]],
            'some jobs, limit 3, offset 2' => [3, 2, 1, [$s1, $s2, $s1, $s1]],
        ];
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    public static function fetchQueuedProvider(): array
    {
        return static::fetchStatusProvider(
            QueueInterface::STATUS_QUEUED,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED
        );
    }

    /**
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchQueuedProvider')]
    public function fetchQueued(int $limit, int $offset, int $expectedCount, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchQueued($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_QUEUED, $job->getStatus());
        }
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    public static function fetchPendingProvider(): array
    {
        return static::fetchStatusProvider(
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_QUEUED,
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED
        );
    }

    /**
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchPendingProvider')]
    public function fetchPending(int $limit, int $offset, int $expectedCount, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchPending($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_PENDING, $job->getStatus());
        }
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    public static function fetchDoneProvider(): array
    {
        return static::fetchStatusProvider(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_QUEUED,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_FAILED
        );
    }

    /**
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchDoneProvider')]
    public function fetchDone(int $limit, int $offset, int $expectedCount, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchDone($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_DONE, $job->getStatus());
        }
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    public static function fetchFailedProvider(): array
    {
        return static::fetchStatusProvider(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_QUEUED,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_DONE
        );
    }

    /**
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchFailedProvider')]
    public function fetchFailed(int $limit, int $offset, int $expectedCount, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchFailed($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        /** @var JobInterface $job */
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_FAILED, $job->getStatus());
        }
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:array<int>}>
     */
    public static function fetchRunningProvider(): array
    {
        return static::fetchStatusProvider(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_QUEUED,
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_PENDING
        );
    }

    /**
     * @param array<int> $jobStatusArray
     */
    #[Test]
    #[DataProvider('fetchRunningProvider')]
    public function fetchRunning(int $limit, int $offset, int $expectedCount, array $jobStatusArray): void
    {
        foreach ($jobStatusArray as $i => $s) {
            $this->subject->add($this->createJob(['value' . $i], $s));
        }

        $jobs = $this->subject->fetchRunning($limit, $offset);
        $this->assertCount($expectedCount, $jobs);
        foreach ($jobs as $job) {
            $this->assertEquals(QueueInterface::STATUS_RUNNING, $job->getStatus());
        }
    }

    /**
     * @return array<array{0:int,1:int,2:int,3:string}>
     */
    public static function fetchRunningWithMinTimeInSecondsSinceChangedProvider(): array
    {
        return [
            [0, QueueInterface::STATUS_RUNNING, 3600, ''],
            [0, QueueInterface::STATUS_RUNNING, 3600, '+1 hour'],
            [1, QueueInterface::STATUS_RUNNING, 3600, '-2 hours'],

            [0, QueueInterface::STATUS_PENDING, 3600, ''],
            [0, QueueInterface::STATUS_PENDING, 3600, '+1 hour'],
            [0, QueueInterface::STATUS_PENDING, 3600, '-2 hours'],
        ];
    }

    #[Test]
    #[DataProvider('fetchRunningWithMinTimeInSecondsSinceChangedProvider')]
    public function fetchRunningWithMinTimeInSecondsSinceChanged(int $expectedCount, int $status, int $minAge, string $modify): void
    {
        $job = $this->createJob(['value1'], $status);
        if ($modify !== '') {
            $job->getChanged()->modify($modify);
        }

        $this->subject->add($job);

        $jobs = $this->subject->fetchRunning(0, 0, $minAge);
        $this->assertCount($expectedCount, $jobs);
    }

    #[Test]
    public function removeAllOldJobs(): void
    {
        $this->subject->add($this->createJob(['value1'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value2'], QueueInterface::STATUS_PENDING));
        $this->subject->add($this->createJob(['value3'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value4'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value5'], QueueInterface::STATUS_RUNNING));
        $this->subject->add($this->createJob(['value6'], QueueInterface::STATUS_FAILED));
        $this->subject->add($this->createJob(['value7'], QueueInterface::STATUS_PENDING));

        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(7, $jobs);

        $jobs[0]->getCreated()->modify('-3 hours');
        $jobs[1]->getCreated()->modify('-1 day');
        $jobs[2]->getCreated()->modify('-10 minutes');
        $jobs[3]->getCreated()->modify('-2 hours');
        $jobs[4]->getCreated()->modify('+1 day');
        $jobs[5]->getCreated()->modify('+10 minutes');
        $jobs[6]->getCreated()->modify('+2 hours');

        $this->subject->removeOldJobs(3600);

        $remainingJobs = $this->subject->fetchByStatus();
        $this->assertCount(4, $remainingJobs);
    }

    #[Test]
    public function removeOldJobsThatAreDone(): void
    {
        $this->subject->add($this->createJob(['value1'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value2'], QueueInterface::STATUS_PENDING));
        $this->subject->add($this->createJob(['value3'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value4'], QueueInterface::STATUS_DONE));
        $this->subject->add($this->createJob(['value5'], QueueInterface::STATUS_RUNNING));
        $this->subject->add($this->createJob(['value6'], QueueInterface::STATUS_FAILED));
        $this->subject->add($this->createJob(['value7'], QueueInterface::STATUS_PENDING));

        $jobs = $this->subject->fetchByStatus();
        $this->assertCount(7, $jobs);

        $jobs[0]->getCreated()->modify('-3 hours');
        $jobs[1]->getCreated()->modify('-1 day');
        $jobs[2]->getCreated()->modify('-10 minutes');
        $jobs[3]->getCreated()->modify('-2 hours');
        $jobs[4]->getCreated()->modify('+1 day');
        $jobs[5]->getCreated()->modify('+10 minutes');
        $jobs[6]->getCreated()->modify('+2 hours');

        $this->subject->removeOldJobs(3600, [QueueInterface::STATUS_DONE]);

        $remainingJobs = $this->subject->fetchByStatus();
        $this->assertCount(5, $remainingJobs);
    }

    /**
     * @param array<mixed> $arguments
     */
    protected function markAs(int $status, int $initialStatus, string $method, array $arguments = [], string $expectedStatusMessage = '', bool $expectedSkipped = false): void
    {
        $job = $this->createJob(['value1'], $initialStatus);
        $this->subject->add($job);
        static::assertEquals($initialStatus, $job->getStatus());

        $this->subject->$method($job, ...$arguments);
        static::assertEquals($status, $job->getStatus());
        if ($expectedStatusMessage === '') {
            static::assertEquals('', $job->getStatusMessage());
        } else {
            static::assertStringEndsWith($expectedStatusMessage, $job->getStatusMessage());
        }

        static::assertEquals($expectedSkipped, $job->getSkipped());
    }

    #[Test]
    public function markAsPending(): void
    {
        $this->markAs(
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_RUNNING,
            'markAsPending'
        );
    }

    #[Test]
    public function markAsRunning(): void
    {
        $this->markAs(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_PENDING,
            'markAsRunning'
        );
    }

    #[Test]
    public function markAsDone(): void
    {
        $this->markAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            'markAsDone'
        );
    }

    #[Test]
    public function markAsDoneButSkipped(): void
    {
        $this->markAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            'markAsDone',
            [true],
            '',
            true
        );
    }

    #[Test]
    public function markAsFailed(): void
    {
        $this->markAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            'markAsFailed'
        );
    }

    #[Test]
    public function markAsFailedWithMessage(): void
    {
        $errorMessage = 'my error message';
        $this->markAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            'markAsFailed',
            [$errorMessage],
            $errorMessage
        );
    }

    /**
     * @param array<mixed> $arguments
     */
    protected function markListAs(
        int $status,
        int $initialStatus,
        int $unrelatedStatus,
        string $method,
        array $arguments = [],
        string $expectedStatusMessage = '',
        bool $expectedSkipped = false,
    ): void {
        $this->subject->add($this->createJob(['value1'], $initialStatus));
        $this->subject->add($this->createJob(['value2'], $unrelatedStatus));
        $this->subject->add($this->createJob(['value3'], $initialStatus));

        $jobs = $this->subject->fetchByStatus([$initialStatus]);
        $this->assertCount(2, $jobs);

        $this->subject->$method($jobs, ...$arguments);
        foreach ($jobs as $job) {
            $this->assertEquals($status, $job->getStatus());
            $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());
            $this->assertEquals($expectedSkipped, $job->getSkipped());
        }

        $jobs = $this->subject->fetchByStatus([$status]);
        $this->assertCount(2, $jobs);
        foreach ($jobs as $job) {
            $this->assertEquals($status, $job->getStatus());
            $this->assertEquals($expectedStatusMessage, $job->getStatusMessage());
            $this->assertEquals($expectedSkipped, $job->getSkipped());
        }
    }

    #[Test]
    public function markListAsRunning(): void
    {
        $this->markListAs(
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_PENDING,
            QueueInterface::STATUS_FAILED,
            'markListAsRunning'
        );
    }

    #[Test]
    public function markListAsDone(): void
    {
        $this->markListAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED,
            'markListAsDone'
        );
    }

    #[Test]
    public function markListAsDoneButSkipped(): void
    {
        $this->markListAs(
            QueueInterface::STATUS_DONE,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_FAILED,
            'markListAsDone',
            [true],
            '',
            true
        );
    }

    #[Test]
    public function markListAsFailed(): void
    {
        $this->markListAs(
            QueueInterface::STATUS_FAILED,
            QueueInterface::STATUS_RUNNING,
            QueueInterface::STATUS_DONE,
            'markListAsFailed'
        );
    }
}
