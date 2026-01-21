<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\Model\Data;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @template FieldClass of ValueInterface
 */
abstract class FieldTestBase extends TestCase
{
    protected const FIELD_CLASS = ValueInterface::class;

    /** @var FieldClass */
    protected ValueInterface $subject;

    public static function assertFieldEquals(mixed $expected, mixed $result): void
    {
        self::assertInstanceOf(ValueInterface::class, $expected);
        self::assertInstanceOf(ValueInterface::class, $result);
        self::assertEquals($expected->pack(), $result->pack());
    }

    /**
     * @return FieldClass
     */
    protected function createField(mixed ...$arguments): ValueInterface
    {
        $class = static::FIELD_CLASS;

        return new $class(...$arguments);
    }

    #[Test]
    abstract public function init(): void;

    /**
     * @return array<array{0:array<mixed>,1:string}>
     */
    abstract public static function castToStringProvider(): array;

    /**
     * @param array<mixed> $arguments
     */
    #[Test]
    #[DataProvider('castToStringProvider')]
    public function castToString(array $arguments, string $stringRepresentation): void
    {
        $this->subject = $this->createField(...$arguments);
        $result = (string)$this->subject;
        $this->assertEquals($stringRepresentation, $result);
    }

    /**
     * @return array<array{0:array<mixed>,1:array<mixed>}>
     */
    abstract public static function packProvider(): array;

    /**
     * @param array<mixed> $arguments
     * @param array<mixed> $packed
     */
    #[Test]
    #[DataProvider('packProvider')]
    public function pack(array $arguments, array $packed): void
    {
        $this->subject = $this->createField(...$arguments);
        $result = $this->subject->pack();
        $this->assertEquals($packed, $result);
    }

    #[Test]
    public function packUnpack(): void
    {
        $this->subject = $this->createField();
        $packed = $this->subject->pack();
        $class = static::FIELD_CLASS;
        $unpacked = $class::unpack($packed);
        $this->assertFieldEquals($this->subject, $unpacked);
    }
}
