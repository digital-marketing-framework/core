<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\ConfigurationResolver\ConfigurationResolverInterface;
use DigitalMarketingFramework\Core\Model\Form\FieldInterface;

interface ValueMapperInterface extends ConfigurationResolverInterface
{
    /**
     * @param string|FieldInterface|null $fieldValue
     * @return string|FieldInterface|null
     */
    public function resolve($fieldValue = null);
}
