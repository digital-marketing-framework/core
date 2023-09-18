<?php

namespace DigitalMarketingFramework\Core\Tests\Unit\DataProcessor\ValueSource;

use DigitalMarketingFramework\Core\DataProcessor\ValueSource\ListValueSource;

class ListValueSourceTest extends MultiValueValueSourceTest
{
    protected const KEYWORD = 'list';

    protected const CLASS_NAME = ListValueSource::class;
}
