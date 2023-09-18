<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource
 */
class ConstantValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'constant';

    /** @test */
    public function emptyConfigurationReturnsEmptyString(): void
    {
        $config = $this->getValueSourceConfiguration([]);
        $output = $this->processValueSource($config);
        $this->assertEquals('', $output);
    }

    /** @test */
    public function configuredConstantValueWillBeUsed(): void
    {
        $config = $this->getValueSourceConfiguration([
            ConstantValueSource::KEY_VALUE => 'foobar',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('foobar', $output);
    }
}
