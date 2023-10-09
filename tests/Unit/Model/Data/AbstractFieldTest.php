<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\TestCase;

/**
 * @template FieldClass of ValueInterface
 */
abstract class AbstractFieldTest extends TestCase
{
    protected const FIELD_CLASS = ValueInterface::class;

    /** @var FieldClass */
    protected ValueInterface $subject;

    public static function assertFieldEquals(mixed $expected, mixed $result): void
    {
        static::assertInstanceOf(ValueInterface::class, $expected);
        static::assertInstanceOf(ValueInterface::class, $result);
        static::assertEquals($expected->pack(), $result->pack());
    }

    /**
     * @return FieldClass
     */
    protected function createField(mixed ...$arguments): ValueInterface
    {
        $class = static::FIELD_CLASS;

        return new $class(...$arguments);
    }

    /** @test */
    abstract public function init(): void;

    /**
     * @return array<array{0:array<mixed>,1:string}>
     */
    abstract public function castToStringProvider(): array;

    /**
     * @param array<mixed> $arguments
     *
     * @dataProvider castToStringProvider
     *
     * @test
     */
    public function castToString(array $arguments, string $stringRepresentation): void
    {
        $this->subject = $this->createField(...$arguments);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    /**
     * @return array<array{0:array<mixed>,1:array<mixed>}>
     */
    abstract public function packProvider(): array;

    /**
     * @param array<mixed> $arguments
     * @param array<mixed> $packed
     *
     * @dataProvider packProvider
     *
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
