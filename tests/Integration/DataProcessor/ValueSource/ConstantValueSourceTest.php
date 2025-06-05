<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ConstantValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ConstantValueSource::class)]
class ConstantValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'constant';

    #[Test]
    public function emptyConfigurationReturnsEmptyString(): void
    {
        $config = $this->getValueSourceConfiguration([]);
        $output = $this->processValueSource($config);
        $this->assertEquals('', $output);
    }

    #[Test]
    public function configuredConstantValueWillBeUsed(): void
    {
        $config = $this->getValueSourceConfiguration([
            ConstantValueSource::KEY_VALUE => 'foobar',
        ]);
        $output = $this->processValueSource($config);
        $this->assertEquals('foobar', $output);
    }
}
