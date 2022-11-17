<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\SelfContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;
use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;

/**
 * @covers SelfContentResolver
 */
class SelfContentResolverTest extends AbstractContentResolverTest
{
    protected const KEY_SELF = ConfigurationResolverInterface::KEY_SELF;

    /** @test */
    public function nullReturnsNull()
    {
        $config = null;
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function selfNullReturnsNull()
    {
        $config = [static::KEY_SELF => null];
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function stringReturnsItself()
    {
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function selfStringReturnsItself()
    {
        $config = [static::KEY_SELF => 'value1'];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function emptyStringReturnsEmptyString()
    {
        $config = '';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function selfEmptyStringReturnsEmptyString()
    {
        $config = [static::KEY_SELF => ''];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('', $result);
    }

    /** @test */
    public function complexFieldReturnsItself()
    {
        $config = new MultiValue(['value1', 'value2']);
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1','value2'], $result);
    }

    /** @test */
    public function selfComplexFieldReturnsItself()
    {
        $config = [static::KEY_SELF => new MultiValue(['value1', 'value2'])];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals(['value1', 'value2'], $result);
    }
}
