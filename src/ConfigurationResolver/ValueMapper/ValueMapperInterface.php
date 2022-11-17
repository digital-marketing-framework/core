<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

interface ValueMapperInterface extends ConfigurationResolverInterface
{
    public function resolve(string|ValueInterface|null $fieldValue = null): string|ValueInterface|null;
}
