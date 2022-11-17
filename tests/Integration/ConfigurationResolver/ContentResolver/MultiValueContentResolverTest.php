<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\MultiValueContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers MultiValueContentResolver
 */
class MultiValueContentResolverTest extends AbstractContentResolverTest
{
    protected const RESOLVER_CLASS = MultiValueContentResolver::class;
    protected const MULTI_VALUE_CLASS = MultiValue::class;
    protected const KEYWORD = 'multiValue';

    /** @test */
    public function MultiValue()
    {
        $config = [
            static::KEYWORD => [3,5,17],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([3,5,17], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function MultiValueEmpty()
    {
        $config = [
            static::KEYWORD => [],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function MultiValueContainsNull()
    {
        $config = [
            static::KEYWORD => [3,null,17],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([0 => 3, 2 => 17], $result, static::MULTI_VALUE_CLASS);
    }

    /** @test */
    public function MultiValueContainsOnlyNulls()
    {
        $config = [
            static::KEYWORD => [null, null, null],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertMultiValueEquals([], $result, static::MULTI_VALUE_CLASS);
    }
}
