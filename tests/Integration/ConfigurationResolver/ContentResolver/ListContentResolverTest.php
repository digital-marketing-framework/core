<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\ListContentResolver;
use DigitalMarketingFramework\Core\Model\Data\Value\MultiValue;

/**
 * @covers ListContentResolver
 */
class ListContentResolverTest extends MultiValueContentResolverTest
{
    protected const RESOLVER_CLASS = ListContentResolver::class;
    protected const MULTI_VALUE_CLASS = MultiValue::class;
    protected const KEYWORD = 'list';
}
