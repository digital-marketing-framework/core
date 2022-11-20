<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IgnoreContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers IgnoreContentResolver
 */
class IgnoreContentResolverTest extends AbstractContentResolverTest
{
    protected const KEYWORD = 'ignore';

    public function trueFalseProvider(): array
    {
        return [
            [true,  true],
            [false, false],
        ];
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString(mixed $ignore, bool $ignored): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('value1', $result);
        }
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString(mixed $ignore, bool $ignored): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => '',
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('', $result);
        }
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull(mixed $ignore, bool $ignored): void
    {
        $config = [
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue(mixed $ignore, bool $ignored): void
    {
        $config = [
            'multiValue' => [5, 7, 13],
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEquals([5, 7, 13], $result);
        }
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue(mixed $ignore, bool $ignored): void
    {
        $config = [
            'multiValue' => [],
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($ignored) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEmpty($result);
        }
    }
}
