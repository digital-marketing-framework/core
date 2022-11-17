<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\IgnoreIfEmptyContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers IgnoreIfEmptyContentResolver
 */
class IgnoreIfEmptyContentResolverTest extends AbstractContentResolverTest
{
    public function trueFalseProvider(): array
    {
        return [
            [true,  true],
            [false, false],
        ];
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString($ignore, $enabled)
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString($ignore, $enabled)
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => '',
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($enabled) {
            $this->assertNull($result);
        } else {
            $this->assertEquals('', $result);
        }
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull($ignore, $enabled)
    {
        $config = [
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue($ignore, $enabled)
    {
        $config = [
            'multiValue' => [5, 7, 13],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([5, 7, 13], $result);
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue($ignore, $enabled)
    {
        $config = [
            'multiValue' => [],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        if ($enabled) {
            $this->assertNull($result);
        } else {
            $this->assertMultiValueEmpty($result);
        }
    }

    /**
     * @param $ignore
     * @param $enabled
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValueWithEmptyItemsOnly($ignore, $enabled)
    {
        $config = [
            'multiValue' => ['', ''],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['', ''], $result);
    }
}
