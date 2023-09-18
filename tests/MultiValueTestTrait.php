<?php

namespace DigitalMarketingFramework\Core\Tests;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValueInterface;

trait MultiValueTestTrait // extends \PHPUnit\Framework\TestCase
{
    public static function assertMultiValue(mixed $actual, string $class = MultiValueInterface::class): void
    {
        static::assertIsObject($actual);
        if ($class !== MultiValueInterface::class) {
            static::assertInstanceOf(MultiValueInterface::class, $actual);
        }

        static::assertInstanceOf($class, $actual);
    }

    /**
     * @param MultiValueInterface|array<mixed> $expected
     */
    public static function assertMultiValueEquals(MultiValueInterface|array $expected, mixed $actual, string $class = ''): void
    {
        if ($class === '') {
            $class = $expected instanceof MultiValueInterface ? $expected::class : MultiValueInterface::class;
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
                static::assertMultiValueEquals($value, $actual[$key], $value::class);
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

    public static function convertMultiValues(mixed $value): mixed
    {
        if (is_array($value)) {
            $value = array_map(static function (mixed $subValue) {
                return static::convertMultiValues($subValue);
            }, $value);
            $value = new MultiValue($value);
        }

        return $value;
    }

    public static function deconvertMultiValues(mixed $value): mixed
    {
        $result = $value;
        if ($value instanceof MultiValueInterface) {
            $result = [];
            foreach ($value as $key => $subValue) {
                $result[$key] = static::deconvertMultiValues($subValue);
            }
        }

        return $result;
    }
}
