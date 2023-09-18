<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;

class ConstantValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'constant';

    protected const CLASS_NAME = ConstantValueSource::class;

    /** @test */
    public function emptyConfigurationReturnsEmptyString(): void
    {
        $output = $this->processValueSource([]);
        $this->assertEquals('', $output);
    }

    /** @test */
    public function configuredConstantValueWillBeUsed(): void
    {
        $output = $this->processValueSource([ConstantValueSource::KEY_VALUE => 'foobar']);
        $this->assertEquals('foobar', $output);
    }
}
