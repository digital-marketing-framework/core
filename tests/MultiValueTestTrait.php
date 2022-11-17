<?php

namespace DigitalMarketingFramework\Core\Tests;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

trait MultiValueTestTrait // extends \PHPUnit\Framework\TestCase
{
    public static function assertMultiValue($actual, string $class = MultiValueInterface::class): void
    {
        static::assertIsObject($actual);
        if ($class !== MultiValueInterface::class) {
            static::assertInstanceOf(MultiValueInterface::class, $actual);
        }
        static::assertInstanceOf($class, $actual);
    }

    public static function assertMultiValueEquals(MultiValueInterface|array $expected, mixed $actual, string $class = ''): void
    {
        if ($class === '') {
            if ($expected instanceof MultiValueInterface) {
                $class = get_class($expected);
            } else {
                $class = MultiValueInterface::class;
            }
        }
        static::assertMultiValue($actual, $class);

        if ($expected instanceof MultiValueInterface) {
            $expected = $expected->toArray();
        }
        $actual = $actual->toArray();
        static::assertEquals(count($expected), count($actual));
        static::assertEquals(array_keys($expected), array_keys($actual));

        foreach ($expected as $key => $value) {
            if (is_scalar($value)) {
                static::assertEquals($actual[$key], $value);
            } elseif ($value instanceof MultiValueInterface) {
                static::assertMultiValueEquals($value, $actual[$key], get_class($value));
            } else {
                static::assertMultiValueEquals($value, $actual[$key]);
            }
        }
    }

    public static function assertMultiValueEmpty(mixed $actual, string $class = MultiValueInterface::class): void
    {
        static::assertMultiValue($actual, $class);
        static::assertEmpty($actual->toArray());
    }
}
