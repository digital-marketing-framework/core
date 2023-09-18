<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Context;

use BadMethodCallException;
use DigitalMarketingFramework\Core\Context\RequestContext;
use PHPUnit\Framework\TestCase;

/**
 * RequestContext uses global state data, which is why we can't test all too much here.
 */
class RequestContextTest extends TestCase
{
    protected RequestContext $subject;

    /** @test */
    public function toArray(): never
    {
        $this->subject = new RequestContext();
        $this->expectException(BadMethodCallException::class);
        $this->subject->toArray();
    }

    /** @test */
    public function setOffset(): void
    {
        $this->subject = new RequestContext();
        $this->expectException(BadMethodCallException::class);
        $this->subject['key1'] = 'value1';
    }
}
