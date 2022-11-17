<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\SelfValueMapper;

/**
 * @covers SelfValueMapper
 */
class SelfValueMapperTest extends AbstractValueMapperTest
{
    /** @test */
    public function mapNull(): void
    {
        $this->fieldValue = null;
        $config = 'value1';
        $result = $this->runResolverProcess($config);
        $this->assertNull($result);
    }

    /** @test */
    public function mapConstant(): void
    {
        $this->fieldValue = 'value1';
        $config = 'value1b';
        $result = $this->runResolverProcess($config);
        $this->assertEquals('value1b', $result);
    }
}
