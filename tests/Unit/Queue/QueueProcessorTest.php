<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Queue;

use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Notification\NotificationManagerInterface;
use DigitalMarketingFramework\Core\Queue\GlobalConfiguration\Settings\QueueSettings;
use DigitalMarketingFramework\Core\Queue\QueueException;
use DigitalMarketingFramework\Core\Queue\QueueInterface;
use DigitalMarketingFramework\Core\Queue\QueueProcessor;
use DigitalMarketingFramework\Core\Queue\QueueProcessorInterface;
use DigitalMarketingFramework\Core\Queue\WorkerInterface;
use DigitalMarketingFramework\Core\Tests\TestUtilityTrait;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueueProcessorTest extends TestCase
{
    use TestUtilityTrait;

    protected QueueProcessorInterface $subject;

    protected QueueInterface&MockObject $queue;

    protected WorkerInterface&MockObject $worker;

    protected QueueSettings&MockObject $queueSettings;

    protected NotificationManagerInterface&MockObject $notificationManager;

    /** @var array<JobInterface&MockObject> */
    protected array $jobs = [];

    protected int $batchSize = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationManager = $this->createMock(NotificationManagerInterface::class);
        $this->queue = $this->createMock(QueueInterface::class);
        $this->worker = $this->createMock(WorkerInterface::class);
        $this->queueSettings = $this->createMock(QueueSettings::class);

        $this->subject = new QueueProcessor($this->queue, $this->worker, $this->queueSettings);
        $this->subject->setNotificationManager($this->notificationManager);
    }

    /**
     * @return array<array{string}>
     */
    public static function processorMethodProvider(): array
    {
        return [
            'processBatch' => ['processBatch'],
            'processAll' => ['processAll'],
            'processJobs' => ['processJobs'],
        ];
    }

    protected function prepareQueue(string $method): void
    {
        $this->queueSettings->method('getBatchSize')->willReturn($this->batchSize);

        switch ($method) {
            case 'processBatch':
                $this->queue->expects($this->once())->method('fetchQueued')->with($this->batchSize)->willReturn($this->jobs);
                break;
            case 'processAll':
                $this->queue->expects($this->once())->method('fetchQueued')->with()->willReturn($this->jobs);
                break;
            case 'processJobs':
                $this->queue->expects($this->never())->method('fetchQueued');
                break;
        }
    }

    protected function executeProcessor(string $method): void
    {
        switch ($method) {
            case 'processBatch':
                $this->subject->processBatch();
                break;
            case 'processAll':
                $this->subject->processAll();
                break;
            case 'processJobs':
                $this->subject->processJobs($this->jobs);
                break;
        }
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processEmpty(string $method): void
    {
        $this->jobs = [];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->never())->method('markListAsRunning');
        $this->queue->expects($this->never())->method('markAsDone');
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processJobThatSucceeds(string $method): void
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(true);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, false);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processLessJobsThanRequested(string $method): void
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 20;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(true);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, false);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processTwoJobsThatSucceed(string $method): void
    {
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);

        $this->withConsecutiveWillReturn($this->queue, 'markAsRunning', [
            [$job1],
            [$job2],
        ], checkCount: true);

        $this->withConsecutiveWillReturn($this->worker, 'processJob', [[$job1], [$job2]], [true, true], true);
        $this->withConsecutiveWillReturn($this->queue, 'markAsDone', [[$job1, false], [$job2, false]], checkCount: true);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processJobThatFails(string $method): void
    {
        $errorMessage = 'my error message';
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willThrowException(new QueueException($errorMessage));
        $this->queue->expects($this->once())->method('markAsFailed')->with($job, $errorMessage);
        $this->queue->expects($this->never())->method('markAsDone');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processTwoJobsThatBothFail(string $method): void
    {
        $errorMessage = 'my error message';
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->withConsecutiveWillReturn($this->queue, 'markAsRunning', [[$job1], [$job2]], checkCount: true);

        $exception = new QueueException($errorMessage);
        $this->withConsecutiveWillReturn($this->worker, 'processJob', [[$job1], [$job2]], [$exception, $exception], true);

        $this->withConsecutiveWillReturn($this->queue, 'markAsFailed', [[$job1, $errorMessage], [$job2, $errorMessage]], checkCount: true);
        $this->queue->expects($this->never())->method('markAsDone');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processJobThrowsArbitraryException(string $method): void
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willThrowException(new Exception('my error message'));
        $this->queue->expects($this->never())->method('markAsDone');
        $this->queue->expects($this->never())->method('markAsFailed');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('my error message');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processJobThatSucceedsButWasSkipped(string $method): void
    {
        $job = $this->createMock(JobInterface::class);

        $this->jobs = [$job];
        $this->batchSize = 1;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);
        $this->queue->expects($this->once())->method('markAsRunning')->with($job);
        $this->worker->expects($this->once())->method('processJob')->with($job)->willReturn(false);
        $this->queue->expects($this->once())->method('markAsDone')->with($job, true);
        $this->queue->expects($this->never())->method('markAsFailed');

        $this->executeProcessor($method);
    }

    #[Test]
    #[DataProvider('processorMethodProvider')]
    public function processTwoJobsOfWhichTheFirstFails(string $method): void
    {
        $errorMessage = 'my error message';
        $job1 = $this->createMock(JobInterface::class);
        $job2 = $this->createMock(JobInterface::class);

        $this->jobs = [$job1, $job2];
        $this->batchSize = 2;
        $this->prepareQueue($method);

        $this->queue->expects($this->once())->method('markListAsPending')->with($this->jobs);

        $this->withConsecutiveWillReturn($this->queue, 'markAsRunning', [[$job1], [$job2]], checkCount: true);
        $this->withConsecutiveWillReturn($this->worker, 'processJob', [[$job1], [$job2]], [new QueueException($errorMessage), true], true);
        $this->queue->expects($this->once())->method('markAsFailed')->with($job1, $errorMessage);
        $this->queue->expects($this->once())->method('markAsDone')->with($job2, false);

        $this->executeProcessor($method);
    }
}
