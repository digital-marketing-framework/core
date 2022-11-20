<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\RawValueMapper;

/**
 * @covers RawValueMapper
 */
class RawValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function rawMatches(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'value1' => 'value1b'
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }

    /** @test */
    public function rawDoesNotMatch(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'value2' => 'value2b'
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }

    /** @test */
    public function rawKeywordMatches(): void
    {
        $this->fieldValue = 'if';
        $config = [
            'raw' => [
                'if' => 'ifb',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('ifb', $result);
    }

    /** @test */
    public function rawKeywordDoesNotMatch(): void
    {
        $this->fieldValue = 'value1';
        $config = [
            'raw' => [
                'if' => 'ifb',
            ],
        ];
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1', $result);
    }
}
