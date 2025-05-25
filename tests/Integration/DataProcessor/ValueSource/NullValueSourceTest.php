<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(NullValueSource::class)]
class NullValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'null';

    #[Test]
    public function returnsNull(): void
    {
        $config = $this->getValueSourceConfiguration([]);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }
}
