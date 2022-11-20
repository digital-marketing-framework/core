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
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreString(mixed $ignore, bool $enabled): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyString(mixed $ignore, bool $enabled): void
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
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreNull(mixed $ignore, bool $enabled): void
    {
        $config = [
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValue(mixed $ignore, bool $enabled): void
    {
        $config = [
            'multiValue' => [5, 7, 13],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([5, 7, 13], $result);
    }

    /**
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreEmptyMultiValue(mixed $ignore, bool $enabled): void
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
     * @dataProvider trueFalseProvider
     * @test
     */
    public function ignoreMultiValueWithEmptyItemsOnly(mixed $ignore, bool $enabled): void
    {
        $config = [
            'multiValue' => ['', ''],
            'ignoreIfEmpty' => $ignore,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['', ''], $result);
    }
}
