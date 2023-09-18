<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Context;

use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\Context\WriteableContext;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WriteableContextTest extends TestCase
{
    protected WriteableContext $subject;

    /**
     * @return array<array{0:array<string,mixed>}>
     */
    public function toArrayProvider(): array
    {
        return [
            [[]],
            [[
                'key1' => 'value1',
                'key2' => 'value2',
            ]],
            [[
                'key1' => [
                    'key1.1' => 'value1.1',
                    'key1.2' => 'value1.2',
                ],
            ]],
        ];
    }

    /**
     * @param array<string,mixed> $values
     *
     * @dataProvider toArrayProvider
     *
     * @test
     */
    public function toArray(array $values): void
    {
        $this->subject = new WriteableContext($values);
        $result = $this->subject->toArray();
        $this->assertEquals($values, $result);
    }

    /** @test */
    public function setGetOffset(): void
    {
        $this->subject = new WriteableContext();
        $this->subject['key1'] = 'value1';
        $this->subject['key2']['key2.1'] = 'value2.1';
        $this->assertEquals('value1', $this->subject['key1']);
        $this->assertEquals('value2.1', $this->subject['key2']['key2.1']);
    }

    /** @test */
    public function setCookie(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setCookie('key1', 'value1');

        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::KEY_COOKIES => ['key1' => 'value1']], $result);
    }

    /** @test */
    public function getCookie(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setCookie('key1', 'value1');
        $this->assertEquals('value1', $this->subject->getCookie('key1'));
        $this->assertNull($this->subject->getCookie('key2'));
    }

    /** @test */
    public function getCookies(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setCookie('key1', 'value1');
        $this->assertEquals(['key1' => 'value1'], $this->subject->getCookies());
    }

    /** @test */
    public function setRequestVariable(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setRequestVariable('key1', 'value1');

        $result = $this->subject->toArray();
        $this->assertEquals([ContextInterface::KEY_REQUEST_VARIABLES => ['key1' => 'value1']], $result);
    }

    /** @test */
    public function getRequestVariable(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->assertEquals('value1', $this->subject->getRequestVariable('key1'));
        $this->assertNull($this->subject->getRequestVariable('key2'));
    }

    /** @test */
    public function getRequestVariables(): void
    {
        $this->subject = new WriteableContext();
        $this->subject->setRequestVariable('key1', 'value1');
        $this->assertEquals(['key1' => 'value1'], $this->subject->getRequestVariables());
    }

    /** @test */
    public function copyCookieFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getCookie')->with('name1')->willReturn('value1');

        $this->subject->copyCookieFromContext($sourceContext, 'name1');
        $this->assertEquals('value1', $this->subject->getCookie('name1'));
        $this->assertEquals('value1', $this->subject[ContextInterface::KEY_COOKIES]['name1']);
    }

    /** @test */
    public function copyNonExistentCookieFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getCookie')->with('name1')->willReturn(null);

        $this->subject->copyCookieFromContext($sourceContext, 'name1');
        $this->assertNull($this->subject->getCookie('name1'));
        $this->assertFalse(isset($this->subject[ContextInterface::KEY_COOKIES]));
    }

    /** @test */
    public function copyRequestVariableFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getRequestVariable')->with('name1')->willReturn('value1');

        $this->subject->copyRequestVariableFromContext($sourceContext, 'name1');
        $this->assertEquals('value1', $this->subject->getRequestVariable('name1'));
        $this->assertEquals('value1', $this->subject[ContextInterface::KEY_REQUEST_VARIABLES]['name1']);
    }

    /** @test */
    public function copyNonExistentRequestVariableFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getRequestVariable')->with('name1')->willReturn(null);

        $this->subject->copyRequestVariableFromContext($sourceContext, 'name1');
        $this->assertNull($this->subject->getRequestVariable('name1'));
        $this->assertFalse(isset($this->subject[ContextInterface::KEY_REQUEST_VARIABLES]));
    }

    /** @test */
    public function copyIpAddressFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getIpAddress')->willReturn('value1');

        $this->subject->copyIpAddressFromContext($sourceContext);
        $this->assertEquals('value1', $this->subject->getIpAddress());
        $this->assertEquals('value1', $this->subject[ContextInterface::KEY_IP_ADDRESS]);
    }

    /** @test */
    public function copyInvalidIpAddressFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getIpAddress')->willReturn(null);

        $this->subject->copyIpAddressFromContext($sourceContext);
        $this->assertNull($this->subject->getIpAddress());
        $this->assertFalse(isset($this->subject[ContextInterface::KEY_IP_ADDRESS]));
    }

    /** @test */
    public function copyTimestampFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getTimestamp')->willReturn(41);

        $this->subject->copyTimestampFromContext($sourceContext);
        $this->assertEquals(41, $this->subject->getTimestamp());
        $this->assertEquals(41, $this->subject[ContextInterface::KEY_TIMESTAMP]);
    }

    /** @test */
    public function copyInvalidTimestampFromContext(): void
    {
        $this->subject = new WriteableContext();

        /** @var ContextInterface&MockObject */
        $sourceContext = $this->createMock(ContextInterface::class);
        $sourceContext->expects($this->once())->method('getTimestamp')->willReturn(null);

        $this->subject->copyTimestampFromContext($sourceContext);
        $this->assertNull($this->subject->getTimestamp());
        $this->assertFalse(isset($this->subject[ContextInterface::KEY_TIMESTAMP]));
    }
}
