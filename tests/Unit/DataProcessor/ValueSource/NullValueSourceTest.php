<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;

class NullValueSourceTest extends ValueSourceTest
{
    protected const KEYWORD = 'constant';

    protected const CLASS_NAME = NullValueSource::class;

    /** @test */
    public function returnsNull(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }
}
