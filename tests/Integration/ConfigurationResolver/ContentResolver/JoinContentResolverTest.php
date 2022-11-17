<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\JoinContentResolver;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers JoinContentResolver
 */
class JoinContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function joinNull(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => null,
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function joinString(): void
    {
        $config = [
            ConfigurationResolverInterface::KEY_SELF => 'value1',
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function joinMultiValue(): void
    {
        $config = [
            'multiValue' => [5, 7, 17],
            'join' => true,
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5\n7\n17", $result);
    }

    /** @test */
    public function joinMultiValueWithGlue(): void
    {
        $config = [
            'multiValue' => [5, 7, 17],
            'join' => [
                'glue' => ',',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals("5,7,17", $result);
    }
}
