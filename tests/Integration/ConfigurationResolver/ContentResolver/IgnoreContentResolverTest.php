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
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString($ignore, $ignored)
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
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString($ignore, $ignored)
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
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull($ignore, $ignored)
    {
        $config = [
            static::KEYWORD => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /**
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue($ignore, $ignored)
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
     * @param $ignore
     * @param $ignored
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue($ignore, $ignored)
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
