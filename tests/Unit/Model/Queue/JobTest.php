<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Queue;

use DateTime;
use DigitalMarketingFramework\Core\Model\Queue\Job;
use DigitalMarketingFramework\Core\Model\Queue\JobInterface;
use DigitalMarketingFramework\Core\Queue\QueueInterface;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase
{
    protected JobInterface $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Job();
    }

    /** @test */
    public function defaultValues()
    {
        // NOTE we can't really test the default values of the fields "created" and "changed"
        //      because we don't necessarily have the exact timestamp of the object creation
        $this->assertEquals(QueueInterface::STATUS_PENDING, $this->subject->getStatus());
        $this->assertEquals('', $this->subject->getStatusMessage());
        $this->assertEmpty($this->subject->getData());
    }

    /** @test */
    public function setGetCreated()
    {
        $value = DateTime::createFromFormat('Y-m-d', '2013-05-23');
        $this->subject->setCreated($value);
        $this->assertEquals($value, $this->subject->getCreated());
    }

    /** @test */
    public function setGetChanged()
    {
        $value = DateTime::createFromFormat('Y-m-d', '2014-06-24');
        $this->subject->setChanged($value);
        $this->assertEquals($value, $this->subject->getChanged());
    }

    public function statusProvider(): array
    {
        return [
            [QueueInterface::STATUS_PENDING],
            [QueueInterface::STATUS_RUNNING],
            [QueueInterface::STATUS_DONE],
            [QueueInterface::STATUS_FAILED],
        ];
    }

    /**
     * @param $value
     * @dataProvider statusProvider
     * @test
     */
    public function setGetStatus($value)
    {
        $this->subject->setStatus($value);
        $this->assertEquals($value, $this->subject->getStatus());
    }

    /** @test */
    public function setGetSkippedTrue()
    {
        $this->subject->setSkipped(true);
        $this->assertTrue($this->subject->getSkipped());
    }

    /** @test */
    public function setGetSkippedFalse()
    {
        $this->subject->setSkipped(false);
        $this->assertFalse($this->subject->getSkipped());
    }

    /** @test */
    public function setGetStatusMessage()
    {
        $value = 'my status message';
        $this->subject->setStatusMessage($value);
        $this->assertEquals($value, $this->subject->getStatusMessage());
    }

    /** @test */
    public function setGetEmptyStatusMessage()
    {
        $this->subject->setStatusMessage('');
        $this->assertEquals('', $this->subject->getStatusMessage());
    }

    /** @test */
    public function setGetData()
    {
        $value = ['key1' => 'value1'];
        $this->subject->setData($value);
        $this->assertEquals($value, $this->subject->getData());
    }

    /** @test */
    public function setGetEmptyData()
    {
        $this->subject->setData([]);
        $this->assertEmpty($this->subject->getData());
    }
}
