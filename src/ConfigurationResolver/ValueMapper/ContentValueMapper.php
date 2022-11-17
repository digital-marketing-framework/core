<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ValueMapper;

use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class ContentValueMapper extends ValueMapper
{
    public function resolve(string|ValueInterface|null $fieldValue = null): string|ValueInterface|null
    {
        return $this->resolveContent($this->configuration);
    }
}
