<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ValueContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers ValueContentResolver
 */
class ValueContentResolverTest extends AbstractContentResolverTest
{
    /** @test */
    public function selfNullReturnsNull(): void
    {
        $config = ['value' => null];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function selfStringReturnsItself(): void
    {
        $config = ['value' => 'value1'];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function selfEmptyStringReturnsEmptyString(): void
    {
        $config = ['value' => ''];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function selfComplexFieldReturnsItself(): void
    {
        $config = ['value' => new MultiValue(['value1', 'value2'])];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }
}
