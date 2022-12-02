<?php

namespace DigitalMarketingFramework\Core\ConfigurationResolver\ContentResolver;

use DigitalMarketingFramework\Core\Model\Data\Value\IntegerValue;
use DigitalMarketingFramework\Core\Model\Data\Value\ValueInterface;

class IntegerContentResolver extends ContentResolver
{
    public function build(): string|ValueInterface|null
    {
        return new IntegerValue((int)$this->configuration);
    }
}
