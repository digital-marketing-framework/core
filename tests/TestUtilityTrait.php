<?php

namespace DigitalMarketingFramework\Core\Tests;

use Exception;
use PHPUnit\Framework\MockObject\MockObject;

trait TestUtilityTrait
{
    /**
     * @param array<mixed> $arguments
     */
    private function computeReturn(mixed $return, array $arguments): mixed
    {
        if ($return instanceof Exception) {
            throw $return;
        }

        if (is_callable($return)) {
            return $return(...$arguments);
        }

        return $return;
    }

    /**
     * @param array<mixed> $with
     * @param ?array<mixed> $return
     */
    public function withConsecutiveWillReturn(MockObject $mock, string $method, array $with, array|callable|Exception|null $return = null, bool $checkCount = false): void
    {
        $with = array_values($with);
        $count = 0;
        $obj = $mock;
        if ($checkCount) {
            $obj = $obj->expects($this->exactly(count($with)));
        }

        $obj->method($method)->willReturnCallback(function (...$arguments) use (&$count, $with, $return) {
            $argCount = count($with[$count]);
            for ($i = 0; $i < $argCount; ++$i) {
                static::assertEquals($with[$count][$i], $arguments[$i] ?? null);
            }

            ++$count;
            if (is_array($return)) {
                return $this->computeReturn($return[$count - 1], $arguments);
            } elseif ($return !== null) {
                return $this->computeReturn($return, $arguments);
            }
        });
    }
}
