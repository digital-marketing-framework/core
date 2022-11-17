<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver\GeneralContentResolver;
use DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractContentResolverTest extends AbstractConfigurationResolverTest
{
    protected function getGeneralResolverClass(): string
    {
        return GeneralContentResolver::class;
    }
}
