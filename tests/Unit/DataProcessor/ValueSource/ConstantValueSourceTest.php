<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<ConstantValueSource>
 */
class ConstantValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'constant';

    protected const CLASS_NAME = ConstantValueSource::class;

    #[Test]
    public function emptyConfigurationReturnsEmptyString(): void
    {
        $output = $this->processValueSource([]);
        $this->assertEquals('', $output);
    }

    #[Test]
    public function configuredConstantValueWillBeUsed(): void
    {
        $output = $this->processValueSource([ConstantValueSource::KEY_VALUE => 'foobar']);
        $this->assertEquals('foobar', $output);
    }
}
