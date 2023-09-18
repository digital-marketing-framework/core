<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\DataProcessor\ValueSource;

/**
 * @covers \DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource
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
