<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\Job;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Queue\QueueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    protected JobInterface $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Job();
    }

    #[Test]
    public function defaultValues(): void
    {
        // NOTE we can't really test the default values of the fields "created" and "changed"
        //      because we don't necessarily have the exact timestamp of the object creation
        $this->assertEquals(QueueInterface::STATUS_QUEUED, $this->subject->getStatus());
        $this->assertEquals('', $this->subject->getStatusMessage());
        $this->assertEmpty($this->subject->getData());
    }

    #[Test]
    public function setGetCreated(): void
    {
        $value = DateTime::createFromFormat('Y-m-d', '2013-05-23');
        $this->subject->setCreated($value);
        $this->assertEquals($value, $this->subject->getCreated());
    }

    #[Test]
    public function setGetChanged(): void
    {
        $value = DateTime::createFromFormat('Y-m-d', '2014-06-24');
        $this->subject->setChanged($value);
        $this->assertEquals($value, $this->subject->getChanged());
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

    #[Test]
    #[DataProvider('statusProvider')]
    public function setGetStatus(int $value): void
    {
        $this->subject->setStatus($value);
        $this->assertEquals($value, $this->subject->getStatus());
    }

    #[Test]
    public function setGetSkippedTrue(): void
    {
        $this->subject->setSkipped(true);
        $this->assertTrue($this->subject->getSkipped());
    }

    #[Test]
    public function setGetSkippedFalse(): void
    {
        $this->subject->setSkipped(false);
        $this->assertFalse($this->subject->getSkipped());
    }

    #[Test]
    public function setGetStatusMessage(): void
    {
        $value = 'my status message';
        $this->subject->setStatusMessage($value);
        $this->assertEquals($value, $this->subject->getStatusMessage());
    }

    #[Test]
    public function setGetEmptyStatusMessage(): void
    {
        $this->subject->setStatusMessage('');
        $this->assertEquals('', $this->subject->getStatusMessage());
    }

    #[Test]
    public function setGetData(): void
    {
        $value = ['key1' => 'value1'];
        $this->subject->setData($value);
        $this->assertEquals($value, $this->subject->getData());
    }

    #[Test]
    public function setGetEmptyData(): void
    {
        $this->subject->setData([]);
        $this->assertEmpty($this->subject->getData());
    }
}
