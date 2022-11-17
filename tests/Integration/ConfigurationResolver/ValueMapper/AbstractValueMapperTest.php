<?php

namespace DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\GeneralConfigurationResolverInterface;
use DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper\GeneralValueMapper;
use DigitalMarketingFramework\Core\Tests\Integration\ConfigurationResolver\AbstractConfigurationResolverTest;

abstract class AbstractValueMapperTest extends AbstractConfigurationResolverTest
{
    protected mixed $fieldValue = null;

    protected function getGeneralResolverClass(): string
    {
        return GeneralValueMapper::class;
    }

    protected function executeResolver(GeneralConfigurationResolverInterface $resolver): mixed
    {
        /** @var GeneralValueMapper $resolver */
        return $resolver->resolve($this->fieldValue);
    }
}
