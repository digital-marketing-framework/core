<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractFieldTest extends TestCase
{
    protected const FIELD_CLASS = ValueInterface::class;

    protected ValueInterface $subject;

    public static function assertFieldEquals($expected, $result): void
    {
        static::assertInstanceOf(ValueInterface::class, $expected);
        static::assertInstanceOf(ValueInterface::class, $result);
        static::assertEquals($expected->pack(), $result->pack());
    }

    protected function createField(array ...$arguments): ValueInterface
    {
        $class = static::FIELD_CLASS;
        return new $class(...$arguments);
    }

    /** @test */
    abstract public function init(): void;

    abstract public function castToStringProvider(): array;

    /**
     * @dataProvider castToStringProvider
     * @test
     */
    public function castToString(array $arguments, string $stringRepresentation): void
    {
        $this->subject = $this->createField(...$arguments);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    abstract public function packProvider(): array;

    /**
     * @dataProvider packProvider
     * @test
     */
    public function pack(array $arguments, array $packed): void
    {
        $this->subject = $this->createField(...$arguments);
        $result = $this->subject->pack();
        $this->assertEquals($packed, $result);
    }

    /** @test */
    public function packUnpack(): void
    {
        $this->subject = $this->createField();
        $packed = $this->subject->pack();
        $class = static::FIELD_CLASS;
        $unpacked = $class::unpack($packed);
        $this->assertFieldEquals($this->subject, $unpacked);
    }
}
