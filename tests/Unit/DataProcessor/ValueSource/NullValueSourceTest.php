<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\NullValueSource;
use PHPUnit\Framework\Attributes\Test;

/**
 * @extends ValueSourceTestBase<NullValueSource>
 */
class NullValueSourceTest extends ValueSourceTestBase
{
    protected const KEYWORD = 'constant';

    protected const CLASS_NAME = NullValueSource::class;

    #[Test]
    public function returnsNull(): void
    {
        $output = $this->processValueSource([]);
        $this->assertNull($output);
    }
}
