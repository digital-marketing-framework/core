<?php

namespace DigitalMarketingFramework\Core\Tests;

use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

trait MultiValueTestTrait // extends \PHPUnit\Framework\TestCase
{
    public static function assertMultiValue($actual, string $class = MultiValue::class)
    {
        static::assertIsObject($actual);
        if ($class !== MultiValue::class) {
            static::assertInstanceOf(MultiValue::class, $actual);
        }
        static::assertEquals($class, get_class($actual));
    }

    public static function assertMultiValueEquals($expected, $actual, string $class = MultiValue::class)
    {
        /** @var MultiValue $actual */
        static::assertMultiValue($actual, $class);

        if ($expected instanceof MultiValue) {
            $expected = $expected->toArray();
        }
        $actual = $actual->toArray();
        static::assertEquals(array_keys($actual), array_keys($expected));

        foreach ($expected as $key => $value) {
            if (is_scalar($value)) {
                static::assertEquals($actual[$key], $value);
            } elseif ($value instanceof MultiValue) {
                static::assertMultiValueEquals($value, $actual[$key], get_class($value));
            } else {
                static::assertMultiValueEquals($value, $actual[$key]);
            }
        }
    }

    public static function assertMultiValueEmpty($actual, string $class = MultiValue::class)
    {
        static::assertMultiValue($actual, $class);
        /** @var MultiValue $actual */
        static::assertEmpty($actual->toArray());
    }
}
