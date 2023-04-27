<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;

/**
 * @covers NullValueSource
 */
class NullValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'null';

    /** @test */
    public function returnsNull(): void
    {
        $config = $this->getValueSourceConfiguration([]);
        $output = $this->processValueSource($config);
        $this->assertNull($output);
    }
}
